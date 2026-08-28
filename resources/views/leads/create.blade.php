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

    <form action="{{ route('leads.store') }}" method="POST" enctype="multipart/form-data"
          class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-6">
        @csrf

        {{-- Info Umum --}}
        <section class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6">
            <div>
                <label for="pt_group" class="flex items-center gap-1.5 text-sm font-medium text-slate-700 mb-1">
                    PT <span class="text-red-500">*</span>
                    <x-info-tip tip="Entitas perusahaan grup yang menangani lead ini: NTI, MGK, TPS, atau WANI." />
                </label>
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
                <label for="incoming_date" class="flex items-center gap-1.5 text-sm font-medium text-slate-700 mb-1">
                    Tanggal Masuk <span class="text-red-500">*</span>
                    <x-info-tip tip="Tanggal pertama kali lead ini masuk ke tim (misal dari chat/email)." />
                </label>
                <input type="date" name="incoming_date" id="incoming_date" required value="{{ old('incoming_date', now()->toDateString()) }}"
                       class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label for="source" class="flex items-center gap-1.5 text-sm font-medium text-slate-700 mb-1">
                    Masuk by
                    <x-info-tip tip="Dari mana lead ini berasal: WhatsApp, email, telepon, canvasing, event, dll." />
                </label>
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
                <label for="segment" class="flex items-center gap-1.5 text-sm font-medium text-slate-700 mb-1">
                    Segmentasi <span class="text-red-500">*</span>
                    <x-info-tip tip="Jenis calon client: End User, Vendor, System Integrator, Kontraktor, Gov, Principle, Distributor, atau lainnya." />
                </label>
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
        </section>

        {{-- Data Customer --}}
        <section x-data="{ mode: '{{ old('customer_mode', 'new') }}' }" class="pt-6 border-t border-slate-200">
            <label class="flex items-center gap-1.5 text-sm font-medium text-slate-700 mb-3">
                Data Customer <span class="text-red-500">*</span>
                <x-info-tip tip="Pilih Customer Baru kalau belum pernah tercatat, atau Customer Lama kalau sudah ada di database." />
            </label>

            <div class="flex items-center gap-6 mb-4">
                <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-medium text-slate-700">
                    <input type="radio" name="customer_mode" value="new" x-model="mode" class="accent-blue-600">
                    Customer Baru
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-medium text-slate-700">
                    <input type="radio" name="customer_mode" value="existing" x-model="mode" class="accent-blue-600">
                    Customer Lama
                </label>
            </div>

            <div x-show="mode === 'new'" x-cloak class="space-y-4">
                <div>
                    <label for="customer_name" class="block text-sm font-medium text-slate-700 mb-1">
                        Perusahaan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}"
                           placeholder="cth: PT Koin Konstruksi"
                           class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('customer_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="customer_address" class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
                        <textarea name="customer_address" id="customer_address" rows="2"
                                  placeholder="cth: Plaza Kebon Jeruk Blok D7-8, Jl. Raya Perjuangan, Jakarta Barat 11530"
                                  class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('customer_address') }}</textarea>
                    </div>
                    <div>
                        <label for="customer_contact_person" class="flex items-center gap-1.5 text-sm font-medium text-slate-700 mb-1">
                            PIC
                            <x-info-tip tip="Nama orang yang bisa dihubungi di perusahaan tersebut." />
                        </label>
                        <input type="text" name="customer_contact_person" id="customer_contact_person" value="{{ old('customer_contact_person') }}"
                               placeholder="cth: Ibu Vita"
                               class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="customer_phone" class="block text-sm font-medium text-slate-700 mb-1">Telpon Kantor</label>
                        <input type="text" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}"
                               placeholder="cth: 0812-3456-7890"
                               class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="customer_whatsapp" class="block text-sm font-medium text-slate-700 mb-1">No WA</label>
                        <input type="text" name="customer_whatsapp" id="customer_whatsapp" value="{{ old('customer_whatsapp') }}"
                               placeholder="cth: 0812-3456-7890"
                               class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="md:col-span-2">
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

            <div x-show="mode === 'existing'" x-cloak class="space-y-4">
                <div>
                    <label class="flex items-center gap-1.5 text-sm font-medium text-slate-700 mb-1">
                        Pilih Customer <span class="text-red-500">*</span>
                        <x-info-tip tip="Ketik nama perusahaan untuk mencari customer yang sudah terdaftar." />
                    </label>
                    <x-searchable-select
                        name="customer_id"
                        :options="$customers->mapWithKeys(fn ($c) => [$c->id => $c->name.($c->contact_person ? ' - '.$c->contact_person : '')])->all()"
                        :selected="old('customer_id')"
                        placeholder="Ketik nama customer untuk mencari..."
                    />
                    @error('customer_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="customer_contact_person_existing" class="flex items-center gap-1.5 text-sm font-medium text-slate-700 mb-1">
                        PIC (perbarui kontak customer)
                        <x-info-tip tip="Isi hanya jika ingin memperbarui nama PIC customer ini." />
                    </label>
                    <input type="text" name="customer_contact_person" id="customer_contact_person_existing" value="{{ old('customer_contact_person') }}"
                           placeholder="kosongkan jika tidak berubah"
                           class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
        </section>

        {{-- Detail --}}
        <section class="pt-6 border-t border-slate-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-5">
                <div>
                    <label for="kebutuhan" class="flex items-center gap-1.5 text-sm font-medium text-slate-700 mb-1">
                        Kebutuhan
                        <x-info-tip tip="Ringkasan apa yang dibutuhkan user: produk, jumlah, dan apakah termasuk instalasi." />
                    </label>
                    <textarea name="kebutuhan" id="kebutuhan" rows="3"
                              placeholder="cth: Kebutuhan Cisco IP Phone 780 Series dengan instalasi"
                              class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('kebutuhan') }}</textarea>
                </div>
                <div>
                    <label for="solusi" class="flex items-center gap-1.5 text-sm font-medium text-slate-700 mb-1">
                        Solusi
                        <x-info-tip tip="Solusi yang ditawarkan untuk memenuhi kebutuhan customer." />
                    </label>
                    <textarea name="solusi" id="solusi" rows="3"
                              placeholder="cth: Rekomendasi Cisco Webex Board 55S untuk ruang meeting"
                              class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('solusi') }}</textarea>
                </div>
                <div>
                    <label for="progress_notes" class="flex items-center gap-1.5 text-sm font-medium text-slate-700 mb-1">
                        Progress FollowUp / Keterangan
                        <x-info-tip tip="Catatan progress follow-up: sudah dihubungi, jadwal meeting, status negosiasi, dll." />
                    </label>
                    <textarea name="progress_notes" id="progress_notes" rows="3"
                              placeholder="cth: Sudah telepon, menunggu balasan. Follow-up lagi Senin depan."
                              class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('progress_notes') }}</textarea>
                </div>
                <div>
                    <label for="notes" class="flex items-center gap-1.5 text-sm font-medium text-slate-700 mb-1">
                        Catatan Internal
                        <x-info-tip tip="Catatan khusus tim, tidak ditampilkan ke customer." />
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                              placeholder="Catatan internal..."
                              class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>
                </div>
            </div>
        </section>

        {{-- Penugasan --}}
        <section class="pt-6 border-t border-slate-200">
            <div>
                <label for="assigned_to" class="flex items-center gap-1.5 text-sm font-medium text-slate-700 mb-1">
                    Sales <span class="text-red-500">*</span>
                    <x-info-tip tip="Orang yang bertanggung jawab follow-up lead ini." />
                </label>
                <select name="assigned_to" id="assigned_to" required
                        class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih Sales</option>
                    @foreach($salesUsers as $user)
                        <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </section>

        {{-- Lampiran --}}
        <section class="pt-6 border-t border-slate-200">
            <label class="flex items-center gap-1.5 text-sm font-medium text-slate-700 mb-1">
                Lampiran (BOQ / Kebutuhan User)
                <x-info-tip tip="Unggah file BOQ awal, spesifikasi, atau dokumen kebutuhan dari user. Maksimal 5 file, 10 MB per file." />
            </label>
            <input type="file" name="attachments[]" multiple
                   accept=".pdf,.jpg,.jpeg,.png,.gif,.webp,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.txt,.csv"
                   class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 file:text-sm file:font-medium">
            @error('attachments.*')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </section>

        {{-- Aksi --}}
        <div class="flex justify-end gap-3 pt-6 border-t border-slate-200">
            <a href="{{ route('leads.index') }}"
               class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-medium transition">
                Batal
            </a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition">
                Simpan Lead
            </button>
        </div>
    </form>
</x-app-layout>
