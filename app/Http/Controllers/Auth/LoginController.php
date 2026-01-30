<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     * User bisa login dengan: WhatsApp number, username, atau member number
     */
    public function login(Request $request)
    {
        $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $identifier = $request->identifier;
        $password = $request->password;

        // Cari user berdasarkan identifier (bisa WA number, username, atau member number)
        $user = User::where(function($query) use ($identifier) {
            $query->where('whatsapp_number', $identifier)
                  ->orWhere('username', $identifier)
                  ->orWhere('member_number', $identifier);
        })->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'identifier' => ['Nomor WA/Username/Member atau password salah.'],
            ]);
        }

        // Login user
        Auth::login($user, $request->filled('remember'));

        $request->session()->regenerate();

        // Redirect based on role
        if ($user->isAdmin() || $user->isOwner()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('customer.dashboard'));
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
