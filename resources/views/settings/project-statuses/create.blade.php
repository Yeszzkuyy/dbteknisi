<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">Tambah Project Status</h1>
                <p class="text-slate-500 mt-1">Buat status project baru</p>
            </div>
            <a href="{{ route('project-statuses.index') }}" 
               class="px-5 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium transition">
                ← Kembali
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 w-full max-w-2xl">
            <form action="{{ route('project-statuses.store') }}" method="POST">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Status <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               placeholder="Contoh: Open, Progress, Done"
                               class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Warna (Color)</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="color" value="{{ old('color', '#3b82f6') }}"
                                   class="w-12 h-12 rounded border-slate-300 cursor-pointer">
                            <input type="text" name="color_text" value="{{ old('color_text', '#3b82f6') }}"
                                   placeholder="#3b82f6"
                                   class="flex-1 rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        @error('color') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}
                               class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <label for="is_default" class="text-sm font-medium text-slate-700">Jadikan Status Default</label>
                    </div>
                    @error('is_default') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                        Simpan
                    </button>
                    <a href="{{ route('project-statuses.index') }}" 
                       class="px-6 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>