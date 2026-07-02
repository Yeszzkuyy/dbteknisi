<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Project List') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <a href="{{ route('projects.create') }}" class="inline-block mb-4 px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                    Tambah Project
                </a>

                <table class="min-w-full divide-y divide-gray-200 border">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Project</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">PIC</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($projects as $project)
                        <tr>
                            <td class="px-4 py-2">{{ $project->id }}</td>
                            <td class="px-4 py-2">
                                <a href="{{ route('projects.show', $project) }}" class="text-indigo-600 hover:underline">
                                    {{ $project->project_name }}
                                </a>
                            </td>
                            <td class="px-4 py-2">{{ $project->customer->name }}</td>
                            <td class="px-4 py-2">{{ $project->picEngineer->name }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">{{ $project->status }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>