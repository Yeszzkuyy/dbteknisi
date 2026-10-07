<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">{{ __('Detail Lead') }}: {{ $lead->customer?->name ?? '-' }}</h1>
            <p class="text-slate-500 mt-1">{{ __('Informasi lengkap lead / opportunity') }}</p>
        </div>
        <div class="flex gap-3">
            @php
                $canConvert = auth()->user()?->can('manage-marketing')
                    || ((int) $lead->assigned_to === (int) auth()->id() && auth()->user()?->can('manage-sales'))
                    || auth()->user()?->can('manage-technician')
                    || auth()->user()?->can('manage-admin');
            @endphp
            @if($canConvert && !in_array($lead->status, ['won', 'lost']))
                    <button type="button"
                            onclick="openConvertModal()"
                            class="px-4 py-2.5 rounded-xl bg-green-100 hover:bg-green-200 text-green-700 text-sm font-medium transition">
                        {{ __('Konversi ke Project') }}
                    </button>
                @endif
            @can('manage-marketing')
                <a href="{{ route('leads.edit', $lead) }}"
                   class="px-4 py-2.5 rounded-xl bg-accent-600 hover:bg-accent-700 text-white text-sm font-medium transition">
                    Edit
                </a>
                <form action="{{ route('leads.destroy', $lead) }}" method="POST" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit"
                            onclick="return confirm('{{ __('Hapus lead ini?') }}')"
                            class="px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-medium transition">
                        {{ __('Hapus') }}
                    </button>
                </form>
            @endcan
            @if(auth()->user()->can('view-sales') || auth()->user()->can('manage-sales'))
                <a href="{{ route('leads.pipeline') }}"
                   class="px-4 py-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-medium transition">
                    {{ __('Kembali ke Pipeline') }}
                </a>
            @endif
            <a href="{{ (auth()->user()->can('view-marketing') || auth()->user()->can('manage-marketing')) ? route('leads.index') : route('sales.my-leads') }}"
               class="px-4 py-2.5 rounded-xl bg-accent-500 text-white hover:bg-accent-600 dark:bg-accent-600 dark:hover:bg-accent-700 text-sm font-medium transition">
                {{ __('Kembali') }}
            </a>
        </div>
    </div>

    @php
        $canSalesWrite = auth()->user()?->can('manage-sales') || auth()->user()?->can('manage-inside-sales');
        $techRequests = $techRequests ?? collect();
        $activeTechRequest = $activeTechRequest ?? null;
        $proposalsList = $proposalsList ?? collect();
        $completedTechRequest = $techRequests->first(fn ($r) => $r->status === 'completed');
        $sentProposal = $proposalsList->first(fn ($p) => in_array($p->status, ['sent', 'viewed']));
        $upcomingSchedules = $upcomingSchedules ?? collect();
        $pastSchedules = $pastSchedules ?? collect();
    @endphp
    @if($canSalesWrite)
        <div class="flex flex-wrap items-center gap-2 mb-4">
            <x-icon-button as="a" icon="add" href="{{ route('sales.schedules.create', ['lead_id' => $lead->id]) }}" title="{{ __('Add schedule') }}" />
            @if($activeTechRequest)
                <a href="{{ route('sales.technical-requests.show', $activeTechRequest) }}"
                   class="px-3 py-2 text-xs rounded-lg bg-amber-100 hover:bg-amber-200 text-amber-800 font-medium transition">
                    {{ \App\Models\TechnicalRequest::statusLabel($activeTechRequest->status) }} • {{ $activeTechRequest->title }}
                </a>
            @else
                <x-icon-button as="a" icon="add" href="{{ route('sales.technical-requests.create', ['lead_id' => $lead->id]) }}" title="{{ __('Request technical team') }}" />
            @endif
            <x-icon-button as="a" icon="add" href="{{ route('sales.proposals.create', ['lead_id' => $lead->id]) }}" title="{{ $completedTechRequest ? __('Add proposal (technical result ready)') : __('Add proposal') }}" />
            @if($sentProposal)
                <a href="{{ route('sales.proposals.show', $sentProposal) }}"
                   class="px-3 py-2 text-xs rounded-lg bg-green-100 hover:bg-green-200 text-green-800 font-medium transition">
                    {{ $sentProposal->proposal_number }} • {{ \App\Models\Proposal::statusLabel($sentProposal->status) }}
                </a>
            @endif
        </div>
    @endif

    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">PT</label>
                        <p class="mt-1 text-slate-900 dark:text-slate-100 font-semibold">{{ $lead->pt_group ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Perusahaan') }}</label>
                        <p class="mt-1 text-slate-900 dark:text-slate-100 font-semibold">{{ $lead->customer?->company ?? $lead->customer?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">PIC</label>
                        <p class="mt-1 text-slate-900 dark:text-slate-100">{{ $lead->customer?->contact_person ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</label>
                        <p class="mt-1">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold
                                @php
                                    $colors = [
                                        'cool' => 'bg-blue-100 text-blue-800',
                                        'warm' => 'bg-yellow-100 text-yellow-800',
                                        'hot' => 'bg-orange-100 text-orange-800',
                                        'won' => 'bg-green-100 text-green-800',
                                        'lost' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                {{ $colors[$lead->status] ?? 'bg-slate-100 text-slate-800' }}">
                                {{ ucfirst($lead->status) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Masuk by') }}</label>
                        <p class="mt-1 text-slate-900 dark:text-slate-100">{{ $lead->source ? \App\Http\Controllers\LeadController::label($lead->source) : '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Sales Penanganan') }}</label>
                        <p class="mt-1 text-slate-900 dark:text-slate-100">{{ $lead->assignee?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Segment</label>
                        <p class="mt-1 text-slate-900 dark:text-slate-100 font-semibold">{{ $lead->segment ? \App\Http\Controllers\LeadController::label($lead->segment) : '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Partner</label>
                        <p class="mt-1 text-slate-900">
                            @if($lead->partner)
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ \App\Models\Partner::TYPE_DOTS[$lead->partner->type] ?? '#94a3b8' }}"></span>
                                    {{ $lead->partner->name }} ({{ __(\App\Models\Partner::TYPES[$lead->partner->type] ?? $lead->partner->type) }})
                                </span>
                            @else
                                -
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Alamat') }}</label>
                        <p class="mt-1 text-slate-900 dark:text-slate-100">{{ $lead->customer?->address ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Telpon Kantor') }}</label>
                        <p class="mt-1 text-slate-900 dark:text-slate-100">{{ $lead->customer?->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('No WA') }}</label>
                        <p class="mt-1 text-slate-900 dark:text-slate-100">{{ $lead->customer?->whatsapp ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Email</label>
                        <p class="mt-1 text-slate-900 dark:text-slate-100">{{ $lead->customer?->email ?? '-' }}</p>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Tanggal Masuk') }}</label>
                        <p class="mt-1 text-slate-900 dark:text-slate-100">{{ $lead->incoming_date ? $lead->incoming_date->format('d M Y') : '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Dibuat pada') }}</label>
                        <p class="mt-1 text-slate-900 dark:text-slate-100">{{ $lead->created_at?->format('d M Y H:i') ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Terakhir Diperbarui') }}</label>
                        <p class="mt-1 text-slate-900 dark:text-slate-100">{{ $lead->updated_at?->format('d M Y H:i') ?? '-' }}</p>
                    </div>
                </div>

                @if($lead->notes)
                    <div class="mt-1 pt-1 border-t">
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Catatan') }}</label>
                        <div class="mt-1 p-3 bg-slate-50 dark:bg-slate-700 rounded-xl text-slate-900 dark:text-slate-100 whitespace-pre-wrap">
                            {{ $lead->notes }}
                        </div>
                    </div>
                @endif

                @if($lead->kebutuhan)
                    <div class="mt-1 pt-1 border-t">
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Kebutuhan') }}</label>
                        <div class="mt-1 p-3 bg-slate-50 dark:bg-slate-700 rounded-xl text-slate-900 dark:text-slate-100 whitespace-pre-wrap">
                            {{ $lead->kebutuhan }}
                        </div>
                    </div>
                @endif

                @if($lead->solusi)
                    <div class="mt-1 pt-1 border-t">
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Solusi') }}</label>
                        <div class="mt-1 p-3 bg-slate-50 dark:bg-slate-700 rounded-xl text-slate-900 dark:text-slate-100 whitespace-pre-wrap">
                            {{ $lead->solusi }}
                        </div>
                    </div>
                @endif

                @if($lead->progress_notes)
                    <div class="mt-1 pt-1 border-t">
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Progress FollowUp / Keterangan') }}</label>
                        <div class="mt-1 p-3 bg-slate-50 dark:bg-slate-700 rounded-xl text-slate-900 dark:text-slate-100 whitespace-pre-wrap">
                            {{ $lead->progress_notes }}
                        </div>
                    </div>
                @endif

                @if(in_array($lead->status, ['won', 'lost']))
                    <div class="mt-1 pt-1 border-t">
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Hasil Akhir') }}</label>
                        @if($lead->status === 'won')
                            <div class="mt-1 p-3 bg-green-50 dark:bg-green-900/20 rounded-xl text-sm text-slate-700 dark:text-slate-200">
                                <p class="font-semibold text-green-700 dark:text-green-300">{{ __('Won') }}
                                    @if($lead->closed_at)
                                        <span class="font-normal text-slate-500">• {{ $lead->closed_at->setTimezone('Asia/Jakarta')->format('d M Y H:i') }}</span>
                                    @endif
                                </p>
                                @if($lead->closing_note)
                                    <p class="mt-1 whitespace-pre-wrap">{{ $lead->closing_note }}</p>
                                @endif
                            </div>
                        @else
                            <div class="mt-1 p-3 bg-red-50 dark:bg-red-900/20 rounded-xl text-sm text-slate-700 dark:text-slate-200">
                                <p class="font-semibold text-red-700 dark:text-red-300">{{ __('Lost') }}
                                    @if($lead->lost_reason)
                                        <span class="font-normal">• {{ \App\Models\Lead::lostReasonLabel($lead->lost_reason) }}</span>
                                    @endif
                                </p>
                                @if($lead->lost_note)
                                    <p class="mt-1 whitespace-pre-wrap">{{ $lead->lost_note }}</p>
                                @endif
                            </div>
                            @php
                                $canOutcome = auth()->user()?->can('manage-marketing')
                                    || ((int) $lead->assigned_to === (int) auth()->id() && auth()->user()?->can('manage-sales'))
                                    || auth()->user()?->can('manage-technician')
                                    || auth()->user()?->can('manage-admin');
                            @endphp
                            @if($canOutcome)
                                <form action="{{ route('leads.outcome', $lead) }}" method="POST" class="mt-3 flex flex-wrap items-end gap-3">
                                    @csrf @method('PATCH')
                                    <div>
                                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Alasan Lost') }}</label>
                                        <select name="lost_reason"
                                                class="rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500 text-sm">
                                            <option value="">{{ __('-- Pilih alasan --') }}</option>
                                            @foreach(\App\Models\Lead::LOST_REASONS as $reason)
                                                <option value="{{ $reason }}" @selected(old('lost_reason', $lead->lost_reason) === $reason)>
                                                    {{ \App\Models\Lead::lostReasonLabel($reason) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex-1 min-w-52">
                                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Catatan') }}</label>
                                        <input type="text" name="lost_note" value="{{ old('lost_note', $lead->lost_note) }}"
                                               placeholder="{{ __('Catatan tambahan (opsional)') }}"
                                               class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500 text-sm">
                                    </div>
                                    <button type="submit"
                                            class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition">
                                        {{ __('Simpan') }}
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                @endif

                <div class="mt-1 pt-1 border-t">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Lampiran (BOQ / Kebutuhan User)') }}</label>
                    <div class="mt-3 space-y-2">
                        @if($lead->documents->isEmpty())
                            <p class="text-slate-500">{{ __('Belum ada lampiran.') }}</p>
                        @else
                                @foreach($lead->documents as $doc)
                                    <div class="flex items-center justify-between gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-xl">
                                    <span class="text-sm text-slate-800 truncate">{{ $doc->file_name }}</span>
                                    <span class="shrink-0 inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-200 text-slate-700">
                                        {{ \App\Models\LeadDocument::categoryLabel($doc->category) }}
                                    </span>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <button type="button" title="{{ __('Lihat') }}"
                                                data-url="{{ route('leads.attachments.show', [$lead, $doc]) }}"
                                                data-filename="{{ $doc->file_name }}"
                                                data-mime="{{ $doc->mime_type }}"
                                                onclick="openPreviewModal(this.dataset.url, this.dataset.filename, this.dataset.mime)"
                                                class="p-2 rounded-lg bg-accent-50 hover:bg-accent-100 text-accent-700 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                            </svg>
                                        </button>
                                        <a href="{{ route('leads.attachments.download', [$lead, $doc]) }}" title="Download"
                                           class="p-2 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                                            </svg>
                                        </a>
                                        @can('manage-marketing')
                                            <form action="{{ route('leads.attachments.destroy', [$lead, $doc]) }}" method="POST"
                                                  onsubmit="return confirm('{{ __('Yakin hapus lampiran ini?') }}')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="{{ __('Hapus') }}"
                                                        class="p-2 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endcan
                                        @can('manage-marketing')
                                            <form action="{{ route('leads.attachments.category', [$lead, $doc]) }}" method="POST" class="inline-flex">
                                                @csrf @method('PATCH')
                                                <select name="category" onchange="this.form.submit()" title="{{ __('Kategori dokumen') }}"
                                                        class="rounded-lg border-slate-300 text-xs text-slate-600 focus:border-accent-500 focus:ring-accent-500">
                                                    <option value="">{{ __('Tanpa kategori') }}</option>
                                                    @foreach(\App\Models\LeadDocument::CATEGORIES as $category)
                                                        <option value="{{ $category }}" @selected($doc->category === $category)>
                                                            {{ \App\Models\LeadDocument::categoryLabel($category) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="mt-1 pt-1 border-t">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Riwayat Meeting & Follow Up') }}</label>
                        @can('manage-sales')
                            <div class="flex gap-2">
                                <a href="{{ route('sales.meetings.create', ['customer_id' => $lead->customer_id, 'lead_id' => $lead->id]) }}"
                                   class="px-3 py-1.5 text-xs rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium transition">{{ __('+ Meeting') }}</a>
                                <a href="{{ route('sales.follow-ups.create', ['customer_id' => $lead->customer_id, 'lead_id' => $lead->id]) }}"
                                   class="px-3 py-1.5 text-xs rounded-lg bg-green-100 hover:bg-green-200 text-green-700 font-medium transition">{{ __('+ Follow Up') }}</a>
                            </div>
                        @endcan
                    </div>
                    <div class="mt-3 space-y-2">
                        @forelse($timeline ?? [] as $item)
                            <div class="flex items-start gap-3 p-3 bg-slate-50 dark:bg-slate-700 rounded-xl">
                                <span class="mt-0.5 inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold shrink-0 {{ $item['type'] === 'meeting' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $item['title'] }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-slate-800 dark:text-slate-100 line-clamp-2">{{ $item['summary'] ?: '-' }}</p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $item['date'] ? \Carbon\Carbon::parse($item['date'])->format('d M Y') : '-' }}
                                        @if($item['by']) • {{ $item['by'] }} @endif
                                    </p>
                                </div>
                                @if($item['url'])
                                    <a href="{{ $item['url'] }}"
                                       class="shrink-0 px-3 py-1.5 text-xs rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium transition">{{ __('Detail') }}</a>
                                @endif
                            </div>
                        @empty
                            <p class="text-slate-500">{{ __('Belum ada meeting atau follow up untuk lead ini.') }}</p>
                        @endforelse
                    </div>
                </div>

                <div class="mt-1 pt-1 border-t">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Riwayat Lead') }}</label>
                    <div class="mt-3 space-y-2">
                        @forelse($lead->activities->sortByDesc('created_at') as $activity)
                            <div class="flex items-start gap-3 p-3 bg-slate-50 dark:bg-slate-700 rounded-xl">
                                <span class="mt-0.5 inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold shrink-0 bg-slate-200 text-slate-700 dark:bg-slate-600 dark:text-slate-200">
                                    {{ $activity->actionLabel() }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-slate-800 dark:text-slate-100">
                                        {{ $activity->user?->name ?? 'System' }}
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $activity->created_at?->setTimezone('Asia/Jakarta')->format('d M Y H:i') ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-slate-500">{{ __('Belum ada riwayat untuk lead ini.') }}</p>
                        @endforelse
                    </div>
                </div>

                <div class="mt-1 pt-1 border-t">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Inside Sales Task') }}</label>
                        @php
                            $canRequestTask = auth()->user()?->can('manage-sales-leads')
                                || auth()->user()?->can('manage-marketing')
                                || ((int) $lead->assigned_to === (int) auth()->id() && auth()->user()?->can('manage-sales'));
                        @endphp
                        @if($canRequestTask)
                            <a href="{{ route('lead-tasks.create', ['lead_id' => $lead->id]) }}"
                               class="px-3 py-1.5 text-xs rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium transition">{{ __('+ Request Inside Sales') }}</a>
                        @endif
                    </div>
                    <div class="mt-3 space-y-2">
                        @forelse($lead->tasks->sortByDesc('created_at') as $task)
                            <div class="flex items-start gap-3 p-3 bg-slate-50 dark:bg-slate-700 rounded-xl">
                                <span class="mt-0.5 inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold shrink-0 bg-indigo-50 text-indigo-700">
                                    {{ \App\Models\LeadTask::statusLabel($task->status) }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('lead-tasks.show', $task) }}"
                                       class="text-sm font-medium text-slate-800 dark:text-slate-100 hover:underline line-clamp-2">{{ $task->title }}</a>
                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $task->assignee?->name ?? __('Belum di-assign') }}
                                        @if($task->due_date) • {{ $task->due_date->format('d M Y') }} @endif
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-slate-500">{{ __('Belum ada inside sales task untuk lead ini.') }}</p>
                        @endforelse
                    </div>
                </div>

                <div class="mt-1 pt-1 border-t" id="section-technical">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Technical Request') }}</label>
                        @if($canSalesWrite && !$activeTechRequest)
                            <a href="{{ route('sales.technical-requests.create', ['lead_id' => $lead->id]) }}"
                               class="px-3 py-1.5 text-xs rounded-lg bg-green-100 hover:bg-green-200 text-green-700 font-medium transition">{{ __('+ Request Tim Teknis') }}</a>
                        @endif
                    </div>
                    <div class="mt-3 space-y-2">
                        @forelse($techRequests as $tech)
                            <div class="flex items-start gap-3 p-3 bg-slate-50 dark:bg-slate-700 rounded-xl">
                                <span class="mt-0.5 inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold shrink-0 bg-amber-100 text-amber-800">
                                    {{ \App\Models\TechnicalRequest::statusLabel($tech->status) }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-slate-800 dark:text-slate-100 line-clamp-2">{{ $tech->title }}</p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ \App\Models\TechnicalRequest::typeLabel($tech->request_type) }}
                                        @if($tech->technician) • {{ $tech->technician->name }} @endif
                                    </p>
                                    @if($tech->status === 'completed' && $tech->technical_result)
                                        <p class="mt-1 text-xs text-slate-600 dark:text-slate-300 line-clamp-2">{{ $tech->technical_result }}</p>
                                    @endif
                                </div>
                                <a href="{{ route('sales.technical-requests.show', $tech) }}"
                                   class="shrink-0 px-3 py-1.5 text-xs rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium transition">{{ __('Detail') }}</a>
                            </div>
                        @empty
                            <p class="text-slate-500">{{ __('Belum ada technical request untuk lead ini.') }}</p>
                        @endforelse
                    </div>
                </div>

                <div class="mt-1 pt-1 border-t" id="section-schedule">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Jadwal Sales') }}</label>
                        @if($canSalesWrite)
                            <a href="{{ route('sales.schedules.create', ['lead_id' => $lead->id]) }}"
                               class="px-3 py-1.5 text-xs rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium transition">{{ __('+ Jadwal') }}</a>
                        @endif
                    </div>
                    <div class="mt-3 space-y-2">
                        @php $allSchedules = $upcomingSchedules->concat($pastSchedules); @endphp
                        @forelse($allSchedules as $schedule)
                            <div class="flex items-start gap-3 p-3 bg-slate-50 dark:bg-slate-700 rounded-xl">
                                <span class="mt-0.5 inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold shrink-0 bg-blue-100 text-blue-800">
                                    {{ \App\Models\SalesSchedule::typeLabel($schedule->type) }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-slate-800 dark:text-slate-100 line-clamp-2">{{ $schedule->title }}</p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $schedule->start_at?->format('d M Y H:i') ?? '-' }}
                                        • {{ __('PIC') }}: {{ $schedule->assignee?->name ?? '-' }}
                                        • {{ \App\Models\SalesSchedule::statusLabel($schedule->status) }}
                                    </p>
                                </div>
                                <a href="{{ route('sales.schedules.show', $schedule) }}"
                                   class="shrink-0 px-3 py-1.5 text-xs rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium transition">{{ __('Detail') }}</a>
                            </div>
                        @empty
                            <p class="text-slate-500">{{ __('Belum ada jadwal untuk lead ini.') }}</p>
                        @endforelse
                    </div>
                </div>

                <div class="mt-1 pt-1 border-t" id="section-proposal">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Penawaran') }}</label>
                        @if($canSalesWrite)
                            <a href="{{ route('sales.proposals.create', ['lead_id' => $lead->id]) }}"
                               class="px-3 py-1.5 text-xs rounded-lg bg-green-100 hover:bg-green-200 text-green-700 font-medium transition">{{ __('+ Penawaran') }}</a>
                        @endif
                    </div>
                    <div class="mt-3 space-y-2">
                        @forelse($proposalsList as $proposal)
                            <div class="flex items-start gap-3 p-3 bg-slate-50 dark:bg-slate-700 rounded-xl">
                                <span class="mt-0.5 inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold shrink-0 bg-slate-200 text-slate-700 dark:bg-slate-600 dark:text-slate-200">
                                    {{ \App\Models\Proposal::statusLabel($proposal->status) }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-slate-800 dark:text-slate-100 line-clamp-2">{{ $proposal->proposal_number }}</p>
                                    <p class="mt-1 text-xs text-slate-500">Rp {{ number_format($proposal->grand_total, 0, ',', '.') }}</p>
                                </div>
                                <a href="{{ route('sales.proposals.show', $proposal) }}"
                                   class="shrink-0 px-3 py-1.5 text-xs rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium transition">{{ __('Detail') }}</a>
                            </div>
                        @empty
                            <p class="text-slate-500">{{ __('Belum ada penawaran untuk lead ini.') }}</p>
                        @endforelse
                    </div>
                </div>

                <div class="mt-1 pt-1 border-t">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Dokumentasi Instalasi (Read-Only)') }}</label>
                    <p class="mt-1 text-sm text-slate-500">{{ __('Dokumen dari project terkait customer ini (diunggah Tim Teknisi)') }}</p>

                    <div class="mt-4">
                        @if($documents->isEmpty())
                            <p class="text-slate-500">{{ __('Belum ada dokumentasi instalasi untuk customer ini.') }}</p>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
@foreach($documents as $doc)
                                    <div class="border border-slate-200 rounded-xl p-4 hover:shadow-md transition">
                                        <div class="flex items-center gap-3">
                                            <div class="p-2 bg-slate-100 rounded-lg">
                                                @if($doc->is_image)
                                                    <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                @elseif($doc->is_pdf)
                                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                @else
                                                    <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-slate-900 truncate">{{ $doc->file_name }}</p>
                                                <p class="text-xs text-slate-500">
                                                    {{ $doc->category->name ?? __('Tanpa Kategori') }} •
                                                    {{ $doc->uploader->name ?? 'Unknown' }} •
                                                    {{ $doc->created_at->format('d M Y') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="mt-3 flex gap-2">
                                            <a href="{{ route('leads.documents.download', ['lead' => $lead->id, 'document' => $doc->id]) }}"
                                               class="px-3 py-1.5 text-xs rounded-lg bg-accent-100 text-accent-700 hover:bg-accent-200 font-medium transition">
                                                Download
                                            </a>
                                            @if($doc->is_image || $doc->is_pdf)
                                                <a href="{{ route('leads.documents.preview', ['lead' => $lead->id, 'document' => $doc->id]) }}"
                                                   target="_blank"
                                                   class="px-3 py-1.5 text-xs rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium transition">
                                                    {{ __('Pratinjau') }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
</x-app-layout>

@if($canConvert ?? false)
@if(!in_array($lead->status, ['won', 'lost']))
<div id="convertModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/50" onclick="closeConvertModal()"></div>
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl">
            <div class="flex items-center justify-between p-4 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">{{ __('Konversi Lead ke Project') }}</h3>
                <button type="button" onclick="closeConvertModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="{{ route('leads.convert', $lead) }}" method="POST">
                @csrf @method('PATCH')
                <div class="p-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Nama Project') }}</label>
                        <input type="text" name="project_name" required
                               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-transparent"
                               placeholder="{{ __('Nama project baru') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Status Project') }}</label>
                        <select name="project_status_id" required
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-transparent">
                            @foreach($projectStatuses ?? [] as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Tipe Pekerjaan') }} <span class="text-red-500">*</span></label>
                        <select name="work_type_id" required
                                 class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-transparent">
                            <option value="">{{ __('-- Pilih Tipe Pekerjaan --') }}</option>
                            @foreach($workTypes ?? [] as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Closing Note (Opsional)') }}</label>
                        <textarea name="closing_note" rows="3"
                                  placeholder="{{ __('Catatan penutupan: kesepakatan, nilai deal, tindak lanjut...') }}"
                                  class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-transparent"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 p-4 border-t border-slate-200">
                    <button type="button" onclick="closeConvertModal()"
                            class="px-4 py-2 bg-accent-500 hover:bg-accent-600 text-white rounded-lg transition">
                        {{ __('Batal') }}
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        {{ __('Konversi') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endif

<script>
    function openConvertModal() {
        document.getElementById('convertModal').classList.remove('hidden');
    }
    function closeConvertModal() {
        document.getElementById('convertModal').classList.add('hidden');
    }

    function openPreviewModal(url, filename, mimeType) {
        var modal = document.getElementById('previewModal');
        var content = document.getElementById('previewContent');
        var title = document.getElementById('previewTitle');

        title.textContent = filename;
        content.innerHTML = '';

        var imageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
        var pdfTypes = ['application/pdf'];

        if (imageTypes.includes(mimeType)) {
            var img = document.createElement('img');
            img.src = url;
            img.className = 'max-h-[80vh] max-w-full mx-auto rounded-lg object-contain';
            img.alt = filename;
            content.appendChild(img);
        } else if (pdfTypes.includes(mimeType)) {
            var iframe = document.createElement('iframe');
            iframe.src = url;
            iframe.className = 'w-full h-[80vh] rounded-lg border-0';
            content.appendChild(iframe);
        } else {
            content.innerHTML = '<div class="text-center py-10">' +
                '<svg class="w-16 h-16 mx-auto text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path></svg>' +
                '<p class="text-slate-600 font-medium mb-2">File ini perlu didownload</p>' +
                '<a href="' + url.replace('/show', '/download') + '" ' +
                'class="inline-flex items-center gap-2 px-4 py-2 bg-accent-600 hover:bg-accent-700 text-white rounded-lg text-sm font-medium transition">' +
                '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"></path></svg>' +
                'Download</a>' +
                '</div>';
        }

        modal.classList.remove('hidden');
    }

    function closePreviewModal() {
        var modal = document.getElementById('previewModal');
        modal.classList.add('hidden');
        document.getElementById('previewContent').innerHTML = '';
    }
</script>

<!-- Preview Modal -->
<div id="previewModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/70" onclick="closePreviewModal()"></div>
        <div class="relative w-full max-w-4xl bg-white rounded-2xl shadow-2xl">
            <div class="flex items-center justify-between p-4 border-b border-slate-200">
                <h3 id="previewTitle" class="text-sm font-semibold text-slate-900 truncate max-w-[80%]"></h3>
                <button type="button" onclick="closePreviewModal()" class="text-slate-400 hover:text-slate-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div id="previewContent" class="p-4"></div>
        </div>
    </div>
</div>