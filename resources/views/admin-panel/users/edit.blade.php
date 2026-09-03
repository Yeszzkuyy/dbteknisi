<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Edit User: {{ $user->name }}</h1>
            <p class="text-slate-500 mt-1">Perbarui informasi user dan role</p>
        </div>
        <a href="{{ route('admin-panel.index') }}"
           class="px-4 py-2 bg-blue-400 text-white rounded-xl hover:bg-blue-500 transition">
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-6">
        <form method="POST" action="{{ route('admin-panel.users.update', $user) }}">
            @csrf @method('PUT')
            
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama</label>
                <input type="text" id="name" name="name" required value="{{ $user->name }}"
                       class="w-full px-4 py-2 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input type="email" id="email" name="email" required value="{{ $user->email }}"
                       class="w-full px-4 py-2 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password Baru (kosongkan jika tidak diubah)</label>
                <input type="password" id="password" name="password" minlength="8"
                       class="w-full px-4 py-2 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password Baru</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="w-full px-4 py-2 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-2">Role(s)</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($roles as $role)
                        <label class="flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-200 cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                            <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                   {{ $user->hasRole($role->name) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-medium text-slate-800 capitalize">{{ $role->name }}</span>
                                <span class="block text-xs text-slate-500">{{ $role->permissions->count() }} permission</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin-panel.index') }}"
                   class="px-4 py-2 rounded-xl bg-blue-500 hover:bg-blue-600 text-white transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>