<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\muser;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('asset.index');
        }

        return view('auth.login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        $request->validate([
            'cemail'    => 'required|email',
            'cpassword' => 'required',
        ]);

        $user = muser::where('cemail', $request->cemail)->first();

        if ($user && Hash::check($request->cpassword, $user->cpassword)) {

            Auth::login($user);

            // hanya admin & super admin
            if ($user->isAdmin() || $user->isSuperAdmin()) {
                return redirect()->route('asset.index');
            }

            Auth::logout();
            return back()->withErrors([
                'cemail' => 'Akses hanya untuk Admin / Super Admin'
            ]);
        }

        return back()->withErrors([
            'cemail' => 'Email atau password salah'
        ]);
    }

    /**
     * Logout
     */
    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
