<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display products and search results.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Product::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $totalProducts = Product::count();

        $lowStockProducts = Product::whereColumn(
            'stock',
            '<=',
            'low_stock'
        )->count();

        return view('credits.products.index', compact(
            'products',
            'search',
            'totalProducts',
            'lowStockProducts'
        ));
    }


    /**
     * Store a new product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'sku' => [
                'nullable',
                'string',
                'max:255',
                'unique:products,sku',
            ],

            'unit' => [
                'required',
                'in:pcs,carton,box,pack,jar,set,mala,poka',
            ],

            'purchase_price' => ['required', 'numeric', 'gte:0'],
            'selling_price' => ['required', 'numeric', 'gte:0'],
            'stock' => ['required', 'numeric', 'gte:0'],
            'low_stock' => ['required', 'numeric', 'gte:0'],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')
                ->store('products', 'public');
        }

        unset($validated['image']);

        Product::create($validated);

        return redirect()
            ->route('credits.products.index')
            ->with('success', 'Product added successfully.');
    }


    /**
     * Return product details for the View modal.
     */
    public function show(Product $product)
    {
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'unit' => $product->unit,
            'purchase_price' => (float) $product->purchase_price,
            'selling_price' => (float) $product->selling_price,
            'stock' => (float) $product->stock,
            'low_stock' => (float) $product->low_stock,
            'image_url' => $product->image_path
                ? asset('storage/' . $product->image_path)
                : null,
            'created_at' => $product->created_at?->format('Y-m-d H:i'),
            'updated_at' => $product->updated_at?->format('Y-m-d H:i'),
        ]);
    }


    /**
     * Update an existing product.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'sku' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($product->id),
            ],

            'unit' => [
                'required',
                'in:pcs,carton,box,pack,jar,set,mala,poka',
            ],

            'purchase_price' => ['required', 'numeric', 'gte:0'],
            'selling_price' => ['required', 'numeric', 'gte:0'],
            'stock' => ['required', 'numeric', 'gte:0'],
            'low_stock' => ['required', 'numeric', 'gte:0'],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        if ($request->hasFile('image')) {

            // Remove previous image.
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }

            $validated['image_path'] = $request->file('image')
                ->store('products', 'public');
        }

        unset($validated['image']);

        $product->update($validated);

        return redirect()
            ->route('credits.products.index', [
                'search' => $request->input('search'),
                'page' => $request->input('page'),
            ])
            ->with('success', 'Product updated successfully.');
    }


    /**
     * Delete a product.
     */
    public function destroy(Product $product)
    {
        // Protect purchase history from being affected.
        if ($product->purchaseItems()->exists()) {
            return redirect()
                ->route('credits.products.index')
                ->with(
                    'error',
                    'This product is already used in purchase records and cannot be deleted.'
                );
        }

        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return redirect()
            ->route('credits.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
