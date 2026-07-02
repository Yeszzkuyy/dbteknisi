<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Trash
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Customers Terhapus</h2>

                @if($customers->count())
                <table class="min-w-full divide-y divide-gray-200 border">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dihapus Pada</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($customers as $customer)
                        <tr>
                            <td class="px-4 py-2">{{ $customer->name }}</td>
                            <td class="px-4 py-2">{{ $customer->deleted_at ? $customer->deleted_at->format('d M Y, H:i') : '-' }}</td>
                            <td class="px-4 py-2">
                                <form method="POST" action="{{ route('trash.restore-customer', $customer->id) }}"
                                    onsubmit="return confirm('Restore customer ini? Project dan kontak terkait juga akan ikut direstore.')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-indigo-600 hover:underline">Restore</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p class="text-gray-500 text-sm">Tidak ada customer di trash</p>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Projects Terhapus</h2>

                @if($projects->count())
                <table class="min-w-full divide-y divide-gray-200 border">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Project</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dihapus Pada</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($projects as $project)
                        <tr>
                            <td class="px-4 py-2">{{ $project->project_name }}</td>
                            <td class="px-4 py-2">{{ $project->deleted_at ? $project->deleted_at->format('d M Y, H:i') : '-' }}</td>
                            <td class="px-4 py-2">
                                <form method="POST" action="{{ route('trash.restore-project', $project->id) }}"
                                    onsubmit="return confirm('Restore project ini? Dokumen, task, dan support terkait juga akan ikut direstore.')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-indigo-600 hover:underline">Restore</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p class="text-gray-500 text-sm">Tidak ada project di trash</p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>