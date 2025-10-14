<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class CompleteProfileController extends Controller
{
    /**
     * Show the profile completion form.
     */
    public function edit(Request $request): View
    {
        return view('profile.complete', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Handle profile completion.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'nim' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'nim')->ignore($user->id),
            ],
            'kelas' => ['required', 'string', 'max:50'],
            'no_hp' => ['nullable', 'string', 'max:50'],
        ]);

        $user->fill($data)->save();

        return redirect()
            ->intended(route('dashboard'))
            ->with('status', 'profile-completed');
    }
}

