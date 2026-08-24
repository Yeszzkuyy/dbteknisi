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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="pt_group" class="block text-sm font-medium text-slate-700 mb-1">PT <span class="text-red-500">*</span></label>
                    <select name="pt_group" id="pt_group" required
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih PT</option>
                        @foreach($ptGroups as $group)
                            <option value="{{ $group }}" {{ old('pt_group') == $group ? 'selected' : '' }}>{{ $group }}</option>
                        @endforeach
                    </select>
                    @error('pt_group')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="incoming_date" class="block text-sm font-medium text-slate-700 mb-1">Tanggal Masuk</label>
                    <input type="date" name="incoming_date" id="incoming_date" value="{{ old('incoming_date', now()->toDateString()) }}"
                           class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="source" class="block text-sm font-medium text-slate-700 mb-1">Masuk by</label>
                    <select name="source" id="source"
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih</option>
                        @foreach($sources as $source)
                            <option value="{{ $source }}" {{ old('source') == $source ? 'selected' : '' }}>
                                {{ \App\Http\Controllers\LeadController::label($source) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="segment" class="block text-sm font-medium text-slate-700 mb-1">Segmentasi <span class="text-red-500">*</span></label>
                    <select name="segment" id="segment" required
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih Segmentasi</option>
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
            </div>

            <div x-data="{ mode: '{{ old('customer_mode', 'new') }}' }">
                <label class="block text-sm font-medium text-slate-700 mb-2">Data Customer <span class="text-red-500">*</span></label>

                <div class="flex items-center gap-6 mb-4">
                    <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-medium text-slate-700">
                        <input type="radio" name="customer_mode" value="new" x-model="mode" class="accent-blue-600">
                        Customer Baru
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-medium text-slate-700">
                        <input type="radio" name="customer_mode" value="existing" x-model="mode" class="accent-blue-600">
                        Pilih Customer Lama
                    </label>
                </div>

                <div x-show="mode === 'new'" x-cloak>
                    <label for="customer_name" class="block text-sm font-medium text-slate-700 mb-1">Perusahaan <span class="text-red-500">*</span></label>
                    <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}"
                           placeholder="cth: PT Koin Konstruksi"
                           class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('customer_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="md:col-span-2">
                            <label for="customer_address" class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
                            <textarea name="customer_address" id="customer_address" rows="2"
                                      placeholder="cth: Plaza Kebon Jeruk Blok D7-8, Jl. Raya Perjuangan, Jakarta Barat 11530"
                                      class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('customer_address') }}</textarea>
                        </div>
                        <div>
                            <label for="customer_contact_person" class="block text-sm font-medium text-slate-700 mb-1">PIC</label>
                            <input type="text" name="customer_contact_person" id="customer_contact_person" value="{{ old('customer_contact_person') }}"
                                   placeholder="cth: Ibu Vita"
                                   class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="customer_phone" class="block text-sm font-medium text-slate-700 mb-1">Telp</label>
                            <input type="text" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}"
                                   placeholder="cth: wa.me/6282234470680 atau 0812-3456-7890"
                                   class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="customer_email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                            <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email') }}"
                                   placeholder="cth: vita@ptkoin.com"
                                   class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                            @error('customer_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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
                                {{ $customer->name }}{{ $customer->contact_person ? ' - '.$customer->contact_person : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="mt-4">
                        <label for="customer_contact_person" class="block text-sm font-medium text-slate-700 mb-1">PIC (perbarui kontak customer)</label>
                        <input type="text" name="customer_contact_person" id="customer_contact_person_existing" value="{{ old('customer_contact_person') }}"
                               placeholder="kosongkan jika tidak berubah"
                               class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div>
                <label for="kebutuhan" class="block text-sm font-medium text-slate-700 mb-1">Kebutuhan</label>
                <textarea name="kebutuhan" id="kebutuhan" rows="3"
                          placeholder="cth: Kebutuhan Cisco IP Phone 780 Series, Cisco IP Phone 6800 series, dan Cisco IP conference phone dengan instalasi"
                          class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('kebutuhan') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-slate-700 mb-1">Sales</label>
                    <select name="assigned_to" id="assigned_to"
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih Sales</option>
                        @foreach($salesUsers as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-slate-700 mb-1">Catatan Internal</label>
                    <textarea name="notes" id="notes" rows="3"
                              class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>
            </div>

        
    </div>
</x-app-layout>
