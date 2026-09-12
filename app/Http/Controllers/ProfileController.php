<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'avatar' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            $user->update(['avatar' => $request->file('avatar')->store('avatars', 'public')]);
        }

        return back()->with('success', 'Profil diperbarui');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', PasswordRule::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        Auth::user()->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', 'Password diubah');
    }
}
