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

    //Mobile API Login
    public function apiLogin(Request $request)
    {
        // VALIDASI
        $request->validate([
            'cemail'    => 'required|email',
            'cpassword' => 'required',
        ]);

        // AMBIL USER
        $user = muser::where('cemail', $request->cemail)->first();

        // CEK LOGIN
        if (!$user || !Hash::check($request->cpassword, $user->cpassword)) {
            return response()->json([
                "success" => false,
                "message" => "Email atau password salah"
            ], 401);
        }

        // 🔒 OPTIONAL: filter hanya admin & super admin
        if (!$user->isAdmin() && !$user->isSuperAdmin()) {
            return response()->json([
                "success" => false,
                "message" => "Akses hanya untuk Admin"
            ], 403);
        }

        // 🔥 RESPONSE API (TANPA SESSION)
        return response()->json([
            "success" => true,
            "message" => "Login berhasil",
            "user" => [
                "id"    => (int)$user->nid,
                "name"  => $user->cname,
                "email" => $user->cemail,

                "role" => [
                    "fadmin" => (int)$user->fadmin,
                    "fsuper" => (int)$user->fsuper,
                    "fhrd"   => (int)$user->fhrd
                ],

                "face" => [
                    "approved" => (int)$user->fface_approved
                ]
            ]
        ]);
    }
}
