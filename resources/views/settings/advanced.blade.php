<x-app-layout>
    <div class="w-full space-y-6 tab-container">
        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">{{ __('Pengaturan Lanjutan') }}</h1>
                <p class="mt-1 text-slate-500">{{ __('Keamanan akun Anda') }}</p>
            </div>
            <x-icon-button as="a" icon="back" href="{{ route('settings.edit') }}" title="{{ __('Kembali') }}" />
        </div>

        <x-profile-tabs />

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-600 dark:bg-slate-800">
            @include('settings.partials.update-account-form')
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-600 dark:bg-slate-800">
            @include('profile.partials.update-password-form')
        </div>

        <div class="rounded-2xl border border-red-200 bg-white p-6 shadow-sm dark:border-red-900/50 dark:bg-slate-800">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
