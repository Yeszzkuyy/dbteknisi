<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Tambah User Baru</h1>
            <p class="text-slate-500 mt-1">Buat akun user dan assign role</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-6">
        <form action="{{ route('admin-panel.users.store') }}" method="POST">
            @csrf
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full px-4 py-2 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full px-4 py-2 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       required>
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                <input type="password" name="password"
                       class="w-full px-4 py-2 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       required minlength="8">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation"
                       class="w-full px-4 py-2 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       required>
                @error('password_confirmation')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-2">Role</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($roles as $role)
                        <label class="flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-200 cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                            <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                   class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-medium text-slate-800 capitalize">{{ $role->name }}</span>
                                <span class="block text-xs text-slate-500">{{ $role->permissions->count() }} permission</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('admin-panel.index') }}"
                   class="px-4 py-2 rounded-xl bg-blue-500 hover:bg-blue-600 text-white transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                    Simpan User
                </button>
            </div>
        </form>
    </div>
</x-app-layout>