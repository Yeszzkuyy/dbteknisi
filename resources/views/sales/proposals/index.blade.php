<x-app-layout>
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 dark:text-slate-100">{{ __('Proposal') }}</h1>
                <p class="text-slate-500 mt-1">{{ __('Daftar penawaran sales.') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <x-icon-button as="a" icon="back" href="{{ route('sales.dashboard') }}" title="{{ __('Back') }}" />
                <x-icon-button as="a" icon="add" href="{{ route('sales.proposals.create') }}" title="{{ __('Add') }}" />
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-4">
            @forelse($proposals as $proposal)
                <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 {{ !$loop->last ? 'border-b border-slate-100 dark:border-slate-700' : '' }}">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-800 dark:text-slate-100 truncate">{{ $proposal->proposal_number }} • {{ $proposal->customer }}</p>
                        <p class="text-xs text-slate-500">
                            Rp {{ number_format($proposal->grand_total, 0, ',', '.') }}
                            • {{ $proposal->lead->customer?->name ?? '' }}
                        </p>
                    </div>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold shrink-0 bg-slate-200 text-slate-700 dark:bg-slate-600 dark:text-slate-200">
                        {{ \App\Models\Proposal::statusLabel($proposal->status) }}
                    </span>
                    <a href="{{ route('sales.proposals.show', $proposal) }}" title="{{ __('View details') }}" aria-label="{{ __('View details') }}"
                       class="h-10 w-10 inline-flex items-center justify-center rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                    </a>
                </div>
            @empty
                <p class="text-slate-500 text-center py-8">{{ __('Belum ada proposal.') }}</p>
            @endforelse
            <div class="mt-4">{{ $proposals->links() }}</div>
        </div>
    </div>
</x-app-layout>
