<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\TechnicianSchedule;
use App\Models\User;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class TechnicianScheduleController extends Controller
{
    public function __construct(
        protected GoogleCalendarService $google
    ) {}

    public function events(Request $request)
    {
        $schedules = $this->schedulesForRange(
            $request->query('start'),
            $request->query('end'),
            $request->query('search'),
        );

        return response()->json($schedules->map(fn ($s) => [
            'id' => $s->id,
            'title' => $s->title,
            'description' => $s->description,
            'location' => $s->location,
            'project_id' => $s->project_id,
            'technician_user_id' => $s->technician_user_id,
            'reminder_minutes' => $s->reminder_minutes,
            'start' => $s->start_at->setTimezone(GoogleCalendarService::TIMEZONE)->format('Y-m-d\TH:i:s'),
            'end' => $s->end_at->setTimezone(GoogleCalendarService::TIMEZONE)->format('Y-m-d\TH:i:s'),
            'status' => $s->status,
            'sync_status' => $s->google_sync_status,
            'google_event_id' => $s->google_event_id,
            'technician' => $s->technician?->name,
            'project' => $s->project?->project_name,
            'customer' => $s->project?->customer?->name,
        ]));
    }

    public function jadwal(Request $request)
    {
        $technicians = User::orderBy('name')->get(['id', 'name']);
        $projects = Project::whereNotNull('project_name')->orderBy('project_name')->get(['id', 'project_name']);
        $connected = $this->google->isApiReachable();

        return view('teknisi.jadwal', compact('technicians', 'projects', 'connected'));
    }

    public function store(Request $request)
    {
        $data = $this->validateSchedule($request);

        $schedule = $this->google->createSchedule(auth()->user(), $data);

        return back()->with('success', 'Jadwal berhasil dibuat.')
            ->with('sync_message', $this->syncMessage($schedule));
    }

    public function update(Request $request, TechnicianSchedule $schedule)
    {
        if ($schedule->user_id !== auth()->id()) {
            abort(403);
        }

        $data = $this->validateSchedule($request);
        $schedule = $this->google->updateSchedule($schedule, $data);

        return back()->with('success', 'Jadwal berhasil diupdate.')
            ->with('sync_message', $this->syncMessage($schedule));
    }

    public function destroy(TechnicianSchedule $schedule)
    {
        if ($schedule->user_id !== auth()->id() && ! auth()->user()->can('manage-admin')) {
            abort(403);
        }

        $this->google->deleteSchedule($schedule);

        return back()->with('success', 'Jadwal berhasil dihapus.');
    }

    public function connect()
    {
        $state = Str::random(40);
        session(['google_oauth_state' => $state]);

        return redirect($this->google->getAuthUrl(auth()->user(), $state));
    }

    public function callback(Request $request)
    {
        if (! is_string($request->query('state'))
            || ! hash_equals((string) session('google_oauth_state'), $request->query('state'))) {
            abort(403, 'OAuth state tidak valid.');
        }

        $error = $request->query('error');
        if ($error) {
            return redirect()->route('teknisi.jadwal')->with('error', 'Koneksi Google dibatalkan: '.$error);
        }

        try {
            $this->google->handleCallback(auth()->user(), $request->query('code'));
        } catch (\Throwable $e) {
            return redirect()->route('teknisi.jadwal')
                ->with('error', $e->getMessage());
        }

        session()->forget('google_oauth_state');

        return redirect()->route('teknisi.jadwal')
            ->with('success', 'Google Calendar Terhubung.');
    }

    public function disconnect()
    {
        $this->google->disconnect(auth()->user());

        return back()->with('success', 'Koneksi Google Calendar diputuskan.');
    }

    private function schedulesForRange(?string $start, ?string $end, $search = null)
    {
        return TechnicianSchedule::with(['project.customer', 'technician'])
            ->when($start && $end, function ($q) use ($start, $end) {
                $q->where('start_at', '<', $end)->where('end_at', '>', $start);
            })
            ->when($search, fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhere('location', 'like', "%{$s}%")
                    ->orWhereHas('project', fn ($q) => $q->where('project_name', 'like', "%{$s}%"))
                    ->orWhereHas('technician', fn ($q) => $q->where('name', 'like', "%{$s}%"));
            }))
            ->orderBy('start_at')
            ->get();
    }

    private function validateSchedule(Request $request): array
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
            'technician_user_id' => 'nullable|exists:users,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'status' => 'required|in:'.implode(',', array_keys(TechnicianSchedule::STATUSES)),
            'reminder_minutes' => 'nullable|integer|min:5|max:1440',
        ], [
            'end_time.after' => 'Jam selesai harus setelah jam mulai.',
        ]);

        $tz = GoogleCalendarService::TIMEZONE;
        $validated['start_at'] = Carbon::parse(
            $validated['date'].' '.$validated['start_time'],
            $tz
        )->setTimezone(config('app.timezone'));

        $validated['end_at'] = Carbon::parse(
            $validated['date'].' '.$validated['end_time'],
            $tz
        )->setTimezone(config('app.timezone'));

        unset($validated['date'], $validated['start_time'], $validated['end_time']);

        return $validated;
    }

    private function syncMessage(TechnicianSchedule $schedule): string
    {
        return match ($schedule->google_sync_status) {
            'synced' => 'Jadwal tersinkron ke Google Calendar.',
            'syncing' => 'Jadwal disimpan, sinkronisasi Google sedang diproses.',
            'error' => 'Jadwal disimpan, tetapi sinkron Google gagal: '.$schedule->google_sync_error,
            default => 'Jadwal disimpan. Hubungkan Google Calendar agar tersinkron.',
        };
    }
}
