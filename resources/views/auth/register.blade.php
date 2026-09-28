<x-auth-nexa-layout title="Create account">
    <h2 class="nx-h">Create your account</h2>
    <p class="nx-sub">Start managing your workspace in minutes.</p>

    <form
        method="POST"
        action="{{ route('register') }}"
        x-data="{ isSubmitting: false, showPassword: false, showConfirmPassword: false }"
        x-on:submit="isSubmitting = true"
        class="nx-form"
        aria-label="Sign up"
    >
        @csrf

        <div class="nx-field">
            <label for="name" class="nx-label">Name</label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                placeholder="Enter your name"
                required
                autofocus
                autocomplete="name"
                aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                aria-describedby="name-error"
                class="nx-input"
            >
            <div id="name-error" aria-live="polite">
                <x-input-error :messages="$errors->get('name')" class="nx-err" />
            </div>
        </div>

        <div class="nx-field">
            <label for="email" class="nx-label">Email address</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="Enter your email"
                required
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
                    autocomplete="new-password"
                    aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                    aria-describedby="password-error"
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
            <div id="password-error" aria-live="polite">
                <x-input-error :messages="$errors->get('password')" class="nx-err" />
            </div>
        </div>

        <div class="nx-field">
            <label for="password_confirmation" class="nx-label">Confirm password</label>
            <div class="nx-pass-wrap">
                <input
                    id="password_confirmation"
                    :type="showConfirmPassword ? 'text' : 'password'"
                    name="password_confirmation"
                    placeholder="Confirm your password"
                    required
                    autocomplete="new-password"
                    aria-invalid="{{ $errors->has('password_confirmation') ? 'true' : 'false' }}"
                    aria-describedby="password-confirmation-error"
                    class="nx-input"
                >
                <button
                    type="button"
                    x-on:click="showConfirmPassword = !showConfirmPassword"
                    :aria-label="showConfirmPassword ? 'Hide password' : 'Show password'"
                    :aria-pressed="showConfirmPassword"
                    aria-controls="password_confirmation"
                    class="nx-pass-toggle"
                >
                    <svg x-show="!showConfirmPassword" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.5 12s3.3-5.5 9.5-5.5 9.5 5.5 9.5 5.5-3.3 5.5-9.5 5.5S2.5 12 2.5 12Z" /><circle cx="12" cy="12" r="2.5" /></svg>
                    <svg x-show="showConfirmPassword" x-cloak width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" /><path d="M10.73 5.08A10.4 10.4 0 0 1 12 5c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19M6.61 6.61A13.5 13.5 0 0 0 1 13s3 6 9 6a9.7 9.7 0 0 0 5.39-1.61" /><line x1="2" x2="22" y1="2" y2="22" /></svg>
                </button>
            </div>
            <div id="password-confirmation-error" aria-live="polite">
                <x-input-error :messages="$errors->get('password_confirmation')" class="nx-err" />
            </div>
        </div>

        <div class="nx-btn-stack">
            <button
                type="submit"
                x-bind:disabled="isSubmitting"
                x-bind:aria-busy="isSubmitting"
                class="nx-btn nx-btn-primary"
            >
                <span x-show="!isSubmitting">Sign Up</span>
                <span x-show="isSubmitting" x-cloak>Signing up&hellip;</span>
            </button>
            <a href="{{ route('login') }}" class="nx-btn nx-btn-secondary">Sign In</a>
        </div>
    </form>
</x-auth-nexa-layout>
