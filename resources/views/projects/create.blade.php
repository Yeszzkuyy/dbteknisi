<x-app-layout>
<div class="px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Tambah Project</h1>
            <p class="text-slate-500 mt-1">Buat project baru untuk: <span class="font-semibold text-slate-700">{{ $customer->name }}</span></p>
        </div>
        <a href="{{ route('customers.show', $customer) }}" class="px-5 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium transition">Kembali</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 w-full">
        <form action="{{ route('projects.store') }}" method="POST">
            @csrf
            <input type="hidden" name="customer_id" value="{{ $customer->id }}">

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Project <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Pekerjaan <span class="text-red-500">*</span></label>
                    <select name="work_type_id" required class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih Jenis Pekerjaan</option>
                        @foreach($workTypes as $workType)
                            <option value="{{ $workType->id }}" {{ old('work_type_id') == $workType->id ? 'selected' : '' }}>{{ $workType->name }}</option>
                        @endforeach
                    </select>
                    @error('work_type_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Account Manager</label>
                    <select name="account_manager_id" class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih Account Manager</option>
                        @foreach($accountManagers as $am)
                            <option value="{{ $am->id }}" {{ old('account_manager_id') == $am->id ? 'selected' : '' }}>{{ $am->name }}</option>
                        @endforeach
                    </select>
                    @error('account_manager_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- PIC Engineer --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        PIC Engineer <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="pic_engineer" 
                           value="{{ old('pic_engineer') }}"
                           required
                           placeholder="Nama PIC Engineer yang bertanggung jawab"
                           class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('pic_engineer') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Support Technicians --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Support Technicians <span class="text-slate-400 text-xs">(Opsional)</span>
                    </label>
                    <input type="text" 
                           name="support_technicians" 
                           value="{{ old('support_technicians') }}"
                           placeholder="Nama support teknisi (pisahkan dengan koma jika lebih dari 1)"
                           class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    <p class="text-xs text-slate-400 mt-1">Contoh: Budi, Andi, Siti</p>
                    @error('support_technicians') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                    <textarea name="description" rows="3" class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                    @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">Simpan Project</button>
                <a href="{{ route('customers.show', $customer) }}" class="px-6 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium transition">Batal</a>
            </div>
        </form>
    </div>
</div>
</x-app-layout>
EOF