<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\TemporarySale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TemporarySaleController extends Controller
{
    public function index()
    {
        $temporarySales = TemporarySale::with(['customer', 'items'])
            ->latest()
            ->get();

        return view('credits.temporary-sales.index', compact('temporarySales'));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('credits.temporary-sales.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'sale_date' => ['required', 'date'],
        ]);

        $temporarySale = TemporarySale::create([
            'customer_id' => $data['customer_id'],
            'sale_date' => $data['sale_date'],
            'total_amount' => 0,
        ]);

        return redirect()
            ->route('credits.temporary-sales.edit', $temporarySale->id)
            ->with('success', 'Temporary bill created. You can now add products.');
    }

    public function edit($id)
    {
        $temporarySale = TemporarySale::with([
            'customer',
            'items',
        ])->findOrFail($id);

        return view(
            'credits.temporary-sales.edit',
            compact('temporarySale')
        );
    }

    public function addItem(Request $request, $id)
    {
        $data = $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'unit' => ['required', 'string', 'max:30'],
            'unit_price' => ['required', 'numeric', 'gte:0'],
        ]);

        DB::transaction(function () use ($data, $id) {
            $temporarySale = TemporarySale::lockForUpdate()
                ->findOrFail($id);

            $quantity = (float) $data['quantity'];
            $price = (float) $data['unit_price'];

            $temporarySale->items()->create([
                'product_name' => $data['product_name'],
                'quantity' => $quantity,
                'unit' => $data['unit'],
                'unit_price' => $price,
                'line_total' => round($quantity * $price, 2),
            ]);

            $temporarySale->update([
                'total_amount' => round(
                    $temporarySale->items()->sum('line_total'),
                    2
                ),
            ]);
        });

        return back()->with('success', 'Product added to temporary bill.');
    }

    public function updateItem(Request $request, $id, $itemId)
    {
        $data = $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'unit' => ['required', 'string', 'max:30'],
            'unit_price' => ['required', 'numeric', 'gte:0'],
        ]);

        DB::transaction(function () use ($data, $id, $itemId) {
            $temporarySale = TemporarySale::lockForUpdate()
                ->findOrFail($id);

            $item = $temporarySale->items()->findOrFail($itemId);

            $quantity = (float) $data['quantity'];
            $price = (float) $data['unit_price'];

            $item->update([
                'product_name' => $data['product_name'],
                'quantity' => $quantity,
                'unit' => $data['unit'],
                'unit_price' => $price,
                'line_total' => round($quantity * $price, 2),
            ]);

            $temporarySale->update([
                'total_amount' => round(
                    $temporarySale->items()->sum('line_total'),
                    2
                ),
            ]);
        });

        return back()->with('success', 'Product updated.');
    }

    public function deleteItem($id, $itemId)
    {
        DB::transaction(function () use ($id, $itemId) {
            $temporarySale = TemporarySale::lockForUpdate()
                ->findOrFail($id);

            $temporarySale->items()->findOrFail($itemId)->delete();

            $temporarySale->update([
                'total_amount' => round(
                    $temporarySale->items()->sum('line_total'),
                    2
                ),
            ]);
        });

        return back()->with('success', 'Product removed from temporary bill.');
    }

    public function destroy($id)
    {
        $temporarySale = TemporarySale::findOrFail($id);
        $temporarySale->delete();

        return redirect()
            ->route('credits.temporary-sales.index')
            ->with('success', 'Temporary bill deleted.');
    }
}