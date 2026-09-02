<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Kelola Lead: {{ $lead->customer->name ?? 'N/A' }}</h1>
            <p class="text-slate-500 mt-1">Isi solusi, progress follow-up, catatan internal, dan assign ke Sales</p>
        </div>
        <a href="{{ route('manage-sales.index') }}"
           class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-white dark:border-slate-600 dark:text-slate-200 text-sm font-medium transition">
            Kembali
        </a>
    </div>

    <form action="{{ route('manage-sales.update', $lead) }}" method="POST"
          class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-4">
        @csrf
        @method('PUT')

        {{-- Info Lead --}}
        <section class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-slate-50 dark:bg-slate-700 rounded-xl p-4">
            <div>
                <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">Lead dari PT</div>
                @if($lead->pt_group)
                    <span class="inline-flex px-2 py-0.5 rounded {{ \App\Models\Lead::PT_COLORS[$lead->pt_group] ?? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' }} text-xs font-semibold mt-1">{{ $lead->pt_group }}</span>
                @else
                    <span class="text-slate-400">-</span>
                @endif
            </div>
            <div>
                <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">PIC</div>
                <div class="text-sm text-slate-700 mt-1">{{ $lead->customer->contact_person ?? '-' }}</div>
            </div>
            <div>
                <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal Masuk</div>
                <div class="text-sm text-slate-700 mt-1">{{ $lead->incoming_date?->format('d M Y') ?? '-' }}</div>
            </div>
        </section>

        {{-- Detail --}}
        <section class="border-t border-slate-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div class="md:col-span-2">
                    <label for="kebutuhan" class="flex items-center gap-1.5 text-sm font-medium text-slate-700 mb-1">
                        Kebutuhan
                    </label>
                    <textarea id="kebutuhan" rows="2" readonly
                              class="w-full rounded-xl border-slate-300 bg-slate-50">{{ $lead->kebutuhan }}</textarea>
                </div>
                <div>
                    <label for="solusi" class="flex items-center gap-1.5 text-sm font-medium text-slate-700 mb-1">
                        Solusi
                        <x-info-tip tip="Solusi yang ditawarkan untuk memenuhi kebutuhan customer." />
                    </label>
                    <textarea name="solusi" id="solusi" rows="3"
                              placeholder="cth: Rekomendasi Cisco Webex Board 55S untuk ruang meeting"
                              class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('solusi', $lead->solusi) }}</textarea>
                </div>
                <div>
                    <label for="progress_notes" class="flex items-center gap-1.5 text-sm font-medium text-slate-700 mb-1">
                        Progress FollowUp / Keterangan
                        <x-info-tip tip="Catatan progress follow-up: sudah dihubungi, jadwal meeting, status negosiasi, dll." />
                    </label>
                    <textarea name="progress_notes" id="progress_notes" rows="3"
                              placeholder="cth: Sudah telepon, menunggu balasan. Follow-up lagi Senin depan."
                              class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('progress_notes', $lead->progress_notes) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label for="notes" class="flex items-center gap-1.5 text-sm font-medium text-slate-700 mb-1">
                        Catatan Internal
                        <x-info-tip tip="Catatan khusus tim, tidak ditampilkan ke customer." />
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                              placeholder="Catatan internal..."
                              class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $lead->notes) }}</textarea>
                </div>
            </div>
        </section>

        {{-- Penugasan --}}
        <section class="border-t border-slate-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <label for="assigned_to" class="flex items-center gap-1.5 text-sm font-medium text-slate-700 mb-1">
                        Assign / Direct ke Sales
                        <x-info-tip tip="Sales yang bertanggung jawab follow-up lead ini." />
                    </label>
                    <select name="assigned_to" id="assigned_to"
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">— Belum di-assign (NEW) —</option>
                        @foreach($salesUsers as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to', $lead->assigned_to) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @if($lead->assignee)
                        <p class="mt-1 text-xs text-slate-500">
                            Di-assign ke {{ $lead->assignee->name }}
                            @if($lead->assigned_at) pada {{ $lead->assigned_at->format('d M Y H:i') }} @endif
                        </p>
                    @endif
                </div>
            </div>
        </section>

        {{-- Aksi --}}
        <div class="flex justify-end gap-3 border-t border-slate-200">
            <a href="{{ route('manage-sales.index') }}"
               class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-white dark:border-slate-600 dark:text-slate-200 text-sm font-medium transition">
                Batal
            </a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition">
                Simpan
            </button>
        </div>
    </form>
</x-app-layout>