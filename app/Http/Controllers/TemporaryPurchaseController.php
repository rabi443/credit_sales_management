<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\TemporaryPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TemporaryPurchaseController extends Controller
{
    /**
     * Display purchase page and all temporary bills.
     */
    public function index()
    {
        $products = Product::orderBy('name')->get();

        $temporaryPurchases = TemporaryPurchase::with('items', 'images')
            ->latest()
            ->get();

        return view('credits.purchases.create', compact(
            'products',
            'temporaryPurchases'
        ));
    }


    /**
     * Start a new temporary purchase bill.
     */
    public function startDraft(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => ['required', 'string', 'max:255'],
            'invoice_no' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $purchase = DB::transaction(function () use ($validated) {

            $sequence = DB::table('purchase_sequences')
                ->where('id', 1)
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                throw new \RuntimeException(
                    'Purchase sequence record not found.'
                );
            }

            $nextNumber = $sequence->last_number + 1;

            DB::table('purchase_sequences')
                ->where('id', 1)
                ->update([
                    'last_number' => $nextNumber,
                    'updated_at' => now(),
                ]);

            return TemporaryPurchase::create([
                'purchase_number' => $nextNumber,
                'supplier_name' => $validated['supplier_name'],
                'invoice_no' => $validated['invoice_no'] ?? null,
                'purchase_date' => $validated['purchase_date'],
                'notes' => $validated['notes'] ?? null,
                'total_amount' => 0,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Temporary purchase bill created successfully.',
            'purchase' => [
                'id' => $purchase->id,
                'purchase_number' => $purchase->purchase_number,
                'supplier_name' => $purchase->supplier_name,
                'total_amount' => $purchase->total_amount,
            ],
        ]);
    }


    /**
     * Add a product to a temporary purchase using AJAX.
     */
    public function addItem(Request $request, $purchaseId)
    {
        $validated = $request->validate([
            'product_id' => ['nullable', 'exists:products,id'],
            'product_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'unit' => [
                'required',
                'in:pcs,carton,box,pack,jar,set,mala,poka'
            ],
            'pieces_per_pack' => ['required', 'numeric', 'gt:0'],
            'unit_price' => ['required', 'numeric', 'gte:0'],
        ]);

        $purchase = TemporaryPurchase::findOrFail($purchaseId);

        $product = null;

        if (!empty($validated['product_id'])) {
            $product = Product::findOrFail($validated['product_id']);
        }

        $productName = $product
            ? $product->name
            : $validated['product_name'];

        $quantity = (float) $validated['quantity'];

        $piecesPerPack = $validated['unit'] === 'pcs'
            ? 1
            : (float) $validated['pieces_per_pack'];

        $unitPrice = (float) $validated['unit_price'];

        // Convert cartons, boxes, packs, etc. into individual pieces.
        $stockQuantity = $quantity * $piecesPerPack;

        // Total purchase amount.
        $totalPrice = $quantity * $unitPrice;

        $item = DB::transaction(function () use (
            $purchase,
            $product,
            $productName,
            $validated,
            $quantity,
            $piecesPerPack,
            $stockQuantity,
            $unitPrice,
            $totalPrice
        ) {

            $item = $purchase->items()->create([
                'product_id' => $product?->id,
                'product_name' => $productName,
                'quantity' => $quantity,
                'unit' => $validated['unit'],
                'pieces_per_pack' => $piecesPerPack,
                'stock_quantity' => $stockQuantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
            ]);

            $purchase->total_amount = $purchase->items()
                ->sum('total_price');

            $purchase->save();

            return $item;
        });

        return response()->json([
            'success' => true,
            'message' => 'Product added successfully.',
            'item' => [
                'id' => $item->id,
                'product_name' => $item->product_name,
                'quantity' => (float) $item->quantity,
                'unit' => $item->unit,
                'pieces_per_pack' => (float) $item->pieces_per_pack,
                'stock_quantity' => (float) $item->stock_quantity,
                'unit_price' => (float) $item->unit_price,
                'total_price' => (float) $item->total_price,
            ],
            'total_amount' => (float) $purchase->fresh()->total_amount,
        ]);
    }


    /**
     * Update a temporary purchase item using AJAX.
     */
    public function updateItem(Request $request, $purchaseId, $itemId)
    {
        $validated = $request->validate([
            'product_id' => ['nullable', 'exists:products,id'],
            'product_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'unit' => [
                'required',
                'in:pcs,carton,box,pack,jar,set,mala,poka'
            ],
            'pieces_per_pack' => ['required', 'numeric', 'gt:0'],
            'unit_price' => ['required', 'numeric', 'gte:0'],
        ]);

        $purchase = TemporaryPurchase::findOrFail($purchaseId);

        $item = $purchase->items()->findOrFail($itemId);

        $product = null;

        if (!empty($validated['product_id'])) {
            $product = Product::findOrFail($validated['product_id']);
        }

        $productName = $product
            ? $product->name
            : $validated['product_name'];

        $quantity = (float) $validated['quantity'];

        $piecesPerPack = $validated['unit'] === 'pcs'
            ? 1
            : (float) $validated['pieces_per_pack'];

        $unitPrice = (float) $validated['unit_price'];

        $stockQuantity = $quantity * $piecesPerPack;

        $totalPrice = $quantity * $unitPrice;

        DB::transaction(function () use (
            $item,
            $purchase,
            $product,
            $productName,
            $validated,
            $quantity,
            $piecesPerPack,
            $stockQuantity,
            $unitPrice,
            $totalPrice
        ) {

            $item->update([
                'product_id' => $product?->id,
                'product_name' => $productName,
                'quantity' => $quantity,
                'unit' => $validated['unit'],
                'pieces_per_pack' => $piecesPerPack,
                'stock_quantity' => $stockQuantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
            ]);

            $purchase->total_amount = $purchase->items()
                ->sum('total_price');

            $purchase->save();
        });

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'item' => [
                'id' => $item->id,
                'product_name' => $item->product_name,
                'quantity' => (float) $item->quantity,
                'unit' => $item->unit,
                'pieces_per_pack' => (float) $item->pieces_per_pack,
                'stock_quantity' => (float) $item->stock_quantity,
                'unit_price' => (float) $item->unit_price,
                'total_price' => (float) $item->total_price,
            ],
            'total_amount' => (float) $purchase->fresh()->total_amount,
        ]);
    }


    /**
     * Delete a product from a temporary purchase using AJAX.
     */
    public function deleteItem($purchaseId, $itemId)
    {
        $purchase = TemporaryPurchase::findOrFail($purchaseId);

        $item = $purchase->items()->findOrFail($itemId);

        DB::transaction(function () use ($purchase, $item) {

            $item->delete();

            $purchase->total_amount = $purchase->items()
                ->sum('total_price');

            $purchase->save();
        });

        return response()->json([
            'success' => true,
            'message' => 'Product removed successfully.',
            'total_amount' => (float) $purchase->fresh()->total_amount,
        ]);
    }


    /**
     * Open an existing temporary purchase bill.
     */
    public function openDraft($id)
    {
        $purchase = TemporaryPurchase::with([
            'items',
            'images'
        ])->findOrFail($id);

        $products = Product::orderBy('name')->get();

        $temporaryPurchases = TemporaryPurchase::with('items', 'images')
            ->latest()
            ->get();

        return view('credits.purchases.create', compact(
            'purchase',
            'products',
            'temporaryPurchases'
        ));
    }


    /**
     * Delete an entire temporary purchase bill.
     */
    public function deleteDraft($id)
    {
        $purchase = TemporaryPurchase::with('images')->findOrFail($id);

        $imagePaths = $purchase->images
            ->pluck('image_path')
            ->toArray();

        DB::transaction(function () use ($purchase) {
            $purchase->delete();
        });

        // Delete uploaded bill images from storage.
        foreach ($imagePaths as $path) {
            Storage::disk('public')->delete($path);
        }

        return response()->json([
            'success' => true,
            'message' => 'Temporary purchase bill deleted successfully.',
        ]);
    }


    /**
     * Confirm temporary bill and save it permanently.
     */
    public function confirmPurchase($id)
    {
        $permanentPurchase = DB::transaction(function () use ($id) {

            $temporaryPurchase = TemporaryPurchase::with([
                'items',
                'images'
            ])->lockForUpdate()->findOrFail($id);

            if ($temporaryPurchase->items->isEmpty()) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'items' => 'Please add at least one product before confirming.',
                ]);
            }

            // Create permanent purchase with the same bill number.
            $purchase = \App\Models\Purchase::create([
                'purchase_number' => $temporaryPurchase->purchase_number,
                'supplier_name' => $temporaryPurchase->supplier_name,
                'invoice_no' => $temporaryPurchase->invoice_no,
                'purchase_date' => $temporaryPurchase->purchase_date,
                'total_amount' => $temporaryPurchase->total_amount,
                'notes' => $temporaryPurchase->notes,
            ]);

            foreach ($temporaryPurchase->items as $temporaryItem) {

                $product = null;

                if ($temporaryItem->product_id) {
                    $product = Product::lockForUpdate()
                        ->find($temporaryItem->product_id);
                }

                // If no product was selected, try matching by name.
                if (!$product) {
                    $product = Product::whereRaw(
                        'LOWER(name) = ?',
                        [mb_strtolower($temporaryItem->product_name)]
                    )->lockForUpdate()->first();
                }

                // Create a product if it does not already exist.
                if (!$product) {
                    $product = Product::create([
                        'name' => $temporaryItem->product_name,
                        'unit' => 'pcs',
                        'purchase_price' => 0,
                        'selling_price' => 0,
                        'stock' => 0,
                        'low_stock' => 5,
                    ]);
                }

                // Create permanent purchase item.
                \App\Models\PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'product_name' => $temporaryItem->product_name,
                    'quantity' => $temporaryItem->quantity,
                    'unit' => $temporaryItem->unit,
                    'pieces_per_pack' => $temporaryItem->pieces_per_pack,
                    'stock_quantity' => $temporaryItem->stock_quantity,
                    'unit_price' => $temporaryItem->unit_price,
                    'total_price' => $temporaryItem->total_price,
                ]);

                // Update stock only after permanent confirmation.
                $product->stock = (float) $product->stock
                    + (float) $temporaryItem->stock_quantity;

                // Purchase price per individual piece.
                $product->purchase_price =
                    (float) $temporaryItem->unit_price
                    / (float) $temporaryItem->pieces_per_pack;

                $product->save();
            }

            // Transfer bill images to permanent purchase records.
            foreach ($temporaryPurchase->images as $image) {
                \App\Models\PurchaseImage::create([
                    'purchase_id' => $purchase->id,
                    'image_path' => $image->image_path,
                ]);
            }

            // Delete temporary records.
            // Image files remain because permanent records reference them.
            $temporaryPurchase->delete();

            return $purchase;
        });

        return response()->json([
            'success' => true,
            'message' => 'Purchase confirmed successfully.',
            'purchase_id' => $permanentPurchase->id,
            'purchase_number' => $permanentPurchase->purchase_number,
            'redirect_url' => route(
                'credits.purchases.show',
                $permanentPurchase->id
            ),
        ]);
    }


    /**
     * Upload bill images using AJAX.
     * Used when clicking Save Permanently.
     */
    public function uploadImages(Request $request, $purchaseId)
    {
        $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        $purchase = TemporaryPurchase::findOrFail($purchaseId);

        $uploadedImages = [];

        $uploadedPaths = [];

        try {

            DB::transaction(function () use (
                $request,
                $purchase,
                &$uploadedImages,
                &$uploadedPaths
            ) {

                foreach ($request->file('images') as $file) {

                    $path = $file->store(
                        'purchase-bills',
                        'public'
                    );

                    $uploadedPaths[] = $path;

                    $image = $purchase->images()->create([
                        'image_path' => $path,
                    ]);

                    $uploadedImages[] = [
                        'id' => $image->id,
                        'url' => asset('storage/' . $image->image_path),
                    ];
                }
            });

        } catch (\Throwable $e) {

            foreach ($uploadedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            throw $e;
        }

        return response()->json([
            'success' => true,
            'message' => 'Bill images uploaded successfully.',
            'images' => $uploadedImages,
        ]);
    }


    /**
     * Save temporary bill using normal form submission.
     * Upload selected images only when this button is clicked.
     */
    public function saveDraft(Request $request, $id)
    {
        $request->validate([
            'images' => ['nullable', 'array'],
            'images.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        $purchase = TemporaryPurchase::findOrFail($id);

        $uploadedPaths = [];

        try {

            DB::transaction(function () use (
                $request,
                $purchase,
                &$uploadedPaths
            ) {

                if ($request->hasFile('images')) {

                    foreach ($request->file('images') as $file) {

                        $path = $file->store(
                            'purchase-bills',
                            'public'
                        );

                        $uploadedPaths[] = $path;

                        $purchase->images()->create([
                            'image_path' => $path,
                        ]);
                    }
                }
            });

        } catch (\Throwable $e) {

            // Remove files if database saving fails.
            foreach ($uploadedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            throw $e;
        }

        return redirect()
            ->route('credits.purchases.drafts.open', $purchase->id)
            ->with(
                'success',
                'Temporary purchase bill and bill images saved successfully.'
            );
    }
}
