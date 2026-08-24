<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Tambah Lead / Opportunity Baru</h1>
            <p class="text-slate-500 mt-1">Kelola lead marketing dan opportunity sales</p>
        </div>
        <a href="{{ route('leads.index') }}"
           class="px-4 py-2.5 rounded-xl bg-blue-500 text-white hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-sm font-medium transition">
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <form action="{{ route('leads.store') }}" method="POST" class="space-y-6">
            @csrf

            <div x-data="{ mode: '{{ old('customer_mode', 'new') }}' }">
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
                    <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}"
                           placeholder="cth: PT Maju Bersama, CV Karya Abadi, dll..."
                           class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('customer_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="customer_company" class="block text-sm font-medium text-slate-700 mb-1">Nama Perusahaan</label>
                            <input type="text" name="customer_company" id="customer_company" value="{{ old('customer_company') }}"
                                   placeholder="cth: PT Maju Bersama"
                                   class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="customer_email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                            <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email') }}"
                                   placeholder="cth: info@majubersama.co.id"
                                   class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                            @error('customer_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="customer_phone" class="block text-sm font-medium text-slate-700 mb-1">No. Telepon / WhatsApp</label>
                            <input type="text" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}"
                                   placeholder="cth: 0812-3456-7890"
                                   class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="customer_address" class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
                            <input type="text" name="customer_address" id="customer_address" value="{{ old('customer_address') }}"
                                   placeholder="cth: Jl. Jendral Sudirman No. 45, Jakarta"
                                   class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <div x-show="mode === 'existing'" x-cloak>
                    <label for="customer_id" class="block text-sm font-medium text-slate-700 mb-1">Pilih Customer <span class="text-red-500">*</span></label>
                    <select name="customer_id" id="customer_id"
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} ({{ $customer->company ?? 'Perusahaan' }})
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="segment" class="block text-sm font-medium text-slate-700 mb-1">Segment <span class="text-red-500">*</span></label>
                    <select name="segment" id="segment" required
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih Segment</option>
                        @foreach($segments as $segment)
                            <option value="{{ $segment }}" {{ old('segment') == $segment ? 'selected' : '' }}>
                                {{ \App\Http\Controllers\LeadController::label($segment) }}
                            </option>
                        @endforeach
                    </select>
                    @error('segment')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="source" class="block text-sm font-medium text-slate-700 mb-1">Sumber Lead</label>
                    <select name="source" id="source"
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih Sumber</option>
                        @foreach($sources as $source)
                            <option value="{{ $source }}" {{ old('source') == $source ? 'selected' : '' }}>
                                {{ \App\Http\Controllers\LeadController::label($source) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="kebutuhan" class="block text-sm font-medium text-slate-700 mb-1">Kebutuhan User</label>
                    <textarea name="kebutuhan" id="kebutuhan" rows="3"
                              placeholder="cth: Instalasi jaringan untuk kantor baru 3 lantai..."
                              class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('kebutuhan') }}</textarea>
                </div>

                <div>
                    <label for="incoming_date" class="block text-sm font-medium text-slate-700 mb-1">Tanggal Masuk</label>
                    <input type="date" name="incoming_date" id="incoming_date" value="{{ old('incoming_date', now()->toDateString()) }}"
                           class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label for="assigned_to" class="block text-sm font-medium text-slate-700 mb-1">PIC Marketing</label>
                <select name="assigned_to" id="assigned_to"
                        class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih PIC</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-slate-700 mb-1">Catatan</label>
                <textarea name="notes" id="notes" rows="4"
                          class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('leads.index') }}"
                   class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                    Simpan Lead
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
