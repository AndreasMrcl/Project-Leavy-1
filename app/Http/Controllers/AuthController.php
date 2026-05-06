<?php

namespace App\Http\Controllers;

use App\Models\Chair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function signin(Request $request)
    {
        if ($request->filled('qrToken')) {
            $chair = Chair::where('qr_token', $request->input('qrToken'))->first();

            if (! $chair) {
                return redirect()->route('login')
                    ->withErrors(['qrToken' => 'QR code tidak valid. Silakan minta QR baru ke kasir.']);
            }

            Auth::guard('chair')->login($chair);
            $request->session()->regenerate();
            $request->session()->put('session_started_at', now());

            return redirect()->route('user-home');
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($request->only('email', 'password'))) {
            return redirect()->route('login')
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $request->session()->regenerate();

        if (Auth::user() instanceof Chair) {
            return redirect()->route('user-home');
        }

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        Auth::guard('chair')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('toast_success', 'Logged Out Successful!');
    }
}
