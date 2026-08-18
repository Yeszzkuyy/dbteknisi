<x-app-layout>
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">Edit Meeting</h1>
                <p class="text-slate-500 mt-1">Perbarui data meeting dengan customer.</p>
            </div>
            <a href="{{ route('sales.meetings.index') }}"
               class="px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-medium transition">
                Kembali
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <form action="{{ route('sales.meetings.update', $meeting) }}" method="POST" class="space-y-6">
                @csrf @method('PUT')

                <div x-data="{ mode: '{{ old('customer_mode', 'existing') }}' }">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Customer <span class="text-red-500">*</span></label>

                    <div class="flex items-center gap-6 mb-4">
                        <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-medium text-slate-700">
                            <input type="radio" name="customer_mode" value="new" x-model="mode" class="accent-blue-600">
                            Customer Baru (ketik nama)
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-medium text-slate-700">
                            <input type="radio" name="customer_mode" value="existing" x-model="mode" class="accent-blue-600">
                            Pilih Customer Lama
                        </label>
                    </div>

                    <div x-show="mode === 'new'" x-cloak>
                        <label for="customer_name" class="block text-sm font-medium text-slate-700 mb-1">Nama Customer / Perusahaan <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', $meeting->customer->name ?? '') }}"
                               placeholder="cth: PT Maju Bersama, CV Karya Abadi, dll..."
                               class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('customer_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div x-show="mode === 'existing'" x-cloak>
                        <label for="customer_id" class="block text-sm font-medium text-slate-700 mb-1">Pilih Customer <span class="text-red-500">*</span></label>
                        <select name="customer_id" id="customer_id"
                                class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- Pilih Customer --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id', $meeting->customer_id) == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Meeting <span class="text-red-500">*</span></label>
                    <input type="date" name="meeting_date" value="{{ old('meeting_date', $meeting->meeting_date->format('Y-m-d')) }}" required
                           class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Peserta</label>
                    <input type="text" name="participants" value="{{ old('participants', $meeting->participants) }}"
                           class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kebutuhan User</label>
                    <textarea name="user_needs" rows="3"
                              class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('user_needs', $meeting->user_needs) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Keluhan User</label>
                    <textarea name="user_complaints" rows="3"
                              class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('user_complaints', $meeting->user_complaints) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Sistem Existing</label>
                    <textarea name="existing_system" rows="3"
                              class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('existing_system', $meeting->existing_system) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Catatan Lain</label>
                    <textarea name="notes" rows="2"
                              class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $meeting->notes) }}</textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                        Update
                    </button>
                    <a href="{{ route('sales.meetings.index') }}"
                       class="px-6 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
