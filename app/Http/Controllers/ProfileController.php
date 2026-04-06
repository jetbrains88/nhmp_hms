<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show user profile
     */
    public function show()
    {
        $user = auth()->user()->load(['roles', 'branches']);

        return view('profile.show', compact('user'));
    }

    /**
     * Show profile edit form
     */
    public function edit()
    {
        $user = auth()->user();

        return view('profile.edit', compact('user'));
    }

    /**
     * Update profile
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully');
    }

    /**
     * Show change password form
     */
    public function changePasswordForm()
    {
        return view('profile.change-password');
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = auth()->user();
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Password changed successfully');
    }

    /**
     * Show settings page
     */
    public function settings()
    {
        $user = auth()->user();
        $preferences = json_decode($user->preferences ?? '{}', true);

        return view('profile.settings', compact('user', 'preferences'));
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'theme' => 'in:vibrant,nhmp,clinical,green,minimal,warm',
            'notifications_email' => 'boolean',
            'notifications_browser' => 'boolean',
        ]);

        $user = auth()->user();
        $preferences = array_merge(
            json_decode($user->preferences ?? '{}', true),
            $request->only(['theme', 'notifications_email', 'notifications_browser'])
        );

        $user->update(['preferences' => json_encode($preferences)]);

        return redirect()->back()->with('success', 'Settings updated');
    }

    /**
     * Show activity log for user
     */
    public function activity()
    {
        $user = auth()->user();
        $logs = $user->auditLogs()
            ->with(['branch'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('profile.activity', compact('user', 'logs'));
    }
}
