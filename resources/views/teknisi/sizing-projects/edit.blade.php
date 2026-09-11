<x-app-layout>
@php
    $statuses = [
        'draft' => 'Draft',
        'in_progress' => 'In Progress',
        'waiting_approval' => 'Waiting Approval',
        'completed' => 'Completed',
    ];
@endphp

<div class="px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 dark:text-slate-100">Edit Sizing Project</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1">{{ $sizingProject->project->project_name ?? 'Sizing Project' }}</p>
        </div>
        <a href="{{ route('teknisi.sizing-projects.show', $sizingProject) }}"
           class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-white dark:border-slate-600 dark:text-slate-200 rounded-xl text-sm">
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-xl bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-6">
        <form method="POST" action="{{ route('teknisi.sizing-projects.update', $sizingProject) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="project_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Project <span class="text-red-500">*</span></label>
                    <select name="project_id" id="project_id" required
                            class="mt-1 block w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">— Pilih Project —</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}"
                                @selected(old('project_id', $sizingProject->project_id) == $project->id)>
                                {{ $project->project_name }}
                                @if ($project->customer) — {{ $project->customer->name }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="sales_pic" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Sales PIC</label>
                    <input type="text" name="sales_pic" id="sales_pic" value="{{ old('sales_pic', $sizingProject->sales_pic) }}" maxlength="255"
                           class="mt-1 block w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label for="customer_needs" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Customer Needs</label>
                <textarea name="customer_needs" id="customer_needs" rows="3"
                          class="mt-1 block w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('customer_needs', $sizingProject->customer_needs) }}</textarea>
            </div>

            <div>
                <label for="recommendation" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Rekomendasi</label>
                <textarea name="recommendation" id="recommendation" rows="3"
                          class="mt-1 block w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('recommendation', $sizingProject->recommendation) }}</textarea>
            </div>

            <div>
                <label for="specifications" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Spesifikasi</label>
                <textarea name="specifications" id="specifications" rows="3"
                          class="mt-1 block w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('specifications', $sizingProject->specifications) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="quantity" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Quantity</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $sizingProject->quantity) }}" min="0"
                           class="mt-1 block w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Status</label>
                    <select name="status" id="status" required
                            class="mt-1 block w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100 text-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $sizingProject->status) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="topology" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Topologi</label>
                <textarea name="topology" id="topology" rows="3"
                          class="mt-1 block w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('topology', $sizingProject->topology) }}</textarea>
            </div>

            <div>
                <label for="technical_notes" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Catatan Teknis</label>
                <textarea name="technical_notes" id="technical_notes" rows="3"
                          class="mt-1 block w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('technical_notes', $sizingProject->technical_notes) }}</textarea>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Notes</label>
                <textarea name="notes" id="notes" rows="2"
                          class="mt-1 block w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $sizingProject->notes) }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('teknisi.sizing-projects.show', $sizingProject) }}"
                   class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-white dark:border-slate-600 dark:text-slate-200 rounded-xl text-sm">
                    Batal
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-medium">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
</x-app-layout>