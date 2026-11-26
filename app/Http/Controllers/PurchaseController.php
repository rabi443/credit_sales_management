<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PurchaseController extends Controller
{
    // ==========================================
    // SUPPORTED PURCHASE UNITS
    // ==========================================

    private const PURCHASE_UNITS = [
        'pcs',
        'carton',
        'box',
        'pack',
        'jar',
        'set',
        'mala',
        'poka',
    ];


    // ==========================================
    // SHOW PURCHASE HISTORY
    // ==========================================

    public function index(Request $request)
    {
        $query = Purchase::with('items')
            ->latest('purchase_date');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('supplier_name', 'like', "%{$search}%")
                    ->orWhere('invoice_no', 'like', "%{$search}%");
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('purchase_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('purchase_date', '<=', $request->to);
        }

        $purchases = $query->paginate(15)->withQueryString();

        return view('credits.purchases.index', compact('purchases'));
    }


    // ==========================================
    // CREATE PURCHASE PAGE
    // ==========================================

    public function create()
    {
        $draft = session()->get('purchase_draft', [
            'supplier_name' => '',
            'invoice_no' => '',
            'purchase_date' => now()->format('Y-m-d'),
            'notes' => '',
            'items' => [],
        ]);

        return view('credits.purchases.create', compact('draft'));
    }


    // ==========================================
    // ADD MULTIPLE ITEMS TO TEMPORARY LIST
    // ==========================================

    public function addItem(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => [
                'required',
                'string',
                'max:255',
            ],

            'purchase_date' => [
                'required',
                'date',
            ],

            'invoice_no' => [
                'nullable',
                'string',
                'max:255',
            ],

            'notes' => [
                'nullable',
                'string',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.product_name' => [
                'required',
                'string',
                'max:255',
            ],

            'items.*.quantity' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'items.*.unit' => [
                'required',
                'in:' . implode(',', self::PURCHASE_UNITS),
            ],

            'items.*.pieces_per_pack' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'items.*.unit_price' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        $draft = session()->get('purchase_draft', [
            'supplier_name' => '',
            'purchase_date' => now()->format('Y-m-d'),
            'invoice_no' => '',
            'notes' => '',
            'items' => [],
        ]);

        // Save supplier information
        $draft['supplier_name'] = $validated['supplier_name'];
        $draft['purchase_date'] = $validated['purchase_date'];
        $draft['invoice_no'] = $validated['invoice_no'] ?? '';
        $draft['notes'] = $validated['notes'] ?? '';

        // Add all submitted product rows
        foreach ($validated['items'] as $item) {

            $unit = strtolower(trim($item['unit']));

            // PCS always means one individual piece
            $piecesPerPack = $unit === 'pcs'
                ? 1
                : (float) $item['pieces_per_pack'];

            $quantity = (float) $item['quantity'];
            $unitPrice = (float) $item['unit_price'];

            // Convert purchase quantity into individual PCS
            $stockQuantity = round(
                $quantity * $piecesPerPack,
                2
            );

            // Calculate purchase amount
            $totalPrice = round(
                $quantity * $unitPrice,
                2
            );

            $draft['items'][] = [
                'id' => (string) Str::uuid(),

                'product_name' => trim($item['product_name']),

                'quantity' => $quantity,

                'unit' => $unit,

                'pieces_per_pack' => $piecesPerPack,

                'stock_quantity' => $stockQuantity,

                'unit_price' => $unitPrice,

                'total_price' => $totalPrice,
            ];
        }

        session()->put('purchase_draft', $draft);

        return redirect()
            ->route('credits.purchases.create')
            ->with(
                'success',
                count($validated['items']) . ' product(s) added to temporary purchase list.'
            );
    }


    // ==========================================
    // UPDATE TEMPORARY ITEM
    // ==========================================

    public function updateItem(Request $request, $id)
    {
        $validated = $request->validate([
            'product_name' => [
                'required',
                'string',
                'max:255',
            ],

            'quantity' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'unit' => [
                'required',
                'in:' . implode(',', self::PURCHASE_UNITS),
            ],

            'pieces_per_pack' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'unit_price' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        $draft = session()->get('purchase_draft');

        if (!$draft) {
            return redirect()
                ->route('credits.purchases.create')
                ->with('error', 'Temporary purchase list not found.');
        }

        $found = false;

        foreach ($draft['items'] as &$item) {

            if ($item['id'] === $id) {

                $quantity = (float) $validated['quantity'];

                $unitPrice = (float) $validated['unit_price'];

                $unit = strtolower(trim($validated['unit']));

                $piecesPerPack = $unit === 'pcs'
                    ? 1
                    : (float) $validated['pieces_per_pack'];

                $stockQuantity = round(
                    $quantity * $piecesPerPack,
                    2
                );

                $totalPrice = round(
                    $quantity * $unitPrice,
                    2
                );

                $item['product_name'] = trim(
                    $validated['product_name']
                );

                $item['quantity'] = $quantity;

                $item['unit'] = $unit;

                $item['pieces_per_pack'] = $piecesPerPack;

                $item['stock_quantity'] = $stockQuantity;

                $item['unit_price'] = $unitPrice;

                $item['total_price'] = $totalPrice;

                $found = true;

                break;
            }
        }

        unset($item);

        if (!$found) {
            return redirect()
                ->route('credits.purchases.create')
                ->with('error', 'Purchase item not found.');
        }

        session()->put('purchase_draft', $draft);

        return redirect()
            ->route('credits.purchases.create')
            ->with('success', 'Temporary item updated.');
    }


    // ==========================================
    // REMOVE ITEM FROM TEMPORARY LIST
    // ==========================================

    public function removeItem($id)
    {
        $draft = session()->get('purchase_draft');

        if (!$draft) {
            return redirect()
                ->route('credits.purchases.create')
                ->with('error', 'Temporary purchase list not found.');
        }

        $draft['items'] = array_values(array_filter(
            $draft['items'],
            fn ($item) => $item['id'] !== $id
        ));

        session()->put('purchase_draft', $draft);

        return redirect()
            ->route('credits.purchases.create')
            ->with('success', 'Item removed from temporary list.');
    }


    // ==========================================
    // CLEAR TEMPORARY PURCHASE LIST
    // ==========================================

    public function clearDraft()
    {
        session()->forget('purchase_draft');

        return redirect()
            ->route('credits.purchases.create')
            ->with('success', 'Temporary purchase list cleared.');
    }


    // ==========================================
    // CONFIRM PURCHASE AND UPDATE STOCK
    // ==========================================

    public function store(Request $request)
    {
        $draft = session()->get('purchase_draft');

        if (!$draft || empty($draft['items'])) {
            return redirect()
                ->route('credits.purchases.create')
                ->with('error', 'Please add at least one item before confirming.');
        }

        $request->validate([
            'bill_images' => [
                'nullable',
                'array',
            ],

            'bill_images.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        $uploadedPaths = [];

        try {

            $purchase = DB::transaction(function () use (
                $draft,
                $request,
                &$uploadedPaths
            ) {

                // Calculate total purchase amount
                $totalAmount = collect($draft['items'])
                    ->sum('total_price');

                // Create purchase record
                $purchase = Purchase::create([
                    'supplier_name' => $draft['supplier_name'],

                    'invoice_no' => !empty($draft['invoice_no'])
                        ? $draft['invoice_no']
                        : null,

                    'purchase_date' => $draft['purchase_date'],

                    'total_amount' => round($totalAmount, 2),

                    'notes' => !empty($draft['notes'])
                        ? $draft['notes']
                        : null,
                ]);


                // Process each purchased product
                foreach ($draft['items'] as $item) {

                    $productName = trim($item['product_name']);

                    $unit = strtolower(trim($item['unit']));

                    // Validate stored draft unit too
                    if (!in_array($unit, self::PURCHASE_UNITS, true)) {
                        throw new \RuntimeException(
                            'Invalid purchase unit: ' . $unit
                        );
                    }

                    // PCS means one piece; all other units use conversion
                    $piecesPerPack = $unit === 'pcs'
                        ? 1
                        : (float) ($item['pieces_per_pack'] ?? 1);

                    if ($piecesPerPack <= 0) {
                        throw new \RuntimeException(
                            'Invalid pieces per unit for ' . $productName
                        );
                    }

                    $quantity = (float) $item['quantity'];

                    $unitPrice = (float) $item['unit_price'];

                    // Convert purchase quantity into individual PCS
                    $stockQuantity = round(
                        $quantity * $piecesPerPack,
                        2
                    );

                    // Calculate price of one individual PCS
                    $pricePerPiece = $unitPrice / $piecesPerPack;

                    // Find existing product without case sensitivity
                    $product = Product::whereRaw(
                        'LOWER(name) = ?',
                        [mb_strtolower($productName)]
                    )
                        ->lockForUpdate()
                        ->first();

                    // All product stock is maintained in PCS
                    $productUnit = 'pcs';

                    // Create product if it does not exist
                    if (!$product) {

                        $product = Product::create([
                            'name' => $productName,

                            'unit' => $productUnit,

                            'purchase_price' => round($pricePerPiece, 2),

                            'selling_price' => 0,

                            'stock' => 0,

                            'low_stock' => 0,
                        ]);
                    }

                    // Increase product stock
                    $product->increment(
                        'stock',
                        $stockQuantity
                    );

                    // Update latest purchase price per PCS
                    $product->update([
                        'purchase_price' => round($pricePerPiece, 2),

                        'unit' => 'pcs',
                    ]);

                    // Save original purchase information
                    $purchase->items()->create([

                        'product_id' => $product->id,

                        'product_name' => $productName,

                        'quantity' => $quantity,

                        'unit' => $unit,

                        'pieces_per_pack' => $piecesPerPack,

                        'stock_quantity' => $stockQuantity,

                        'unit_price' => $unitPrice,

                        'total_price' => round(
                            $quantity * $unitPrice,
                            2
                        ),
                    ]);
                }


                // Upload multiple purchase bill images
                if ($request->hasFile('bill_images')) {

                    foreach ($request->file('bill_images') as $image) {

                        $path = $image->store(
                            'purchase-bills',
                            'public'
                        );

                        $uploadedPaths[] = $path;

                        $purchase->images()->create([
                            'image_path' => $path,
                        ]);
                    }
                }

                return $purchase;
            });


            // Clear temporary list after successful confirmation
            session()->forget('purchase_draft');

            return redirect()
                ->route('credits.purchases.show', $purchase->id)
                ->with(
                    'success',
                    'Purchase confirmed and stock updated successfully.'
                );

        } catch (\Throwable $e) {

            // Delete uploaded images if saving fails
            foreach ($uploadedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            report($e);

            return redirect()
                ->route('credits.purchases.create')
                ->with(
                    'error',
                    'Purchase could not be saved. Please try again.'
                );
        }
    }


    // ==========================================
    // SHOW PURCHASE DETAILS
    // ==========================================

    public function show($id)
    {
        $purchase = Purchase::with([
            'items.product',
            'images',
        ])->findOrFail($id);

        return view(
            'credits.purchases.show',
            compact('purchase')
        );
    }
}
