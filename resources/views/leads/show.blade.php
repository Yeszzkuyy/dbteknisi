<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Detail Lead: {{ $lead->customer->name }}</h1>
            <p class="text-slate-500 mt-1">Informasi lengkap lead / opportunity</p>
        </div>
        <div class="flex gap-3">
            @can('manage-marketing')
                @if(!in_array($lead->status, ['won', 'lost']))
                    <button type="button"
                            onclick="openConvertModal()"
                            class="px-4 py-2.5 rounded-xl bg-green-100 hover:bg-green-200 text-green-700 text-sm font-medium transition">
                        Konversi ke Project
                    </button>
                @endif
                <a href="{{ route('leads.edit', $lead) }}"
                   class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition">
                    Edit
                </a>
                <form action="{{ route('leads.destroy', $lead) }}" method="POST" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit"
                            onclick="return confirm('Hapus lead ini?')"
                            class="px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-medium transition">
                        Hapus
                    </button>
                </form>
            @endcan
            <a href="{{ route('leads.index') }}"
               class="px-4 py-2.5 rounded-xl bg-blue-500 text-white hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-sm font-medium transition">
                Kembali
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">PT</label>
                        <p class="mt-1 text-slate-900 dark:text-slate-100 font-semibold">{{ $lead->pt_group ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Perusahaan</label>
                        <p class="mt-1 text-slate-900 dark:text-slate-100 font-semibold">{{ $lead->customer->company ?? $lead->customer->name }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">PIC</label>
                        <p class="mt-1 text-slate-900 dark:text-slate-100">{{ $lead->customer->contact_person ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</label>
                        <p class="mt-1">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold
                                @php
                                    $colors = [
                                        'new' => 'bg-blue-100 text-blue-800',
                                        'contacted' => 'bg-yellow-100 text-yellow-800',
                                        'qualified' => 'bg-purple-100 text-purple-800',
                                        'proposal' => 'bg-orange-100 text-orange-800',
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
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Masuk by</label>
                        <p class="mt-1 text-slate-900 dark:text-slate-100">{{ $lead->source ? ucfirst(str_replace('_', ' ', $lead->source)) : '-' }}</p>
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
                                    {{ $lead->partner->name }} ({{ ucfirst($lead->partner->type) }})
                                </span>
                            @else
                                -
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Alamat</label>
                        <p class="mt-1 text-slate-900 dark:text-slate-100">{{ $lead->customer->address ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Telpon Kantor</label>
                        <p class="mt-1 text-slate-900 dark:text-slate-100">{{ $lead->customer->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">No WA</label>
                        <p class="mt-1 text-slate-900 dark:text-slate-100">{{ $lead->customer->whatsapp ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Email</label>
                        <p class="mt-1 text-slate-900 dark:text-slate-100">{{ $lead->customer->email ?? '-' }}</p>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Tanggal Masuk</label>
                        <p class="mt-1 text-slate-900 dark:text-slate-100">{{ $lead->incoming_date ? $lead->incoming_date->format('d M Y') : '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Dibuat pada</label>
                        <p class="mt-1 text-slate-900 dark:text-slate-100">{{ $lead->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>

                @if($lead->notes)
                    <div class="mt-1 pt-1 border-t">
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Catatan</label>
                        <div class="mt-1 p-3 bg-slate-50 dark:bg-slate-700 rounded-xl text-slate-900 dark:text-slate-100 whitespace-pre-wrap">
                            {{ $lead->notes }}
                        </div>
                    </div>
                @endif

                @if($lead->kebutuhan)
                    <div class="mt-1 pt-1 border-t">
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Kebutuhan</label>
                        <div class="mt-1 p-3 bg-slate-50 dark:bg-slate-700 rounded-xl text-slate-900 dark:text-slate-100 whitespace-pre-wrap">
                            {{ $lead->kebutuhan }}
                        </div>
                    </div>
                @endif

                @if($lead->solusi)
                    <div class="mt-1 pt-1 border-t">
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Solusi</label>
                        <div class="mt-1 p-3 bg-slate-50 dark:bg-slate-700 rounded-xl text-slate-900 dark:text-slate-100 whitespace-pre-wrap">
                            {{ $lead->solusi }}
                        </div>
                    </div>
                @endif

                @if($lead->progress_notes)
                    <div class="mt-1 pt-1 border-t">
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Progress FollowUp / Keterangan</label>
                        <div class="mt-1 p-3 bg-slate-50 dark:bg-slate-700 rounded-xl text-slate-900 dark:text-slate-100 whitespace-pre-wrap">
                            {{ $lead->progress_notes }}
                        </div>
                    </div>
                @endif

                <div class="mt-1 pt-1 border-t">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Lampiran (BOQ / Kebutuhan User)</label>
                    <div class="mt-3 space-y-2">
                        @if($lead->documents->isEmpty())
                            <p class="text-slate-500">Belum ada lampiran.</p>
                        @else
                            @foreach($lead->documents as $doc)
                                    <div class="flex items-center justify-between gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-xl">
                                    <span class="text-sm text-slate-800 truncate">{{ $doc->file_name }}</span>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <button type="button" title="Lihat"
                                                data-url="{{ route('leads.attachments.show', [$lead, $doc]) }}"
                                                data-filename="{{ $doc->file_name }}"
                                                data-mime="{{ $doc->mime_type }}"
                                                onclick="openPreviewModal(this.dataset.url, this.dataset.filename, this.dataset.mime)"
                                                class="p-2 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                            </svg>
                                        </button>
                                        <a href="{{ route('leads.attachments.download', [$lead, $doc]) }}" title="Download"
                                           class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                                            </svg>
                                        </a>
                                        @can('manage-marketing')
                                            <form action="{{ route('leads.attachments.destroy', [$lead, $doc]) }}" method="POST"
                                                  onsubmit="return confirm('Yakin hapus lampiran ini?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Hapus"
                                                        class="p-2 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="mt-1 pt-1 border-t">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Dokumentasi Instalasi (Read-Only)</label>
                    <p class="mt-1 text-sm text-slate-500">Dokumen dari project terkait customer ini (diunggah Tim Teknisi)</p>

                    <div class="mt-4">
                        @if($documents->isEmpty())
                            <p class="text-slate-500">Belum ada dokumentasi instalasi untuk customer ini.</p>
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
                                                    {{ $doc->category->name ?? 'Tanpa Kategori' }} •
                                                    {{ $doc->uploader->name ?? 'Unknown' }} •
                                                    {{ $doc->created_at->format('d M Y') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="mt-3 flex gap-2">
                                            <a href="{{ route('leads.documents.download', ['lead' => $lead->id, 'document' => $doc->id]) }}"
                                               class="px-3 py-1.5 text-xs rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 font-medium transition">
                                                Download
                                            </a>
                                            @if($doc->is_image || $doc->is_pdf)
                                                <a href="{{ route('leads.documents.preview', ['lead' => $lead->id, 'document' => $doc->id]) }}"
                                                   target="_blank"
                                                   class="px-3 py-1.5 text-xs rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-medium transition">
                                                    Pratinjau
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

@can('manage-marketing')
@if(!in_array($lead->status, ['won', 'lost']))
<div id="convertModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/50" onclick="closeConvertModal()"></div>
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl">
            <div class="flex items-center justify-between p-4 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Konversi Lead ke Project</h3>
                <button type="button" onclick="closeConvertModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="{{ route('leads.convert', $lead) }}" method="POST">
                @csrf @method('PATCH')
                <div class="p-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Project</label>
                        <input type="text" name="project_name" required
                               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Nama project baru">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Status Project</label>
                        <select name="project_status_id" required
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @foreach($projectStatuses ?? [] as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tipe Pekerjaan (Opsional)</label>
                        <select name="work_type_id"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">-- Pilih Tipe Pekerjaan --</option>
                            @foreach($workTypes ?? [] as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 p-4 border-t border-slate-200">
                    <button type="button" onclick="closeConvertModal()"
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        Konversi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endcan

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
                'class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">' +
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