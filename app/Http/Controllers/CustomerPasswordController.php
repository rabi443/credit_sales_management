<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerPasswordController extends Controller
{
    // Display customer profile password section
    public function edit()
    {
        $customer = Auth::guard('customer')->user();

        return view('customer.profile', compact('customer'));
    }

    // Update customer password
    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $customer = Auth::guard('customer')->user();

        // Verify current password
        if (!Hash::check($request->current_password, $customer->password)) {
            return back()->withErrors([
                'current_password' => 'Your current password is incorrect.',
            ]);
        }

        // Save new password
        $customer->password = Hash::make($request->password);
        $customer->save();

        return redirect()
            ->route('customer.profile')
            ->with('success', 'Your password has been changed successfully.');
    }
}