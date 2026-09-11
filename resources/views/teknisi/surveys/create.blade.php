<x-app-layout>
<div class="px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 dark:text-slate-100">Tambah Survey</h1>
            <p class="text-slate-500 mt-1">Isi data survey untuk project</p>
        </div>
        <a href="{{ route('teknisi.surveys.index') }}"
           class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 hover:bg-white dark:hover:bg-slate-700 font-medium transition">
            Kembali
        </a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-6 w-full">
        <form action="{{ route('teknisi.surveys.store') }}" method="POST">
            @csrf

            <div class="space-y-4">
                {{-- Project --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Project <span class="text-red-500">*</span></label>
                    <div x-data="{
                        query: {{ $selectedProject ? json_encode($selectedProject->project_name) : '""' }},
                        selectedId: {{ $selectedProject ? $selectedProject->id : 'null' }},
                        open: false,
                        projects: {{ Js::from($projects->map(fn($p) => ['id' => $p->id, 'name' => $p->project_name . ' — ' . ($p->customer?->name ?? '')])) }},
                        get filtered() {
                            const q = this.query.toLowerCase().trim();
                            return this.projects.filter(p => !q || p.name.toLowerCase().includes(q));
                        }
                    }" class="relative">
                        <input type="hidden" name="project_id" x-model="selectedId">
                        <input type="text" x-model="query"
                               @focus="open = true"
                               @input="if (query !== '') selectedId = null; open = true"
                               @keydown.escape="open = false"
                               @blur="setTimeout(() => open = false, 150)"
                               placeholder="Cari project..."
                               autocomplete="off"
                               class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 focus:border-blue-500 focus:ring-blue-500">
                        <div x-show="open" x-cloak x-transition
                             class="absolute z-20 mt-1 w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                            <template x-for="p in filtered" :key="p.id">
                                <button type="button"
                                        @mousedown.prevent="selectedId = p.id; query = p.name; open = false"
                                        class="w-full text-left px-4 py-2.5 text-sm hover:bg-blue-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition"
                                        :class="String(p.id) === String(selectedId) ? 'bg-blue-50 dark:bg-slate-700' : ''"
                                        x-text="p.name"></button>
                            </template>
                            <p x-show="filtered.length === 0" class="px-4 py-3 text-sm text-slate-400">Tidak ada hasil.</p>
                        </div>
                    </div>
                    @error('project_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Sales Request --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Sales Request</label>
                    <textarea name="sales_request" rows="3"
                              class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 focus:border-blue-500 focus:ring-blue-500">{{ old('sales_request') }}</textarea>
                    @error('sales_request') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Survey Date --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Tanggal Survey <span class="text-red-500">*</span></label>
                        <input type="date" name="survey_date" value="{{ old('survey_date') }}" required
                               class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 focus:border-blue-500 focus:ring-blue-500">
                        @error('survey_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Status <span class="text-red-500">*</span></label>
                        <select name="status" required
                                class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 focus:border-blue-500 focus:ring-blue-500">
                            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="scheduled" {{ old('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="on_survey" {{ old('status') === 'on_survey' ? 'selected' : '' }}>On Survey</option>
                            <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        @error('status') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Location --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Lokasi</label>
                        <input type="text" name="location" value="{{ old('location') }}"
                               class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 focus:border-blue-500 focus:ring-blue-500">
                        @error('location') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- PIC --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">PIC</label>
                        <input type="text" name="pic" value="{{ old('pic') }}"
                               class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 focus:border-blue-500 focus:ring-blue-500">
                        @error('pic') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Survey Data --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Data Survey</label>
                    <textarea name="survey_data" rows="4"
                              class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 focus:border-blue-500 focus:ring-blue-500">{{ old('survey_data') }}</textarea>
                    @error('survey_data') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Survey Report --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Laporan Survey</label>
                    <textarea name="survey_report" rows="4"
                              class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 focus:border-blue-500 focus:ring-blue-500">{{ old('survey_report') }}</textarea>
                    @error('survey_report') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Catatan</label>
                    <textarea name="notes" rows="3"
                              class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>
                    @error('notes') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">Simpan Survey</button>
                <a href="{{ route('teknisi.surveys.index') }}" class="px-6 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 hover:bg-white dark:hover:bg-slate-700 font-medium transition">Batal</a>
            </div>
        </form>
    </div>
</div>
</x-app-layout>
