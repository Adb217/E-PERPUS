<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->with('roles:id,name')
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('name', 'ilike', "%{$request->search}%")
                        ->orWhere('email', 'ilike', "%{$request->search}%");
                });
            })
            ->when($request->role, function ($q) use ($request) {
                $q->whereHas('roles', fn ($r) => $r->where('name', $request->role));
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,user',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('users.index')->with('success', 'Akun berhasil dibuat.');
    }

    public function updateRole(Request $request, User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['role' => 'Tidak bisa mengubah role akun sendiri.']);
        }

        $request->validate(['role' => 'required|in:admin,user']);

        $user->syncRoles([$request->role]);

        return back()->with('success', 'Role berhasil diperbarui.');
    }

    public function resetPassword(User $user)
    {
        $newPassword = 'kosgoro' . rand(1000, 9999);
        $user->update(['password' => Hash::make($newPassword)]);

        return back()->with('success', "Password direset. Password baru: {$newPassword}");
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['user' => 'Tidak bisa menghapus akun sendiri.']);
        }

        if ($user->hasRole('superadmin')) {
            return back()->withErrors(['user' => 'Tidak bisa menghapus akun superadmin.']);
        }

        $user->delete();

        return back()->with('success', 'Akun berhasil dihapus.');
    }
}