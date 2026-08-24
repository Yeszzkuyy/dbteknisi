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
                            class="px-4 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white text-sm font-medium transition">
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

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium text-slate-500">Customer</label>
                        <p class="mt-1 text-slate-900">{{ $lead->customer->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-500">Perusahaan</label>
                        <p class="mt-1 text-slate-900">{{ $lead->customer->company ?? '-' }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-500">Status</label>
                        <p class="mt-1">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium
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
                        <label class="text-sm font-medium text-slate-500">Sumber Lead</label>
                        <p class="mt-1 text-slate-900">{{ $lead->source ? ucfirst(str_replace('_', ' ', $lead->source)) : '-' }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-500">Segment</label>
                        <p class="mt-1 text-slate-900 font-medium">{{ $lead->segment ? \App\Http\Controllers\LeadController::label($lead->segment) : '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-500">Tanggal Masuk</label>
                        <p class="mt-1 text-slate-900">{{ $lead->incoming_date ? $lead->incoming_date->format('d M Y') : '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-500">Dibuat pada</label>
                        <p class="mt-1 text-slate-900">{{ $lead->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>

                @if($lead->notes)
                    <div class="mt-6 pt-6 border-t">
                        <label class="text-sm font-medium text-slate-500">Catatan</label>
                        <div class="mt-1 p-4 bg-slate-50 rounded-xl text-slate-900 whitespace-pre-wrap">
                            {{ $lead->notes }}
                        </div>
                    </div>
                @endif

                <div class="mt-6 pt-6 border-t">
                    <label class="text-sm font-medium text-slate-500">Dokumentasi Instalasi (Read-Only)</label>
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
                            class="px-4 py-2 text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 transition">
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
</script>