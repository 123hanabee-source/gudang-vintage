<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // GET /api/admin/users?search=&role=&page=
    public function index(Request $request)
    {
        $query = User::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name',  'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        // Never expose password hashes
        return $query->latest()
            ->paginate(20, ['id','name','email','role','status','created_at'])
            ->withQueryString();
    }

    // GET /api/admin/users/{user}
    public function show(User $user)
    {
        return $user->only(['id','name','email','role','status','created_at']);
    }

    // POST /api/admin/users
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                  => 'required|string|max:100',
            'email'                 => 'required|email|unique:users,email',
            'role'                  => 'required|in:Admin,Staff,Kasir,Gudang',
            'status'                => 'nullable|in:Aktif,Nonaktif',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'role'     => $data['role'],
            'status'   => $data['status'] ?? 'Aktif',
            'password' => Hash::make($data['password']),
        ]);

        return response()->json($user->only(['id','name','email','role','status','created_at']), 201);
    }

    // PUT /api/admin/users/{user}
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => 'sometimes|required|string|max:100',
            'email'    => 'sometimes|required|email|unique:users,email,' . $user->id,
            'role'     => 'sometimes|required|in:Admin,Staff,Kasir,Gudang',
            'status'   => 'nullable|in:Aktif,Nonaktif',
            'password' => 'nullable|string|min:8',
        ]);

        // Guard: don't demote the only Admin
        if (isset($data['role']) && $data['role'] !== 'Admin' && $user->role === 'Admin') {
            $adminCount = User::where('role', 'Admin')->count();
            if ($adminCount <= 1) {
                return response()->json([
                    'message' => 'Tidak dapat mengubah role — harus ada minimal 1 Admin aktif.',
                ], 422);
            }
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return $user->fresh()->only(['id','name','email','role','status','created_at']);
    }

    // DELETE /api/admin/users/{user}
    public function destroy(User $user)
    {
        // Guard: cannot delete last Admin
        if ($user->role === 'Admin') {
            $adminCount = User::where('role', 'Admin')->count();
            if ($adminCount <= 1) {
                return response()->json([
                    'message' => 'Tidak dapat menghapus satu-satunya Admin.',
                ], 422);
            }
        }

        $user->delete();

        return response()->json(null, 204);
    }

    // PATCH /api/admin/users/{user}/toggle-status
    public function toggleStatus(User $user)
    {
        $user->update([
            'status' => $user->status === 'Aktif' ? 'Nonaktif' : 'Aktif',
        ]);

        return $user->fresh()->only(['id','name','email','role','status','created_at']);
    }
}
