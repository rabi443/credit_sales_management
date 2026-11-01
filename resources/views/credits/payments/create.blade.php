@extends('layouts.credit')
@section('title', 'Receive Payment')
@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('credits.customers.show', $customer) }}" class="text-sm font-medium text-blue-700">← Back to {{ $customer->name }}</a>
    <div class="mt-4 rounded-xl bg-white p-6 shadow-sm">
        <h1 class="text-xl font-bold">Receive Customer Payment</h1>
        <p class="mt-1 text-sm text-slate-500">Customer: <strong>{{ $customer->name }}</strong></p>
        <div class="mt-4 rounded-lg bg-slate-50 p-4">
            <div class="flex justify-between text-sm"><span>Total credit sales</span><strong>Rs. {{ number_format($customer->total_credit,2) }}</strong></div>
            <div class="mt-2 flex justify-between text-sm"><span>Total payments so far</span><strong>Rs. {{ number_format($customer->total_paid,2) }}</strong></div>
            <div class="mt-2 flex justify-between border-t pt-2"><span class="font-semibold">Current balance</span><strong class="{{ $customer->balance > 0 ? 'text-red-600' : 'text-blue-700' }}">Rs. {{ number_format($customer->balance,2) }}</strong></div>
        </div>
        <form method="POST" action="{{ route('credits.payments.store') }}" class="mt-6 space-y-4">
            @csrf
            <input type="hidden" name="customer_id" value="{{ $customer->id }}">
            <div><label class="mb-1 block text-sm font-medium">Payment Date *</label><input type="date" name="payment_date" value="{{ old('payment_date', now()->toDateString()) }}" required class="w-full rounded-lg border-slate-300"></div>
            <div><label class="mb-1 block text-sm font-medium">Amount (Rs.) *</label><input type="number" name="amount" min="0.01" step="0.01" value="{{ old('amount') }}" required class="w-full rounded-lg border-slate-300 text-lg"></div>
            <div><label class="mb-1 block text-sm font-medium">Payment Method *</label><select name="payment_method" required class="w-full rounded-lg border-slate-300"><option value="cash">Cash</option><option value="esewa">eSewa</option><option value="khalti">Khalti</option><option value="bank">Bank Transfer</option><option value="other">Other</option></select></div>
            <div><label class="mb-1 block text-sm font-medium">Reference number (optional)</label><input name="reference" value="{{ old('reference') }}" class="w-full rounded-lg border-slate-300"></div>
            <div><label class="mb-1 block text-sm font-medium">Notes (optional)</label><textarea name="notes" rows="2" class="w-full rounded-lg border-slate-300">{{ old('notes') }}</textarea></div>
            <p class="rounded-lg bg-blue-50 p-3 text-xs text-blue-800">Payment is applied to the oldest unpaid invoices first. Any extra amount is kept as customer advance and applied to future credit invoices automatically.</p>
            <div class="flex justify-end gap-2"><a href="{{ route('credits.customers.show', $customer) }}" class="rounded-lg border px-4 py-2.5 text-sm">Cancel</a><button class="rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-green-700">Save Payment</button></div>
        </form>
    </div>
</div>
@endsection
