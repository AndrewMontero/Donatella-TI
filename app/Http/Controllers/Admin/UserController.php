<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()->orderBy('name')->paginate(15),
        ]);
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'managedUser' => $user,
            'roles' => [UserRole::User, UserRole::Technician],
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $rules = [
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];

        if (! $user->isAdmin()) {
            $rules['role'] = ['required', Rule::enum(UserRole::class), Rule::notIn([UserRole::Admin->value])];
            $rules['is_active'] = ['nullable', 'boolean'];
        }

        $validated = $request->validate($rules);

        if (! $user->isAdmin()) {
            $user->role = $validated['role'];
            $user->is_active = $request->boolean('is_active');
        }

        if (filled($validated['password'] ?? null)) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()->route('admin.users.index')
            ->with('status', 'Usuario actualizado correctamente.');
    }
}
