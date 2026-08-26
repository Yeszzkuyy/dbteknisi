<section>
    <header>
        <h2 class="text-lg font-bold text-slate-800">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div
            x-data="{ preview: @js($user->avatar ? asset('storage/' . $user->avatar) : null) }"
        >
            <x-input-label for="avatar" :value="__('Foto Profil')" />

            <div class="mt-2 flex items-center gap-4">
                <div class="w-16 h-16 rounded-full overflow-hidden shrink-0 ring-2 ring-slate-200 dark:ring-slate-700">
                    <img x-show="preview" :src="preview" alt="{{ __('Foto Profil') }}" class="w-full h-full object-cover">
                    <div x-show="!preview" class="w-full h-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                        <span class="text-xl font-semibold text-blue-600 dark:text-blue-300">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                </div>

                <div>
                    <input id="avatar" name="avatar" type="file" accept="image/png,image/jpeg,image/webp,image/gif"
                           class="sr-only"
                           x-on:change="if ($event.target.files.length) { preview = URL.createObjectURL($event.target.files[0]) }" />
                    <label for="avatar"
                           class="inline-flex cursor-pointer items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700 transition">
                        Pilih Foto
                    </label>
                    <p class="mt-1.5 text-xs text-slate-500">JPG, PNG, WEBP, atau GIF. Maksimal 5MB.</p>
                </div>
            </div>

            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <div x-data="{ name: '{{ old('name', $user->name) }}' }">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" maxlength="16" required autofocus autocomplete="name" x-model="name" />
            <p x-show="name.length >= 16" x-cloak class="mt-1 text-sm text-red-600">
                Maksimal 16 karakter tercapai — pengguna hanya bisa memasukkan 16 karakter.
            </p>
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-slate-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-slate-600 hover:text-slate-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-medium text-green-600 dark:text-green-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
