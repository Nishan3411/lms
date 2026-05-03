<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreManagedUserRequest;
use App\Http\Requests\Admin\UpdateManagedUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->withCount(['enrolledClasses', 'children', 'parents', 'teachingClasses'])
            ->orderByRaw("
                CASE role
                    WHEN 'admin' THEN 1
                    WHEN 'teacher' THEN 2
                    WHEN 'student' THEN 3
                    WHEN 'parent' THEN 4
                    ELSE 5
                END
            ")
            ->orderBy('name')
            ->get()
            ->groupBy('role');

        $stats = [
            'admins' => User::where('role', 'admin')->count(),
            'teachers' => User::where('role', 'teacher')->count(),
            'students' => User::where('role', 'student')->count(),
            'parents' => User::where('role', 'parent')->count(),
        ];

        return view('admin.users', [
            'usersByRole' => $users,
            'stats' => $stats,
        ]);
    }

    public function store(StoreManagedUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()
            ->route('admin.users')
            ->with('success', ucfirst($validated['role']).' created successfully.');
    }

    public function update(UpdateManagedUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('admin.users')
            ->with('success', "{$user->name} updated successfully.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return redirect()
                ->route('admin.users')
                ->with('error', 'At least one admin account must remain in the system.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('admin.users')
            ->with('success', "{$name} deleted successfully.");
    }
}
