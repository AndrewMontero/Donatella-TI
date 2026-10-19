<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
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
            'roles' => UserRole::cases(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $previousRole = $user->role;
        $previousActiveState = $user->is_active;

        $rules = [
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];

        if ($user->isNot($request->user())) {
            $rules['role'] = ['required', Rule::enum(UserRole::class)];
            $rules['is_active'] = ['nullable', 'boolean'];
        }

        $validated = $request->validate($rules);

        if ($user->isNot($request->user())) {
            $user->role = $validated['role'];
            $user->is_active = $request->boolean('is_active');
        }

        if (filled($validated['password'] ?? null)) {
            $user->password = $validated['password'];
        }

        $user->save();

        $changes = [];

        if ($previousRole !== $user->role) {
            $changes['role'] = [
                'from' => $previousRole->value,
                'to' => $user->role->value,
            ];
        }

        if ($previousActiveState !== $user->is_active) {
            $changes['is_active'] = [
                'from' => $previousActiveState,
                'to' => $user->is_active,
            ];
        }

        if (filled($validated['password'] ?? null)) {
            $changes['password_changed'] = true;
        }

        AuditLog::record($request->user(), 'user.updated', $user, $changes, $request->ip());

        return redirect()->route('admin.users.index')
            ->with('status', 'Usuario actualizado correctamente.');
    }
}
