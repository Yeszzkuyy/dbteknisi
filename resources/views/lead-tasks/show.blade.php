<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">{{ $task->title }}</h1>
            <p class="text-slate-500 mt-1">{{ $task->lead?->customer?->name ?? '-' }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('lead-tasks.edit', $task) }}"
               class="px-4 py-2.5 rounded-xl bg-blue-100 hover:bg-blue-200 text-blue-700 text-sm font-medium transition">
                Edit
            </a>
            <a href="{{ route('lead-tasks.index') }}"
               class="px-4 py-2.5 rounded-xl bg-accent-500 text-white hover:bg-accent-600 text-sm font-medium transition">
                {{ __('Kembali') }}
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-6 mb-6">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <dt class="text-xs text-slate-400">{{ __('Status') }}</dt>
                <dd class="font-medium text-slate-800 dark:text-slate-100">{{ \App\Models\LeadTask::statusLabel($task->status) }}</dd>
            </div>
            <div>
                <dt class="text-xs text-slate-400">{{ __('Prioritas') }}</dt>
                <dd class="font-medium text-slate-800 dark:text-slate-100">{{ $task->priority ? ucfirst($task->priority) : '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-slate-400">{{ __('Assign ke') }}</dt>
                <dd class="font-medium text-slate-800 dark:text-slate-100">{{ $task->assignee?->name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-slate-400">{{ __('Jatuh Tempo') }}</dt>
                <dd class="font-medium text-slate-800 dark:text-slate-100">{{ $task->due_date ? $task->due_date->format('d M Y') : '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-slate-400">{{ __('Dibuat oleh') }}</dt>
                <dd class="font-medium text-slate-800 dark:text-slate-100">{{ $task->creator?->name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-slate-400">{{ __('Lead') }}</dt>
                <dd class="font-medium text-slate-800 dark:text-slate-100">
                    <a href="{{ route('leads.show', $task->lead) }}" class="text-accent-600 hover:underline">
                        {{ $task->lead?->customer?->name ?? '-' }}
                    </a>
                </dd>
            </div>
        </dl>

        <div>
            <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3">{{ __('Deskripsi') }}</h3>
            <p class="text-slate-700 dark:text-slate-200 whitespace-pre-wrap">{{ $task->description ?: '-' }}</p>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700">
            <form action="{{ route('lead-tasks.update', $task) }}" method="POST" class="flex flex-wrap items-end gap-3">
                @csrf @method('PUT')
                <input type="hidden" name="title" value="{{ $task->title }}">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Ubah Status') }}</label>
                    <select name="status"
                            class="rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">
                        @foreach(\App\Models\LeadTask::STATUSES as $status)
                            <option value="{{ $status }}" @selected($task->status === $status)>
                                {{ \App\Models\LeadTask::statusLabel($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                        class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition">
                    {{ __('Simpan Status') }}
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-6">
        <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3">{{ __('Komentar') }} ({{ $task->comments->count() }})</h3>
        <div class="space-y-2 mb-4">
            @forelse($task->comments->sortBy('created_at') as $comment)
                <div class="p-3 bg-slate-50 dark:bg-slate-700 rounded-xl">
                    <p class="text-sm text-slate-800 dark:text-slate-100 whitespace-pre-wrap">{{ $comment->body }}</p>
                    <p class="mt-1 text-xs text-slate-500">
                        {{ $comment->user?->name ?? 'System' }} •
                        {{ $comment->created_at?->setTimezone('Asia/Jakarta')->format('d M Y H:i') ?? '-' }}
                    </p>
                </div>
            @empty
                <p class="text-slate-500">{{ __('Belum ada komentar.') }}</p>
            @endforelse
        </div>
        <form action="{{ route('lead-tasks.comments.store', $task) }}" method="POST" class="flex gap-3">
            @csrf
            <input type="text" name="body" required maxlength="2000"
                   placeholder="{{ __('Tulis komentar...') }}"
                   class="flex-1 rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">
            <button type="submit"
                    class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition">
                {{ __('Kirim') }}
            </button>
        </form>
    </div>
</x-app-layout>
