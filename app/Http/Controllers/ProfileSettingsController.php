<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileSettingsController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();

        return view('profile.settings', [
            'user' => $user,
            'pageTitle' => 'Pengaturan Profil',
            'announcementBanner' => null,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nim' => ['required', 'string', 'max:50'],
            'kelas' => ['required', 'string', 'max:50'],
            'no_hp' => ['nullable', 'string', 'max:50'],
        ]);

        $request->user()->update($validated);

        return redirect()
            ->route('dashboard.profile')
            ->with('status', 'profile-updated');
    }
}
