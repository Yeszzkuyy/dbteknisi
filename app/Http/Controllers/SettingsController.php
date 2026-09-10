<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Display the application preference form.
     */
    public function edit(Request $request): View
    {
        return view('settings.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Persist the user's application preferences.
     */
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'theme' => ['required', 'in:light,dark,system'],
            'locale' => ['required', 'in:id,en'],
            'notify_email' => ['sometimes', 'boolean'],
            'notify_system' => ['sometimes', 'boolean'],
        ]);

        $user = $request->user();
        $user->preferences = array_merge($user->preferences ?? [], [
            'theme' => $data['theme'],
            'locale' => $data['locale'],
            'notify_email' => $request->boolean('notify_email'),
            'notify_system' => $request->boolean('notify_system'),
        ]);
        $user->save();

        return Redirect::route('settings.edit')->with('status', 'settings-updated');
    }

    /**
     * Password-gated area: change password and delete account.
     */
    public function advanced(Request $request): View
    {
        return view('settings.advanced', [
            'user' => $request->user(),
        ]);
    }
}
