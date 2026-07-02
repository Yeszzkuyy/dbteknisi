<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Upload Dokumen — {{ $project->project_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('project-documents.store', $project) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label for="document_category_id" class="block text-sm font-medium text-gray-700">Kategori Dokumen</label>
                        <select name="document_category_id" id="document_category_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('document_category_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('document_category_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                        @if($categories->isEmpty())
                            <p class="text-xs text-amber-600 mt-1">Belum ada kategori dokumen. <a href="{{ route('document-categories.create') }}" class="underline">Tambah kategori dulu</a>.</p>
                        @endif
                    </div>

                    <div>
                        <label for="file" class="block text-sm font-medium text-gray-700">File</label>
                        <input type="file" name="file" id="file"
                            class="mt-1 block w-full text-sm text-gray-700
                                   file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0
                                   file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700
                                   hover:file:bg-indigo-100">
                        <p class="text-xs text-gray-500 mt-1">Maksimal ukuran file 10MB.</p>
                        @error('file')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Catatan</label>
                        <textarea name="notes" id="notes" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                            Upload
                        </button>
                        <a href="{{ route('projects.show', $project) }}" class="text-sm text-gray-600 hover:underline">
                            Batal
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>