<x-app-layout>
    <div >
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">Tambah Follow Up</h1>
                <p class="text-slate-500 mt-1">Catat tindak lanjut dengan customer.</p>
            </div>
            <a href="{{ route('sales.follow-ups.index') }}"
               class="px-4 py-2.5 rounded-xl bg-blue-400 hover:bg-blue-500 text-white text-sm font-medium transition">
                Kembali
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <form action="{{ route('sales.follow-ups.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Customer <span class="text-red-500">*</span></label>
                    <select name="customer_id" id="customer_id" required
                            onchange="window.location.href='{{ route('sales.follow-ups.create') }}?customer_id='+this.value"
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Pilih Customer --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id', $customerId) == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Terkait Meeting (opsional)</label>
                    <select name="meeting_id"
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Tidak terkait meeting tertentu --</option>
                        @foreach($meetings as $meeting)
                            <option value="{{ $meeting->id }}" {{ old('meeting_id', $meetingId) == $meeting->id ? 'selected' : '' }}>
                                Meeting {{ $meeting->meeting_date->format('d M Y') }}
                            </option>
                        @endforeach
                    </select>
                    @error('meeting_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi Follow Up <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="4" required
                              placeholder="Jelaskan tindak lanjut yang dilakukan atau informasi tambahan dari user..."
                              class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Follow Up</label>
                    <input type="date" name="follow_up_date" value="{{ old('follow_up_date', date('Y-m-d')) }}"
                           class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('follow_up_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                        Simpan
                    </button>
                    <a href="{{ route('sales.follow-ups.index') }}"
                       class="px-6 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
