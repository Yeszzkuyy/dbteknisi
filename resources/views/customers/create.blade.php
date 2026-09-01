<x-app-layout>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
        <div class="min-w-0">
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">
                Tambah Customer
            </h1>
            <p class="text-slate-500 mt-1">
                Catat perusahaan / customer baru ke Tridaya App.
            </p>
        </div>
        <a href="{{ route('customers.index') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium transition sm:self-start">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <form action="{{ route('customers.store') }}" method="POST"
          class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-6 space-y-4 w-full">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">
                Nama Perusahaan <span class="text-red-500">*</span>
            </label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                   class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="address" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">
                Address
            </label>
            <textarea id="address" name="address" rows="3"
                      class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('address') }}</textarea>
            @error('address') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="contact_person" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">
                PIC
            </label>
            <input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person') }}"
                   placeholder="cth: Ibu Vita"
                   class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            @error('contact_person') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">
                No Telp
            </label>
            <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                   placeholder="cth: 021-1234-5678"
                   class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            @error('phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="whatsapp" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">
                No WA
            </label>
            <input type="text" id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}"
                   placeholder="cth: 0812-3456-7890"
                   class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            @error('whatsapp') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">
                Email
            </label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                   class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="notes" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">
                Notes
            </label>
            <textarea id="notes" name="notes" rows="3"
                      class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>
            @error('notes') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 border-t border-slate-200 dark:border-slate-600 pt-5">
            <a href="{{ route('customers.index') }}"
               class="px-6 py-2.5 rounded-xl bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium transition text-center">
                Batal
            </a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition">
                Simpan Customer
            </button>
        </div>
    </form>
</x-app-layout>
