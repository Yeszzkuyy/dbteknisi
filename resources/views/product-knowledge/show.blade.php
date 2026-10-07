<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 max-w-[1100px] mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">{{ $product->displayName() }}</h1>
                <p class="text-sm text-slate-500 mt-1"><x-status-badge :color="match($product->status) { 'published' => 'green', 'approved' => 'blue', 'rejected' => 'red', default => 'orange' }">{{ ucfirst($product->status) }}</x-status-badge></p>
            </div>
            <div class="flex items-center gap-2">
                @if(!in_array($product->status, ['published', 'rejected'], true))
                    <form action="{{ route('product-knowledge.approve', $product) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" title="Approve & Publish" aria-label="Approve & Publish"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 hover:bg-green-200 text-green-700 transition-all duration-300 hover:scale-105 active:scale-95">
                            <x-icon name="check-circle" class="h-5 w-5" />
                        </button>
                    </form>
                    <form action="{{ route('product-knowledge.reject', $product) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" title="Reject" aria-label="Reject"
                                onclick="return confirm('{{ __('Tolak produk ini? Tidak masuk pencarian AI.') }}')"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 hover:bg-red-200 text-red-700 transition-all duration-300 hover:scale-105 active:scale-95">
                            <x-icon name="trash" class="h-5 w-5" />
                        </button>
                    </form>
                @endif
                <x-icon-button as="a" icon="back" href="{{ route('product-knowledge.index') }}" title="Back" />
            </div>
        </div>

        {{-- Facts --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3 text-sm">
                <div><dt class="font-semibold text-slate-500">Brand</dt><dd class="text-slate-800 dark:text-slate-100">{{ $product->brand ?? '-' }}</dd></div>
                <div><dt class="font-semibold text-slate-500">Model</dt><dd class="text-slate-800 dark:text-slate-100">{{ $product->model ?? '-' }}</dd></div>
                <div><dt class="font-semibold text-slate-500">SKU</dt><dd class="text-slate-800 dark:text-slate-100">{{ $product->sku ?? '-' }}</dd></div>
                <div><dt class="font-semibold text-slate-500">{{ __('Category') }}</dt><dd class="text-slate-800 dark:text-slate-100">{{ $product->category ?? '-' }}</dd></div>
            </dl>
            <p class="mt-4 text-sm text-slate-600 dark:text-slate-300 whitespace-pre-wrap">{{ $product->description ?? '-' }}</p>
        </section>

        {{-- Sources --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-3">{{ __('Sources') }}</h2>
            <div class="space-y-3">
                @forelse($product->sources as $source)
                    <div class="flex flex-wrap items-center gap-3 rounded-xl border border-slate-200 dark:border-slate-600 px-4 py-3">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-mono text-slate-500 break-all">{{ $source->url }}</p>
                            <p class="text-xs text-slate-400 mt-1">{{ $source->source_type }} · {{ __('fetch') }}: {{ $source->last_fetched_at?->format('d M Y H:i') ?? '-' }} · v{{ $source->latestDocument?->version ?? '-' }}</p>
                            @if($source->error)<p class="text-xs text-red-600 mt-1">{{ $source->error }}</p>@endif
                        </div>
                        <x-status-badge :color="match($source->status) { 'success' => 'green', 'failed' => 'red', 'needs_review' => 'orange', 'duplicate' => 'slate', default => 'blue' }">{{ ucfirst(str_replace('_', ' ', $source->status)) }}</x-status-badge>
                        <form action="{{ route('product-knowledge.refetch', $source) }}" method="POST">
                            @csrf
                            <button type="submit" title="Refetch" aria-label="Refetch"
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-700 transition-all duration-300 hover:scale-105 active:scale-95">
                                <x-icon name="restore" class="h-5 w-5" />
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">-</p>
                @endforelse
            </div>
        </section>

        {{-- Latest document --}}
        @php($doc = $product->documents->first())
        @if($doc)
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-3">{{ __('Specifications') }} (v{{ $doc->version }} · {{ $doc->status }})</h2>
                <div class="text-sm text-slate-600 dark:text-slate-300 whitespace-pre-wrap max-h-96 overflow-y-auto">{{ $doc->content }}</div>
            </section>
        @endif
    </div>
</x-app-layout>
