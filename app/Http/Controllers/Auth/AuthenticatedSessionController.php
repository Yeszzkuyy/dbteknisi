<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): View
    {
        return view('auth.login', [
            'kickedAt' => $request->session()->pull('kicked_at'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = $request->user();

        $now = now()->toDateTimeString();
        $currentSessionId = $request->session()->getId();

        $kickedSessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $currentSessionId)
            ->get();

        foreach ($kickedSessions as $oldSession) {
            $data = json_decode(base64_decode($oldSession->payload), true) ?: [];

            foreach (array_keys($data) as $key) {
                if (str_starts_with($key, 'login_')) {
                    unset($data[$key]);
                }
            }

            $data['kicked_at'] = $now;

            DB::table('sessions')->where('id', $oldSession->id)->update([
                'user_id' => null,
                'payload' => base64_encode(json_encode($data)),
            ]);
        }

        $user->forceFill(['remember_token' => null])->save();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
