@props([
    'projects' => [],
    'technicians' => [],
    'connected' => false,
    'canManage' => false,
    'oldData' => null,
])

@once
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('scheduleForm', () => ({
        mode: 'create',
        scheduleId: null,
        syncStatus: null,
        connected: @js($connected),
        canManage: @js($canManage),
        urls: @js([
            'store' => route('teknisi.schedules.store'),
            'update' => route('teknisi.schedules.update', ['schedule' => ':id']),
            'delete' => route('teknisi.schedules.destroy', ['schedule' => ':id']),
        ]),
        form: {
            title: '',
            project_id: '',
            technician_user_id: '',
            date: '',
            start_time: '',
            end_time: '',
            location: '',
            description: '',
            status: 'scheduled',
            reminder_minutes: '30',
        },
        detail: {
            title: '', customer: '', technician: '', location: '', description: '', date: '', time: '',
        },
        technicianEmail: '',

        init() {
            const old = @js($oldData);
            if (old) {
                Object.assign(this.form, old.form);
                this.mode = old.mode || 'create';
                this.scheduleId = old.schedule_id || null;
                if (old.schedule_id) this.setTechnician();
            }

            const self = this;
            window.teknisiSchedule = {
                openCreate: (date) => self.openCreate(date),
                openDetail: (ev) => self.openDetail(ev),
            };
        },

        jakartaDate(d) {
            return new Intl.DateTimeFormat('en-CA', { timeZone: 'Asia/Jakarta' }).format(d);
        },
        jakartaTime(d) {
            return new Intl.DateTimeFormat('en-GB', {
                timeZone: 'Asia/Jakarta', hour: '2-digit', minute: '2-digit', hour12: false,
            }).format(d);
        },
        defaultStart(d) {
            const t = this.jakartaTime(d);
            return t === '24:00' ? '00:00' : t;
        },

        openCreate(date) {
            if (!this.canManage) return;
            this.mode = 'create';
            this.scheduleId = null;
            const d = date instanceof Date ? date : new Date(date);
            this.form = {
                title: '', project_id: '', technician_user_id: '', date: this.jakartaDate(d),
                start_time: this.defaultStart(d), end_time: '',
                location: '', description: '', status: 'scheduled', reminder_minutes: '30',
            };
            this.technicianEmail = '';
            this.show();
        },

        fillForm(ev) {
            const start = new Date(ev.start);
            const end = new Date(ev.end || ev.start);
            this.scheduleId = ev.extendedProps.id;
            this.syncStatus = ev.extendedProps.sync_status || 'not_connected';
            this.form = {
                title: ev.title,
                project_id: ev.extendedProps.project_id || '',
                technician_user_id: ev.extendedProps.technician_user_id || '',
                date: this.jakartaDate(start),
                start_time: this.jakartaTime(start),
                end_time: this.jakartaTime(end),
                location: ev.extendedProps.location || '',
                description: ev.extendedProps.description || '',
                status: ev.extendedProps.status || 'scheduled',
                reminder_minutes: ev.extendedProps.reminder_minutes || '30',
            };
            this.setTechnician();
        },

        openEdit(ev) {
            if (!this.canManage) return;
            this.fillForm(ev);
            this.mode = 'edit';
            this.show();
        },

        openDetail(ev) {
            const start = new Date(ev.start);
            const end = new Date(ev.end || ev.start);
            const t = (d) => this.jakartaTime(d) === '24:00' ? '00:00' : this.jakartaTime(d);
            this.detail = {
                title: ev.title,
                customer: ev.extendedProps.customer || '',
                technician: ev.extendedProps.technician || '',
                location: ev.extendedProps.location || '',
                description: ev.extendedProps.description || '',
                date: new Intl.DateTimeFormat('id-ID', {
                    timeZone: 'Asia/Jakarta', weekday: 'long', day: 'numeric', month: 'long', year: 'numeric',
                }).format(start),
                time: `${t(start)} - ${t(end)}`,
            };
            this.fillForm(ev);
            this.mode = 'view';
            this.show();
        },

        toEdit() {
            this.mode = 'edit';
            this.show();
        },

        setTechnician() {
            const el = document.getElementById('schedule-technician');
            if (!el) return;
            const opt = el.selectedOptions[0];
            this.technicianEmail = opt ? opt.dataset.email || '' : '';
        },

        openEditFromList(data) {
            this.openEdit({
                title: data.title,
                start: data.start,
                end: data.end,
                extendedProps: {
                    id: data.id,
                    project_id: data.project_id,
                    technician_user_id: data.technician_user_id,
                    location: data.location,
                    description: data.description,
                    status: data.status,
                    reminder_minutes: data.reminder_minutes,
                    sync_status: data.sync_status,
                },
            });
        },

        formAction() {
            const base = this.mode === 'edit' ? this.urls.update : this.urls.store;
            return base.replace(':id', this.scheduleId);
        },
        deleteAction() {
            return this.urls.delete.replace(':id', this.scheduleId);
        },
        formMethod() {
            return this.mode === 'edit' ? 'PUT' : 'POST';
        },

        show() {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'schedule-modal' }));
        },
        close() {
            window.dispatchEvent(new CustomEvent('close-modal', { detail: 'schedule-modal' }));
        },
        syncLabel() {
            return {
                synced: '✓ Synced',
                syncing: '⟳ Syncing',
                error: '⚠ Sync Error',
                not_connected: '— Not Connected',
            }[this.syncStatus] || '';
        },
    }));
});
</script>
@endonce

