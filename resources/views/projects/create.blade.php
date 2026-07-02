<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Project') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('projects.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Project Name</label>
                        <input type="text" name="project_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Customer</label>
                        <select name="customer_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Account Manager</label>
                        <select name="account_manager_id" id="account_manager_id" class="form-control">
                            <option value="">-- Pilih Account Manager --</option>
                            @foreach($accountManagers as $am)
                                <option value="{{ $am->id }}">{{ $am->name }}</option> 
                                {{-- ⚠️ Catatan: Pastikan '$am->name' sesuai dengan nama kolom nama AM di tabel kamu (misal: 'name' atau 'nama') --}}
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Work Type</label>
                        <select name="work_type_id" id="work_type_id" class="form-control" required>
                            <option value="">-- Pilih Work Type --</option>
                            @foreach($workTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                {{-- ⚠️ Catatan: Pastikan '$type->name' sesuai dengan nama kolom jenis pekerjaan di tabel kamu --}}
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">PIC Engineer</label>
                        <select name="pic_engineer_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @foreach($engineers as $engineer)
                            <option value="{{ $engineer->id }}">{{ $engineer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Project Code</label>
                        <input type="text" name="project_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Quotation Number</label>
                        <input type="text" name="quotation_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="Open">Open</option>
                            <option value="On Progress">On Progress</option>
                            <option value="Done">Done</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                    </div>

                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                        Simpan
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>