<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kategori Dokumen
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <a href="{{ route('document-categories.create') }}"
                    class="inline-block mb-6 px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                    + Tambah Kategori
                </a>

                @if($categories->count())
                <table class="min-w-full divide-y divide-gray-200 border">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Kategori</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($categories as $category)
                        <tr class="{{ $category->trashed() ? 'bg-gray-50 text-gray-400' : '' }}">
                            <td class="px-4 py-2">{{ $category->name }}</td>
                            <td class="px-4 py-2">
                                @if($category->trashed())
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-600">Dihapus</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Aktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                @if($category->trashed())
                                    <form method="POST" action="{{ route('document-categories.restore', $category->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-indigo-600 hover:underline text-sm">Restore</button>
                                    </form>
                                @else
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('document-categories.edit', $category) }}"
                                            class="text-indigo-600 hover:underline text-sm">Edit</a>
                                        <form method="POST" action="{{ route('document-categories.destroy', $category) }}"
                                            onsubmit="return confirm('Hapus kategori ini? Dokumen yang sudah pakai kategori ini tidak akan terpengaruh.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline text-sm">Hapus</button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p class="text-gray-500 text-sm">Belum ada kategori dokumen.</p>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>