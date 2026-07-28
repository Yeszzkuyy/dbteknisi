<div>
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-4">
            {{ session('message') }}
        </div>
    @endif

    {{-- Form Tambah --}}
    <div class="bg-slate-50 rounded-xl p-4 mb-6">
        <h3 class="text-lg font-bold text-slate-800 mb-3">Tambah Status Baru</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input type="text" 
                       wire:model="name" 
                       placeholder="Nama Status"
                       class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="flex items-center gap-3">
                <input type="color" 
                       wire:model="color" 
                       value="#3b82f6"
                       class="w-12 h-12 rounded border-slate-300 cursor-pointer">
                <input type="text" 
                       wire:model="color" 
                       placeholder="#3b82f6"
                       class="flex-1 rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                    <input type="checkbox" wire:model="is_default" class="w-5 h-5 rounded border-slate-300 text-blue-600">
                    Default
                </label>
                <button wire:click="save" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition">
                    + Tambah
                </button>
            </div>
        </div>
    </div>

    {{-- Tabel Status --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Warna</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Default</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($statuses as $status)
                        <tr class="hover:bg-slate-50 transition" wire:key="status-{{ $status->id }}">
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $status->name }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full" 
                                      style="background-color: {{ $status->color ?? '#3b82f6' }}20; color: {{ $status->color ?? '#3b82f6' }}">
                                    {{ $status->color ?? 'Default' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($status->is_default)
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Primary</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <button wire:click="edit({{ $status->id }})" 
                                            class="text-amber-600 hover:text-amber-800">Edit</button>
                                    <button wire:click="delete({{ $status->id }})" 
                                            onclick="return confirm('Hapus status ini?')"
                                            class="text-red-600 hover:text-red-800">Hapus</button>
                                </div>
                            </td>
                        </tr>

                        {{-- Row Edit --}}
                        @if($editId == $status->id)
                            <tr class="bg-blue-50">
                                <td colspan="4" class="px-6 py-4">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <input type="text" 
                                                   wire:model="editName" 
                                                   class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                                            @error('editName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <input type="color" 
                                                   wire:model="editColor" 
                                                   class="w-12 h-12 rounded border-slate-300 cursor-pointer">
                                            <input type="text" 
                                                   wire:model="editColor" 
                                                   class="flex-1 rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                                                <input type="checkbox" wire:model="editIsDefault" class="w-5 h-5 rounded border-slate-300 text-blue-600">
                                                Default
                                            </label>
                                            <button wire:click="update" 
                                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition">
                                                Update
                                            </button>
                                            <button wire:click="cancelEdit" 
                                                    class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl transition">
                                                Batal
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="4" class="py-16 text-center text-slate-400">
                                Belum ada status.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $statuses->links() }}
        </div>
    </div>
</div>