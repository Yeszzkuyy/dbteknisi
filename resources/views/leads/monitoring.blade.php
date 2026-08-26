<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Monitoring Tim Marketing</h1>
            <p class="text-slate-500 mt-1">Rekap lead dan aktivitas terakhir tiap anggota tim</p>
        </div>
        <a href="{{ route('leads.index') }}"
           class="px-4 py-2.5 rounded-xl bg-blue-500 text-white hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-sm font-medium transition">
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 overflow-x-auto">
        @if($summary->isEmpty())
            <p class="text-center text-sm text-slate-500 py-12">Belum ada anggota tim marketing.</p>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-500 border-b border-slate-200">
                        <th class="py-3 px-3 font-medium">Nama</th>
                        @foreach($statuses as $status)
                            <th class="py-3 px-3 font-medium text-center">{{ ucfirst($status) }}</th>
                        @endforeach
                        <th class="py-3 px-3 font-medium text-center">Total</th>
                        <th class="py-3 px-3 font-medium">Aktivitas Terakhir</th>
                        <th class="py-3 px-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($summary as $row)
                        <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                            <td class="py-3 px-3 font-semibold text-slate-800">{{ $row->user->name }}</td>
                            @foreach($statuses as $status)
                                <td class="py-3 px-3 text-center
                                    {{ $status === 'won' ? 'text-green-600' : ($status === 'lost' ? 'text-red-500' : 'text-slate-600') }}">
                                    {{ $row->counts[$status] }}
                                </td>
                            @endforeach
                            <td class="py-3 px-3 text-center font-bold text-blue-600">{{ $row->total }}</td>
                            <td class="py-3 px-3 text-slate-500">
                                {{ $row->lastActivityAt ? $row->lastActivityAt->diffForHumans() : 'Belum ada' }}
                            </td>
                            <td class="py-3 px-3 text-right">
                                <a href="{{ route('leads.activities', ['user' => $row->user->id]) }}"
                                   class="text-xs font-medium text-blue-600 hover:underline whitespace-nowrap">
                                    Lihat Aktivitas &rarr;
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-app-layout>
