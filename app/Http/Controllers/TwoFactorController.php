<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TwoFactorController extends Controller
{
    public function show()
    {
        return view('auth.setup-2fa', ['user' => $this->currentUser()]);
    }

    public function toggle(Request $request)
    {
        $user    = $this->currentUser();
        $enabled = !$user->two_factor_enabled;

        if ($enabled && !$user->phone) {
            return back()->with('error', 'A verified phone number is required to enable two-factor authentication.');
        }

        if (!$enabled) {
            $request->validate(['current_password' => 'required']);
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
        }

        $user->two_factor_enabled = $enabled;
        $user->save();

        return back()->with('success', $enabled
            ? 'Two-factor authentication enabled. You will receive an SMS code on each login.'
            : 'Two-factor authentication disabled.');
    }
}
