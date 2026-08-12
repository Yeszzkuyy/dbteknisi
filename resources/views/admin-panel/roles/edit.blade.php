<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Edit Role: {{ $role->name }}</h1>
            <p class="text-slate-500 mt-1">Perbarui nama role dan permission</p>
        </div>
        <a href="{{ route('admin-panel.index') }}"
           class="px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition">
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 max-w-3xl">
        <form method="POST" action="{{ route('admin-panel.roles.update', $role) }}">
            @csrf @method('PUT')
            
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama Role</label>
                <input type="text" id="name" name="name" required value="{{ $role->name }}"
                       class="w-full px-4 py-2 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="mb-6">
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-sm font-medium text-slate-700">Permissions</label>
                    <div class="flex gap-2">
                        <button type="button" id="select-all" class="px-3 py-1 text-xs border border-slate-300 rounded hover:bg-slate-50">Pilih Semua</button>
                        <button type="button" id="deselect-all" class="px-3 py-1 text-xs border border-slate-300 rounded hover:bg-slate-50">Batal Pilih</button>
                    </div>
                </div>
                
                <div class="space-y-4">
                    @foreach($permissions as $group => $perms)
                        <div class="border border-slate-200 rounded-xl p-4">
                            <h4 class="font-medium text-slate-800 mb-3 capitalize">{{ $group }}</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                @foreach($perms as $perm)
                                    <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-200 cursor-pointer hover:bg-slate-50 transition">
                                        <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                               {{ $role->hasPermissionTo($perm->name) ? 'checked' : '' }}
                                               class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm text-slate-700">{{ $perm->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('admin-panel.index') }}"
                   class="px-4 py-2 border border-slate-300 text-slate-700 rounded-xl hover:bg-slate-50 transition">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all');
    const deselectAll = document.getElementById('deselect-all');
    const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
    
    selectAll.addEventListener('click', () => {
        checkboxes.forEach(cb => cb.checked = true);
    });
    
    deselectAll.addEventListener('click', () => {
        checkboxes.forEach(cb => cb.checked = false);
    });
});
</script>