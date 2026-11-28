<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CustomerProductController extends Controller
{
    // Display all products
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate(12)->withQueryString();

        return view('customer.products.index', compact('products'));
    }

    // Display product details
    public function show(Product $product)
    {
        return view('customer.products.show', compact('product'));
    }

    // Display shopping cart
    public function cart()
    {
        $cart = session()->get('customer_cart', []);

        $products = Product::whereIn('id', array_keys($cart))->get();

        $subtotal = 0;

        foreach ($products as $product) {
            $quantity = $cart[$product->id]['quantity'] ?? 0;
            $subtotal += $product->selling_price * $quantity;
        }

        return view('customer.cart.index', compact(
            'cart',
            'products',
            'subtotal'
        ));
    }

    // Add product to cart
    public function addToCart(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $quantity = (int) $request->quantity;

        if ($quantity > $product->stock) {
            return back()->with('error', 'Requested quantity is not available in stock.');
        }

        $cart = session()->get('customer_cart', []);

        $newQuantity = ($cart[$product->id]['quantity'] ?? 0) + $quantity;

        if ($newQuantity > $product->stock) {
            return back()->with('error', 'Cart quantity exceeds available stock.');
        }

        $cart[$product->id] = [
            'quantity' => $newQuantity,
        ];

        session()->put('customer_cart', $cart);

        return back()->with('success', 'Product added to cart successfully.');
    }

    // Update cart quantity
    public function updateCart(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $quantity = (int) $request->quantity;

        if ($quantity > $product->stock) {
            return back()->with('error', 'Requested quantity exceeds available stock.');
        }

        $cart = session()->get('customer_cart', []);

        if (!isset($cart[$product->id])) {
            return back()->with('error', 'Product not found in cart.');
        }

        $cart[$product->id]['quantity'] = $quantity;

        session()->put('customer_cart', $cart);

        return back()->with('success', 'Cart updated successfully.');
    }

    // Remove product from cart
    public function removeFromCart(Product $product)
    {
        $cart = session()->get('customer_cart', []);

        unset($cart[$product->id]);

        session()->put('customer_cart', $cart);

        return back()->with('success', 'Product removed from cart.');
    }
}
