<?php

namespace App\Services;

use App\Models\GoogleCredential;
use App\Models\TechnicianSchedule;
use App\Models\User;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use Google\Service\Calendar\EventReminder;
use Google\Service\Calendar\EventReminders;
use Illuminate\Support\Carbon;

class GoogleCalendarService
{
    public const TIMEZONE = 'Asia/Jakarta';

    private const STATUS_COLOR = [
        'scheduled' => 1,
        'on_progress' => 5,
        'completed' => 10,
        'cancelled' => 11,
    ];

    public function clientFor(?User $user = null): Client
    {
        $client = new Client;
        $client->setApplicationName(config('app.name'));
        $client->setClientId(config('services.google-calendar.client_id'));
        $client->setClientSecret(config('services.google-calendar.client_secret'));
        $client->setRedirectUri(config('services.google-calendar.redirect_uri') ?: url('/teknisi/kalender/callback'));
        $client->setScopes([Calendar::CALENDAR_EVENTS]);
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        if ($user) {
            $this->restoreTokens($client, $user);
        }

        return $client;
    }

    public function isConnected(User $user): bool
    {
        return GoogleCredential::where('user_id', $user->id)->exists();
    }

    public function getAuthUrl(User $user, string $state): string
    {
        $client = $this->clientFor();
        $client->setState($state);

        return $client->createAuthUrl();
    }

    public function handleCallback(User $user, string $code): void
    {
        $client = $this->clientFor();
        $token = $client->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            throw new \RuntimeException($token['error_description'] ?? 'Google OAuth gagal.');
        }

