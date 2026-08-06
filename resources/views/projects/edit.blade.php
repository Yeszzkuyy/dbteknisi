
<x-app-layout>
<div class="px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Edit Project</h1>
            <p class="text-slate-500 mt-1">Edit: {{ $project->project_name }}</p>
        </div>
        <a href="{{ route('projects.show', $project) }}" class="px-5 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium transition">Kembali</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 w-full">
        <form action="{{ route('projects.update', $project) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Project</label>
                    <input type="text" name="project_name" value="{{ old('project_name', $project->project_name) }}" class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Customer</label>
                    <select name="customer_id" class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ $project->customer_id == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Pekerjaan</label>
                    <select name="work_type_id" class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        @foreach($workTypes as $workType)
                            <option value="{{ $workType->id }}" {{ $project->work_type_id == $workType->id ? 'selected' : '' }}>{{ $workType->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Account Manager</label>
                    <select name="account_manager_id" class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih</option>
                        @foreach($accountManagers as $am)
                            <option value="{{ $am->id }}" {{ $project->account_manager_id == $am->id ? 'selected' : '' }}>{{ $am->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- PIC Engineer --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        PIC Engineer <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="pic_engineer" 
                           value="{{ old('pic_engineer', $project->pic_engineer) }}"
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
                           value="{{ old('support_technicians', $project->support_technicians) }}"
                           placeholder="Nama support teknisi (pisahkan dengan koma jika lebih dari 1)"
                           class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    <p class="text-xs text-slate-400 mt-1">Contoh: Budi, Andi, Siti</p>
                    @error('support_technicians') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                    <select name="project_status_id" class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->id }}" {{ (old('project_status_id', $project->project_status_id) == $status->id) ? 'selected' : '' }}>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Progress --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Progress (0-100%)
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="range" 
                               name="progress" 
                               min="0" 
                               max="100" 
                               value="{{ old('progress', $project->progress ?? 0) }}"
                               oninput="document.getElementById('progressValue').textContent = this.value + '%'"
                               class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <span id="progressValue" class="text-sm font-semibold text-slate-700 min-w-[50px]">
                            {{ $project->progress ?? 0 }}%
                        </span>
                    </div>
                    @error('progress') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                    <textarea name="description" rows="3" class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ $project->description }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">Update</button>
                <a href="{{ route('projects.show', $project) }}" class="px-6 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium transition">Batal</a>
            </div>
        </form>
    </div>
</div>
</x-app-layout>
