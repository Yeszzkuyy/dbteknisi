<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800">
                    {{ $project->project_code ?? 'NO CODE' }}
                </span>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mt-1">
                    {{ $project->project_name }}
                </h2>
            </div>
            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800 shadow-sm border border-blue-200">
                Status: {{ $project->status }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                
                <div class="space-y-6 lg:col-span-1">
                    
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-md font-bold text-gray-900 border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                            📁 Detail Proyek
                        </h3>
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Customer</dt>
                                <dd class="text-sm font-medium text-gray-900 mt-0.5">{{ $project->customer?->name ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Work Type</dt>
                                <dd class="text-sm font-medium text-gray-900 mt-0.5">{{ $project->workType?->name ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Quotation Number (BOQ)</dt>
                                <dd class="text-sm font-medium text-gray-900 mt-0.5">{{ $project->quotation_number ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Description</dt>
                                <dd class="text-sm text-gray-600 mt-0.5 whitespace-pre-line">{{ $project->description ?? '-' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-md font-bold text-gray-900 border-b border-gray-100 pb-3 mb-4">
                            👷 Tim Lapangan
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">PIC Engineer</span>
                                <div class="p-3 bg-gray-50 border border-gray-100 rounded-lg text-sm font-semibold text-gray-800">
                                    👤 {{ $project->picEngineer?->name ?? '-' }}
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Support Engineers</span>
                                    <a href="{{ route('project-supports.create', $project) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                                        + Tambah
                                    </a>
                                </div>
                                
                                @if($project->supports->count())
                                    <ul class="divide-y divide-gray-100 border border-gray-100 rounded-lg overflow-hidden bg-white shadow-inner">
                                        @foreach($project->supports as $support)
                                            <li class="p-3 text-sm text-gray-700 flex justify-between items-center hover:bg-gray-50 transition">
                                                <span>👥 {{ $support->engineer?->name ?? 'User Terhapus' }}</span>
                                                <form method="POST" action="{{ route('project-supports.destroy', $support) }}" onsubmit="return confirm('Hapus support engineer ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-semibold hover:underline">Hapus</button>
                                                </form>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-xs text-gray-400 italic p-3 bg-gray-50 rounded-lg border border-dashed border-gray-200 text-center">Belum ada support engineer</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6 lg:col-span-2">
                    
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                            <h3 class="text-md font-bold text-gray-900 flex items-center gap-2">
                                📑 Berkas & Dokumen Lapangan
                            </h3>
                            <a href="{{ route('project-documents.create', $project) }}" class="px-3 py-1.5 bg-indigo-600 text-white font-semibold text-xs rounded-lg hover:bg-indigo-700 shadow-sm transition">
                                📤 Upload Berkas
                            </a>
                        </div>

                        @if($project->documents->count())
                            <div class="overflow-x-auto rounded-lg border border-gray-100">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Kategori Berkas</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Nama File</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Uploader</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        @foreach($project->documents as $document)
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="px-4 py-3 text-sm font-bold text-gray-900">
                                                    <span class="px-2 py-0.5 rounded text-xs bg-amber-100 text-amber-800">
                                                        {{ $document->category?->name ?? ($document->document_type ?? 'Lain-lain') }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-600 font-medium">
                                                    📄 {{ $document->file_name }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-500">
                                                    {{ $document->uploader?->name ?? 'User Terhapus' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center p-8 border border-dashed border-gray-200 rounded-xl bg-gray-50">
                                <p class="text-sm text-gray-400">Belum ada dokumen terkumpul untuk proyek ini.</p>
                            </div>
                        @endif
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                            <h3 class="text-md font-bold text-gray-900 flex items-center gap-2">
                                📋 Checklist Pekerjaan (Tasks)
                            </h3>
                            <a href="{{ route('project-tasks.create', $project) }}" class="px-3 py-1.5 bg-indigo-600 text-white font-semibold text-xs rounded-lg hover:bg-indigo-700 shadow-sm transition">
                                + Tambah Task
                            </a>
                        </div>

                        @if($project->tasks->count())
                            <div class="overflow-x-auto rounded-lg border border-gray-100">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Pekerjaan</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Engineer</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        @foreach($project->tasks as $task)
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $task->title }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $task->engineer?->name ?? 'User Terhapus' }}</td>
                                                <td class="px-4 py-3 text-sm">
                                                    <span class="px-2 py-0.5 text-xs font-bold rounded-full {{ $task->status === 'Done' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                        {{ $task->status }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    <form method="POST" action="{{ route('project-tasks.destroy', $task) }}" onsubmit="return confirm('Hapus task ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-500 hover:text-red-700 font-semibold hover:underline">Hapus</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center p-8 border border-dashed border-gray-200 rounded-xl bg-gray-50">
                                <p class="text-sm text-gray-400">Belum ada checklist tugas.</p>
                            </div>
                        @endif
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-md font-bold text-gray-900 border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                            ⏱️ Log Aktivitas Harian (Reminder Digital)
                        </h3>

                        @if($project->activities->count())
                            <div class="flow-root pl-2 mt-2">
                                <ul class="-mb-8">
                                    @foreach($project->activities as $activity)
                                        <li>
                                            <div class="relative pb-8">
                                                @if(!$loop->last)
                                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                                @endif
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span class="h-8 w-8 rounded-full bg-indigo-50 border border-indigo-200 flex items-center justify-center ring-8 ring-white text-sm">
                                                            📢
                                                        </span>
                                                    </div>
                                                    <div class="flex-1 min-w-0 pt-1.5 flex justify-between space-x-4">
                                                        <div>
                                                            <p class="text-sm font-bold text-gray-900">{{ $activity->title }}</p>
                                                            <p class="text-xs text-gray-500 mt-0.5">{{ $activity->description }}</p>
                                                            <span class="text-[11px] font-semibold text-gray-400 mt-1 block">Oleh: {{ $activity->user?->name ?? 'User Terhapus' }}</span>
                                                        </div>
                                                        <div class="text-right text-xs whitespace-nowrap text-gray-400 font-medium">
                                                            <time>{{ \Carbon\Carbon::parse($activity->activity_date)->format('d M Y') }}</time>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <p class="text-sm text-gray-400 italic text-center py-4 bg-gray-50 rounded-lg border border-dashed border-gray-200">Belum ada log aktivitas terekam.</p>
                        @endif
                    </div>

                    <div class="pt-2">
                        <a href="{{ route('projects.index') }}" class="inline-flex items-center text-sm font-bold text-indigo-600 hover:text-indigo-800 transition">
                            ← Kembali ke daftar semua project
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>