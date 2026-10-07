<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 dark:text-slate-100">Product Knowledge Base</h1>
                <p class="text-slate-500 mt-1">{{ __('Import URL produk resmi, review, lalu publish untuk AI.') }}</p>
            </div>
            <x-icon-button as="a" icon="back" href="{{ route('knowledge-base.index') }}" title="Back" />
        </div>

        {{-- Import form --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700">
                <h2 class="text-lg font-bold text-slate-800 dark:text-slate-200">{{ __('Official Product URLs') }}</h2>
            </div>
            <form action="{{ route('product-knowledge.import') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <textarea name="urls" rows="4" required
                          placeholder="https://vendor.com/product/1&#10;https://vendor.com/product/2"
                          class="w-full px-4 py-2 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-transparent bg-white dark:bg-slate-700 dark:border-slate-600 dark:text-slate-100 font-mono text-sm">{{ old('urls') }}</textarea>
                @error('urls')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
                <div>
                    <button type="submit"
                            class="group relative inline-flex items-center gap-2 overflow-hidden rounded-xl bg-accent-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition-all duration-300 hover:scale-105 hover:bg-accent-500 hover:shadow-lg active:scale-95">
                        <span class="pointer-events-none absolute inset-0 -translate-x-full -skew-x-12 bg-gradient-to-r from-transparent via-white/50 to-transparent transition-transform duration-700 ease-out group-hover:translate-x-full" aria-hidden="true"></span>
                        {{ __('Import URLs') }}
                    </button>
                </div>
            </form>
        </div>

        {{-- Batch results --}}
        @if($batchSources->isNotEmpty() || $duplicateRows->isNotEmpty())
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-200">{{ __('Import Results') }}</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-slate-50 dark:bg-slate-700">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">URL</th>
                                <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">{{ __('Product') }}</th>
                                <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Brand</th>
                                <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Model</th>
                                <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-600">
                            @foreach($batchSources as $source)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                                    <td class="px-6 py-4 text-xs font-mono text-slate-600 dark:text-slate-300 break-all max-w-md">{{ $source->url }}</td>
                                    <td class="px-6 py-4 font-semibold text-slate-800 dark:text-slate-100">
                                        @if($source->product)
                                            <a href="{{ route('product-knowledge.show', $source->product) }}" class="text-accent-600 hover:text-accent-700">{{ $source->product->displayName() }}</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $source->product?->brand ?? '-' }}</td>
                                    <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $source->product?->model ?? '-' }}</td>
                                    <td class="px-6 py-4"><x-status-badge :color="match($source->status) { 'published' => 'green', 'success' => 'green', 'failed' => 'red', 'needs_review' => 'orange', 'duplicate' => 'slate', default => 'blue' }">{{ ucfirst(str_replace('_', ' ', $source->status)) }}</x-status-badge></td>
                                </tr>
                            @endforeach
                            @foreach($duplicateRows as $source)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                                    <td class="px-6 py-4 text-xs font-mono text-slate-600 dark:text-slate-300 break-all max-w-md">{{ $source->url }}</td>
                                    <td class="px-6 py-4 font-semibold text-slate-800 dark:text-slate-100">
                                        @if($source->product)
                                            <a href="{{ route('product-knowledge.show', $source->product) }}" class="text-accent-600 hover:text-accent-700">{{ $source->product->displayName() }}</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $source->product?->brand ?? '-' }}</td>
                                    <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $source->product?->model ?? '-' }}</td>
                                    <td class="px-6 py-4"><x-status-badge color="slate">Duplicate</x-status-badge></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Product list --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 flex flex-wrap items-center gap-3">
                <h2 class="text-lg font-bold text-slate-800 dark:text-slate-200 mr-auto">{{ __('Products') }}</h2>
                <form method="GET" action="{{ route('product-knowledge.index') }}" class="flex flex-wrap items-center gap-2">
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search brand / model / SKU"
                           class="px-4 py-2 border border-slate-300 rounded-xl text-sm bg-white dark:bg-slate-700 dark:border-slate-600 dark:text-slate-100" />
                    <select name="status" class="rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm text-slate-700 dark:text-slate-200">
                        <option value="">{{ __('All statuses') }}</option>
                        @foreach(['review', 'approved', 'published', 'rejected'] as $st)
                            <option value="{{ $st }}" {{ ($filters['status'] ?? '') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                    <x-icon-button icon="filter" type="submit" title="Filter" />
                    @if(!empty($filters['q']) || !empty($filters['status']) || !empty($filters['category']))
                        <x-icon-button as="a" icon="reset" href="{{ route('product-knowledge.index') }}" title="Reset" />
                    @endif
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50 dark:bg-slate-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">{{ __('Product') }}</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Brand</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">{{ __('Category') }}</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">{{ __('Sources') }}</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-600">
                        @forelse($products as $product)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                                <td class="px-6 py-4 font-semibold text-slate-800 dark:text-slate-100">
                                    <a href="{{ route('product-knowledge.show', $product) }}" class="text-accent-600 hover:text-accent-700">{{ $product->displayName() }}</a>
                                </td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $product->brand ?? '-' }}</td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $product->category ?? '-' }}</td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $product->sources_count }}</td>
                                <td class="px-6 py-4"><x-status-badge :color="match($product->status) { 'published' => 'green', 'approved' => 'blue', 'rejected' => 'red', default => 'orange' }">{{ ucfirst($product->status) }}</x-status-badge></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-16 text-center text-slate-400">{{ __('Belum ada produk. Paste URL resmi di atas.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4">{{ $products->links() }}</div>
        </div>
    </div>
</x-app-layout>