        GoogleCredential::updateOrCreate(['user_id' => $user->id], [
            'access_token' => $token['access_token'],
            'refresh_token' => $token['refresh_token'] ?? null,
            'expires_at' => isset($token['expires_in'])
                ? now()->addSeconds($token['expires_in'])
                : null,
            'calendar_id' => 'primary',
        ]);
    }

    public function disconnect(User $user): void
    {
        GoogleCredential::where('user_id', $user->id)->delete();

        TechnicianSchedule::where('user_id', $user->id)
            ->where('google_sync_status', '!=', 'not_connected')
            ->update([
                'google_sync_status' => 'not_connected',
                'google_sync_error' => null,
            ]);
    }

    /**
     * Simpan jadwal ke database lalu buat event di Google Calendar.
     */
    public function createSchedule(User $user, array $data): TechnicianSchedule
    {
        $schedule = new TechnicianSchedule(array_merge($data, [
            'user_id' => $user->id,
            'google_sync_status' => $this->isConnected($user) ? 'syncing' : 'not_connected',
        ]));
        $schedule->save();

        if ($this->isConnected($user)) {
            $this->pushEvent($schedule);
        }

        return $schedule;
    }

    /**
     * Update database lalu sinkronkan perubahan ke Google Calendar
     * (pakai google_event_id, tidak membuat event baru).
     */
    public function updateSchedule(TechnicianSchedule $schedule, array $data): TechnicianSchedule
    {
        $schedule->update(array_merge($data, [
            'google_sync_status' => $schedule->google_sync_status === 'not_connected'
                ? 'not_connected'
                : 'syncing',
        ]));

        if ($schedule->google_sync_status !== 'not_connected') {
            $this->pushEvent($schedule);
        }

        return $schedule;
    }

    public function deleteSchedule(TechnicianSchedule $schedule): void
    {
        if ($schedule->google_event_id && $schedule->google_sync_status !== 'not_connected') {
            try {
                $calendar = $this->calendarService($schedule->owner);
                $calendar->events->delete($schedule->google_calendar_id ?? 'primary', $schedule->google_event_id);
            } catch (\Throwable $e) {
                // jangan gagalkan hapus lokal karena error google
            }
        }

        $schedule->delete();
    }

    /**
     * Buat atau update event Google Calendar. Error tidak di-throw ke caller,
     * status sinkronisasi disimpan di baris jadwal.
     */
    private function pushEvent(TechnicianSchedule $schedule): void
    {
        try {
            $calendar = $this->calendarService($schedule->owner);
            $event = new Event($this->buildPayload($schedule));

            if ($schedule->google_event_id) {
                try {
                    $created = $calendar->events->update(
                        $schedule->google_calendar_id ?? 'primary',
                        $schedule->google_event_id,
                        $event
                    );
                } catch (\Throwable $e) {
                    if ($e->getCode() !== 404) {
                        throw $e;
                    }
                    // event sudah dihapus di Google → buat ulang
                    $schedule->google_event_id = null;
                    $created = $calendar->events->insert('primary', $event);
                }
            } else {
                $created = $calendar->events->insert('primary', $event);
            }

            $schedule->forceFill([
                'google_event_id' => $created->getId(),
                'google_calendar_id' => 'primary',
                'google_sync_status' => 'synced',
                'google_sync_error' => null,
            ])->save();
        } catch (\Throwable $e) {
            $schedule->forceFill([
                'google_sync_status' => 'error',
                'google_sync_error' => $this->friendlyError($e),
            ])->save();
        }
    }

    private function buildPayload(TechnicianSchedule $schedule): array
    {
        $payload = [
            'summary' => '[TEKNISI] '.$schedule->title,
            'description' => $this->buildDescription($schedule),
            'location' => $schedule->location,
            'colorId' => self::STATUS_COLOR[$schedule->status] ?? null,
            'start' => $this->eventDateTime($schedule->start_at),
            'end' => $this->eventDateTime($schedule->end_at),
            'reminders' => $this->buildReminders($schedule),
        ];

        if ($schedule->technician?->email) {
            $payload['attendees'] = [
                ['email' => $schedule->technician->email, 'displayName' => $schedule->technician->name],
            ];
        }

        return $payload;
    }

    private function buildDescription(TechnicianSchedule $schedule): string
    {
        $lines = [
            'Project: '.($schedule->project?->project_name ?? '-'),
            'Customer: '.($schedule->project?->customer?->company_name ?? '-'),
            'Teknisi: '.($schedule->technician?->name ?? '-'),
            'Status: '.(TechnicianSchedule::STATUSES[$schedule->status] ?? $schedule->status),
        ];

        if ($schedule->description) {
            $lines[] = '';
            $lines[] = $schedule->description;
        }

        return implode("\n", $lines);
    }

    private function eventDateTime(Carbon $carbon): EventDateTime
    {
        $dt = new EventDateTime;
        $dt->setDateTime($carbon->copy()->setTimezone(self::TIMEZONE)->format('Y-m-d\TH:i:s'));
        $dt->setTimeZone(self::TIMEZONE);

        return $dt;
    }

    private function buildReminders(TechnicianSchedule $schedule): EventReminders
    {
        $reminders = new EventReminders;
        $reminders->setUseDefault(false);

        $popup = $schedule->reminder_minutes ?: 30;
        $reminders->setOverrides([
            (new EventReminder)->setMethod('email')->setMinutes(1440),
            (new EventReminder)->setMethod('popup')->setMinutes(min($popup, 1440)),
        ]);

        return $reminders;
    }

    private function calendarService(User $user): Calendar
    {
        $client = $this->clientFor($user);

        if (! $client->getAccessToken()) {
            throw new \RuntimeException('Google Calendar belum terhubung.');
        }

        try {
            return new Calendar($client);
        } catch (\Throwable $e) {
            throw new \RuntimeException('Koneksi Google Calendar gagal, coba hubungkan ulang.');
        }
    }

    private function restoreTokens(Client $client, User $user): void
    {
        $credential = GoogleCredential::where('user_id', $user->id)->first();

        if (! $credential) {
            return;
        }

        $client->setAccessToken([
            'access_token' => $credential->access_token,
            'refresh_token' => $credential->refresh_token,
            'created' => ($credential->expires_at?->getTimestamp() ?? now()->timestamp) - 3600,
            'expires_in' => 3600,
        ]);

        // simpan otomatis token hasil refresh agar tidak kedaluwarsa
        $client->setTokenCallback(function ($cacheKey, $token) use ($credential) {
            $credential->update([
                'access_token' => $token['access_token'] ?? $credential->access_token,
                'refresh_token' => $token['refresh_token'] ?? $credential->refresh_token,
                'expires_at' => isset($token['expires_in'])
                    ? now()->addSeconds($token['expires_in'])
                    : $credential->expires_at,
            ]);
        });
    }

    private function friendlyError(\Throwable $e): string
    {
        $message = $e->getMessage();

        if (str_contains($message, 'invalid_grant') || str_contains($message, 'Invalid Credentials')) {
            return 'Izin Google Calendar kedaluwarsa, hubungkan kembali.';
        }

        if ($e->getCode() === 401) {
            return 'Token Google kedaluwarsa, coba hubungkan kembali.';
        }

        return 'Gagal sinkron ke Google Calendar: '.$message;
    }
}
