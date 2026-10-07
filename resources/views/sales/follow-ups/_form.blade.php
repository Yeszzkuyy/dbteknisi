{{-- Form tambah/edit follow-up: customer searchable + lead/meeting ikut customer (tanpa reload). --}}
@php($followUp = $followUp ?? null)
<form action="{{ $action }}" method="POST" data-ajax class="space-y-6"
      x-data="{
          customerId: {{ json_encode(old('customer_id', $customerId ?? null)) }},
          leadId: {{ json_encode(old('lead_id', $leadId ?? null)) }},
          meetingId: {{ json_encode(old('meeting_id', $meetingId ?? null)) }},
          leads: {{ json_encode($leadItems) }},
          meetings: {{ json_encode($meetingItems) }},
          str(v) { return (v === null || v === undefined || v === '') ? null : String(v); },
          init() {
              this.customerId = this.str(this.customerId);
              this.leadId = this.str(this.leadId);
              this.meetingId = this.str(this.meetingId);
          },
          get customerLeads() {
              return this.leads.filter((l) => String(l.customer_id) === String(this.customerId));
          },
          get customerMeetings() {
              return this.meetings.filter((m) => String(m.customer_id) === String(this.customerId));
          }
      }"
      @customer-picked.window="if ($event.detail.field === 'customer_id') { customerId = $event.detail.id; leadId = null; meetingId = null; }">
    @csrf
    @if(!empty($method)) @method($method) @endif

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Customer <span class="text-red-500">*</span></label>
        @include('sales._customer-picker', [
            'name' => 'customer_id',
            'options' => $customerOptions,
            'selected' => old('customer_id', $customerId ?? null),
            'placeholder' => __('Search customer...'),
            'required' => true,
            'hint' => __('Pick a customer first — changing it resets Lead and Meeting below.'),
        ])
        @error('customer_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
        </div>
        <p class="mt-1 text-xs text-slate-400">{{ __('Pick a customer first — changing it resets Lead and Meeting below.') }}</p>
        @error('customer_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Related Lead (optional)') }}</label>
        <div class="relative">
            <select name="lead_id" x-model="leadId" :disabled="!customerId"
                    class="w-full appearance-none rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500 disabled:opacity-60 pr-10 text-sm">
                <option value="">{{ __('No specific opportunity') }}</option>
                <template x-for="lead in customerLeads" :key="lead.id">
                    <option :value="lead.id" x-text="lead.label"></option>
                </template>
            </select>
            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6" /></svg>
            </span>
        </div>
        <p class="mt-1 text-xs text-slate-400">{{ __('Links this follow-up to an opportunity. Auto-linked when the customer has exactly one of your active leads.') }}</p>
        @error('lead_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Related Meeting (optional)') }}</label>
        <div class="relative">
            <select name="meeting_id" x-model="meetingId" :disabled="!customerId"
                    class="w-full appearance-none rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500 disabled:opacity-60 pr-10 text-sm">
                <option value="">{{ __('No specific meeting') }}</option>
                <template x-for="meeting in customerMeetings" :key="meeting.id">
                    <option :value="meeting.id" x-text="meeting.label"></option>
                </template>
            </select>
            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6" /></svg>
            </span>
        </div>
        <p class="mt-1 text-xs text-slate-400">{{ __('The earlier meeting this follow-up continues, if any.') }}</p>
        @error('meeting_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Contact Method') }}</label>
        <select name="type"
                class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">
            <option value="">{{ __('Select method') }}</option>
            @foreach(\App\Models\FollowUp::CONTACT_TYPES as $type)
                <option value="{{ $type }}" @selected(old('type', $followUp?->type) === $type)>
                    {{ \App\Models\FollowUp::typeLabel($type) }}
                </option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-slate-400">{{ __('How you reached the customer.') }}</p>
        @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Follow-up Notes') }} <span class="text-red-500">*</span></label>
        <textarea name="description" rows="4" required
                  placeholder="{{ __('What did you do or learn? Keep it short...') }}"
                  class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">{{ old('description', $followUp?->description ?? '') }}</textarea>
        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Follow-up Date') }}</label>
        <x-datepicker name="follow_up_date" value="{{ old('follow_up_date', isset($followUp) ? $followUp->follow_up_date?->format('Y-m-d') : date('Y-m-d')) }}"></x-datepicker>
        @error('follow_up_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Next Follow-up (optional)') }}</label>
        <x-datepicker name="next_follow_up_date" value="{{ old('next_follow_up_date', isset($followUp) && $followUp->next_follow_up_date ? $followUp->next_follow_up_date->format('Y-m-d') : '') }}"></x-datepicker>
        @error('next_follow_up_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="flex gap-3 pt-2">
        <button type="submit"
                class="group relative overflow-hidden px-6 py-2.5 rounded-xl bg-accent-600 hover:bg-accent-500 text-white font-medium transition-all duration-300 hover:scale-105 hover:shadow-lg hover:shadow-accent-500/40 hover:brightness-110 active:scale-95">
            <span class="pointer-events-none absolute inset-0 -translate-x-full -skew-x-12 bg-gradient-to-r from-transparent via-white/50 to-transparent transition-transform duration-700 ease-out group-hover:translate-x-full"></span>
            {{ $submitLabel }}
        </button>
        <a href="{{ route('sales.follow-ups.index') }}"
           class="group relative overflow-hidden px-6 py-2.5 rounded-xl bg-accent-500 hover:bg-accent-600 text-white font-medium transition-all duration-300 hover:scale-105 hover:shadow-lg active:scale-95">
            <span class="pointer-events-none absolute inset-0 -translate-x-full -skew-x-12 bg-gradient-to-r from-transparent via-white/50 to-transparent transition-transform duration-700 ease-out group-hover:translate-x-full"></span>
            {{ __('Cancel') }}
        </a>
    </div>
</form>
