<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Task — {{ $project->project_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('project-tasks.store', $project) }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Judul Task</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('title')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="description" id="description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700">Assigned To</label>
                        <select name="assigned_to" id="assigned_to"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Belum Ditentukan --</option>
                            @foreach($engineers as $engineer)
                                <option value="{{ $engineer->id }}" @selected(old('assigned_to') == $engineer->id)>
                                    {{ $engineer->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                        @if($engineers->isEmpty())
                            <p class="text-xs text-amber-600 mt-1">Belum ada user dengan role engineer/teknisi.</p>
                        @endif
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Pilih Status --</option>
                            <option value="Pending" @selected(old('status') === 'Pending')>Pending</option>
                            <option value="In Progress" @selected(old('status') === 'In Progress')>In Progress</option>
                            <option value="Done" @selected(old('status') === 'Done')>Done</option>
                            <option value="Cancelled" @selected(old('status') === 'Cancelled')>Cancelled</option>
                        </select>
                        @error('status')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('start_date')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="due_date" class="block text-sm font-medium text-gray-700">Tanggal Selesai (Target)</label>
                            <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('due_date')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                            Tambah Task
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