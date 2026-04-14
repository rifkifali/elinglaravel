<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }
        return view('auth.login');
    }

    /**
     * Proses login dengan NIP dan Password
     */
    public function login(Request $request)
    {
        $request->validate([
            'nip'      => 'required',
            'password' => 'required',
        ]);

        // Coba autentikasi dengan field 'nip' sebagai username
        $credentials = [
            'nip'      => $request->nip,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return $this->redirectByRole(Auth::user()->role);
        }

        throw ValidationException::withMessages([
            'nip' => 'NIP atau Password salah! Silakan periksa kembali.',
        ]);
    }

    /**
     * Logout pengguna
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    /**
     * Redirect berdasarkan role
     */
    private function redirectByRole(string $role)
    {
        return match($role) {
            'Admin'       => redirect()->route('admin.index'),
            'Perancang'   => redirect()->route('perancang.index'),
            'Kasubbag'    => redirect()->route('kasubbag.index'),
            'Kabag'       => redirect()->route('kabag.index'),
            'Super Admin' => redirect()->route('superadmin.index'),
            default       => redirect()->route('login')->withErrors(['nip' => 'Role tidak dikenali.']),
        };
    }
}