<x-modal name="schedule-modal" maxWidth="2xl" :show="false">
    <div x-data="scheduleForm" class="max-h-[85vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800" x-text="mode === 'view' ? 'Detail Jadwal' : (mode === 'edit' ? 'Edit Jadwal' : 'Tambah Jadwal')"></h3>
            <button @click="close()" class="text-slate-400 hover:text-slate-600" type="button">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div x-show="mode === 'view'" class="px-6 py-5">
            <dl class="space-y-4 text-sm">
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Project</dt>
                    <dd class="mt-0.5 font-semibold text-slate-800" x-text="detail.title || '-'"></dd>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Customer</dt>
                        <dd class="mt-0.5 text-slate-700" x-text="detail.customer || '-'"></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Teknisi</dt>
                        <dd class="mt-0.5 text-slate-700" x-text="detail.technician || '-'"></dd>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Tanggal</dt>
                        <dd class="mt-0.5 text-slate-700" x-text="detail.date"></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Jam</dt>
                        <dd class="mt-0.5 text-slate-700" x-text="detail.time"></dd>
                    </div>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Lokasi</dt>
                    <dd class="mt-0.5 text-slate-700" x-text="detail.location || '-'"></dd>
                </div>
                <div x-show="detail.description">
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Deskripsi</dt>
                    <dd class="mt-0.5 text-slate-700" x-text="detail.description"></dd>
                </div>
            </dl>

            <div class="flex flex-wrap items-center gap-3 pt-5 mt-5 border-t border-slate-100">
                <button type="button" @click="toEdit()" x-show="canManage"
                        class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                    Edit
                </button>
                <form x-show="canManage" :action="deleteAction()" method="POST" class="inline"
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 font-medium transition text-sm">
                        Hapus
                    </button>
                </form>
                <button type="button" @click="close()" class="ml-auto px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition">
                    Tutup
                </button>
            </div>
        </div>

        <form x-show="mode !== 'view'" :action="formAction()" :method="formMethod()" class="px-6 py-5 space-y-4">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" :value="formMethod()">
            <input type="hidden" name="_schedule_mode" :value="mode">
            <input type="hidden" name="_schedule_id" :value="scheduleId">

            <div>
                <x-input-label for="schedule-title" value="Nama Project / Pekerjaan" />
                <x-text-input id="schedule-title" name="title" class="mt-1 w-full" x-model="form.title" placeholder="Instalasi CCTV PT ABC" required />
                <x-input-error :messages="$errors->get('title')" class="mt-1" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="schedule-project" value="Project" />
                    <select id="schedule-project" name="project_id" x-model="form.project_id"
                            class="mt-1 w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">— Pilih Project —</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('project_id')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="schedule-technician" value="Teknisi" />
                    <select id="schedule-technician" name="technician_user_id" x-model="form.technician_user_id"
                            @change="setTechnician()"
                            class="mt-1 w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">— Pilih Teknisi —</option>
                        @foreach($technicians as $technician)
                            <option value="{{ $technician->id }}" data-email="{{ $technician->email }}">{{ $technician->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('technician_user_id')" class="mt-1" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="schedule-date" value="Tanggal" />
                    <x-datepicker id="schedule-date" name="date" class="mt-1 w-full" x-model="form.date" required></x-datepicker>
                    <x-input-error :messages="$errors->get('date')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="schedule-start" value="Jam Mulai" />
                    <x-text-input id="schedule-start" type="time" name="start_time" class="mt-1 w-full" x-model="form.start_time" required />
                    <x-input-error :messages="$errors->get('start_time')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="schedule-end" value="Jam Selesai" />
                    <x-text-input id="schedule-end" type="time" name="end_time" class="mt-1 w-full" x-model="form.end_time" required />
                    <x-input-error :messages="$errors->get('end_time')" class="mt-1" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="schedule-location" value="Lokasi" />
                    <x-text-input id="schedule-location" name="location" class="mt-1 w-full" x-model="form.location" placeholder="Klaten" />
                    <x-input-error :messages="$errors->get('location')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="schedule-email" value="Email Teknisi" />
                    <x-text-input id="schedule-email" class="mt-1 w-full bg-slate-50" x-model="technicianEmail" readonly placeholder="Otomatis dari teknisi" />
                </div>
            </div>

            <div>
                <x-input-label for="schedule-description" value="Deskripsi" />
                <textarea id="schedule-description" name="description" rows="2" x-model="form.description"
                          class="mt-1 w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                          placeholder="Instalasi 8 kamera CCTV"></textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-1" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="schedule-status" value="Status" />
                    <select id="schedule-status" name="status" x-model="form.status"
                            class="mt-1 w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                        @foreach(\App\Models\TechnicianSchedule::STATUSES as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="schedule-reminder" value="Reminder" />
                    <select id="schedule-reminder" name="reminder_minutes" x-model="form.reminder_minutes"
                            class="mt-1 w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">Tanpa reminder popup</option>
                        <option value="30">30 menit sebelum</option>
                        <option value="60">1 jam sebelum</option>
                        <option value="120">2 jam sebelum</option>
                        <option value="1440">1 hari sebelum</option>
                    </select>
                    <p class="mt-1 text-xs text-slate-400">Email reminder 24 jam sebelum selalu dikirim.</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 pt-2">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                    Simpan Jadwal
                </button>
                <button type="button" @click="close()" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition">
                    Batal
                </button>
                <div class="ml-auto flex items-center gap-3">
                    <span x-show="mode === 'edit' && connected" x-text="syncLabel()" class="text-xs text-slate-500"></span>
                    <form x-show="mode === 'edit' && canManage" :action="deleteAction()" method="POST" class="inline"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 font-medium transition text-sm">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </form>
    </div>
</x-modal>
