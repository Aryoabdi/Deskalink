<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by role
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search by name, email, or username
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'full_name' => ['required', 'string', 'max:100'],
            'phone_number' => ['required', 'string', 'max:20'],
            'role' => ['required', 'in:client,partner,admin'],
            'status' => ['required', 'in:active,suspended,banned'],
        ]);

        $user = User::create([
            'user_id' => 'user' . str_pad(User::count() + 1, 8, '0', STR_PAD_LEFT),
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'full_name' => $request->full_name,
            'phone_number' => $request->phone_number,
            'role' => $request->role,
            'status' => $request->status,
            'is_profile_completed' => true,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:users,username,'.$user->user_id.',user_id'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email,'.$user->user_id.',user_id'],
            'full_name' => ['required', 'string', 'max:100'],
            'phone_number' => ['required', 'string', 'max:20'],
            'role' => ['required', 'in:client,partner,admin'],
            'status' => ['required', 'in:active,suspended,banned'],
        ]);

        $user->update($request->only([
            'username',
            'email',
            'full_name',
            'phone_number',
            'role',
            'status',
            'bio'
        ]));

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Update user status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => ['required', 'in:active,suspended,banned'],
            'reason' => ['required_if:status,suspended,banned', 'string', 'max:255'],
        ]);

        $user->update([
            'status' => $request->status
        ]);

        return redirect()->back()
            ->with('success', 'User status updated successfully.');
    }

    /**
     * Handle user reporting.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function report(Request $request, User $user)
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ]);

        Report::create([
            'reported_by' => auth()->user()->user_id,
            'reported_user' => $user->user_id,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        return redirect()->back()
            ->with('success', 'User reported successfully. Our team will review the report.');
    }
}
