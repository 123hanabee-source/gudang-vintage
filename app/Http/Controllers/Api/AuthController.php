<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * POST /api/auth/login
     *
     * Checks the `users` table first (admins), then `customers`.
     * The login form is identical for both — the role is resolved
     * server-side so the client never needs to choose.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $email    = $request->input('email');
        $password = $request->input('password');

        // ── 1. Check admin users ──────────────────────────────
        $user = User::where('email', $email)->first();

        if ($user) {
            if (! Hash::check($password, $user->password)) {
                return response()->json(['message' => 'Email atau password salah.'], 401);
            }
            if ($user->status !== 'Aktif') {
                return response()->json(['message' => 'Akun Anda tidak aktif. Hubungi administrator.'], 403);
            }

            return response()->json([
                'role' => 'admin',
                'user' => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => $user->role,
                ],
            ]);
        }

        // ── 2. Check customers ────────────────────────────────
        $customer = Customer::where('email', $email)->first();

        if ($customer) {
            if (! $customer->password || ! Hash::check($password, $customer->password)) {
                return response()->json(['message' => 'Email atau password salah.'], 401);
            }

            return response()->json([
                'role' => 'customer',
                'user' => [
                    'id'      => $customer->id,
                    'name'    => $customer->name,
                    'email'   => $customer->email,
                    'phone'   => $customer->phone,
                    'address' => $customer->address,
                ],
            ]);
        }

        // ── 3. Not found in either table ──────────────────────
        return response()->json(['message' => 'Email atau password salah.'], 401);
    }

    /**
     * POST /api/auth/logout
     * Session is managed client-side (sessionStorage), so this is a
     * no-op on the server for now. Include it so the frontend can call
     * a logout endpoint consistently.
     */
    public function logout()
    {
        return response()->json(['message' => 'Berhasil keluar.']);
    }

    /**
     * GET /api/auth/me
     * Returns the currently logged-in user's data from session (frontend
     * sends their user object; this just validates it's still in the DB).
     */
    public function me(Request $request)
    {
        $email = $request->query('email');
        if (! $email) {
            return response()->json(['message' => 'Email wajib diisi.'], 400);
        }

        $user = User::where('email', $email)->first();
        if ($user) {
            return response()->json(['role' => 'admin', 'user' => $user->only(['id','name','email','role'])]);
        }

        $customer = Customer::where('email', $email)->first();
        if ($customer) {
            return response()->json(['role' => 'customer', 'user' => $customer->only(['id','name','email','phone','address'])]);
        }

        return response()->json(['message' => 'Pengguna tidak ditemukan.'], 404);
    }
}
