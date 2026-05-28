<?php

namespace App\Http\Controllers;

use App\Models\Chair;
use App\Models\Staff;
use App\Models\User;
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

            return redirect()->route('user-home')->with('toast_success', 'Login Berhasil!');
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        // User Login
        $user = User::where('email', $request->email)->first();

        if ($user) {

            if (! Auth::guard('web')->attempt($credentials)) {
                return back()->withErrors(['email' => 'Email atau password salah']);
            }

            $request->session()->regenerate();

            return redirect()->route('dashboard')->with('toast_success', 'Login Berhasil!');
        }

        // Staff Login
        $staff = Staff::where('email', $request->email)->first();

        if ($staff) {

            if (! Auth::guard('staff')->attempt($credentials)) {
                return back()->withErrors(['email' => 'Email atau password salah']);
            }

            $request->session()->regenerate();

            return redirect()->route('dashboard')->with('toast_success', 'Login Berhasil!');
        }

        return back()->withErrors(['email' => 'Akun tidak ditemukan']);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        Auth::guard('staff')->logout();

        Auth::guard('chair')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('toast_success', 'Logged Out Successful!');
    }
}
