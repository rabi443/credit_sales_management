<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerAccountController extends Controller
{
    public function edit(Customer $customer)
    {
        return view('admin.customers.account', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Customer model uses Laravel's hashed password cast.
        $customer->password = $data['password'];
        $customer->save();

        return redirect()
            ->route('credits.customers.show', $customer)
            ->with('success', 'Customer login account created/updated successfully.');
    }
}
