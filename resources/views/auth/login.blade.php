<x-auth-nexa-layout title="Sign in">
    <h2 class="nx-h">Welcome back</h2>
    <p class="nx-sub">Sign in to continue to your workspace.</p>

    <x-auth-session-status
        class="nx-status"
        role="status"
        aria-live="polite"
        :status="session('status')"
    />

    <form
        method="POST"
        action="{{ route('login') }}"
        x-data="{ isSubmitting: false, showPassword: false, capsLock: false }"
        x-on:submit="isSubmitting = true"
        class="nx-form"
        aria-label="Sign in"
    >
        @csrf

        <div class="nx-field">
            <label for="email" class="nx-label">Email address</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="Enter your email"
                required
                autofocus
                autocomplete="username"
                aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                aria-describedby="email-error"
                class="nx-input"
            >
            <div id="email-error" aria-live="polite">
                <x-input-error :messages="$errors->get('email')" class="nx-err" />
            </div>
        </div>

        <div class="nx-field">
            <label for="password" class="nx-label">Password</label>
            <div class="nx-pass-wrap">
                <input
                    id="password"
                    :type="showPassword ? 'text' : 'password'"
                    name="password"
                    placeholder="Enter your password"
                    required
                    autocomplete="current-password"
                    aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                    aria-describedby="password-error caps-lock-warning"
                    x-on:keyup="capsLock = $event.getModifierState('CapsLock')"
                    x-on:keydown="capsLock = $event.getModifierState('CapsLock')"
                    class="nx-input"
                >
                <button
                    type="button"
                    x-on:click="showPassword = !showPassword"
                    :aria-label="showPassword ? 'Hide password' : 'Show password'"
                    :aria-pressed="showPassword"
                    aria-controls="password"
                    class="nx-pass-toggle"
                >
                    <svg x-show="!showPassword" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.5 12s3.3-5.5 9.5-5.5 9.5 5.5 9.5 5.5-3.3 5.5-9.5 5.5S2.5 12 2.5 12Z" /><circle cx="12" cy="12" r="2.5" /></svg>
                    <svg x-show="showPassword" x-cloak width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" /><path d="M10.73 5.08A10.4 10.4 0 0 1 12 5c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19M6.61 6.61A13.5 13.5 0 0 0 1 13s3 6 9 6a9.7 9.7 0 0 0 5.39-1.61" /><line x1="2" x2="22" y1="2" y2="22" /></svg>
                </button>
            </div>
            <p id="caps-lock-warning" x-show="capsLock" x-cloak role="alert" class="nx-caps">Caps Lock is on</p>
            <div id="password-error" aria-live="polite">
                <x-input-error :messages="$errors->get('password')" class="nx-err" />
            </div>
        </div>

        <div class="nx-row">
            <label for="remember" class="nx-check">
                <input id="remember" type="checkbox" name="remember" value="1" @checked(old('remember'))>
                <span>Remember me</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="nx-link">Forgot password?</a>
            @endif
        </div>

        <div class="nx-btn-stack">
            <button
                type="submit"
                x-bind:disabled="isSubmitting"
                x-bind:aria-busy="isSubmitting"
                class="nx-btn nx-btn-primary"
            >
                <span x-show="!isSubmitting">Sign In</span>
                <span x-show="isSubmitting" x-cloak>Signing in&hellip;</span>
            </button>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="nx-btn nx-btn-secondary">Create Account</a>
            @endif
        </div>
    </form>
</x-auth-nexa-layout>
