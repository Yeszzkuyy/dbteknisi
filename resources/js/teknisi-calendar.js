import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import idLocale from '@fullcalendar/core/locales/id';

document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('teknisi-calendar');
    if (!el) return;

    const eventsUrl = el.dataset.eventsUrl;

    const palette = [
        '#2563eb', '#059669', '#d97706', '#dc2626', '#7c3aed',
        '#0d9488', '#db2777', '#4f46e5', '#ea580c', '#0891b2',
    ];
    const colorForTechnician = (id) => {
        if (!id) return '#475569';
        let h = 0;
        for (const c of String(id)) h = (h * 31 + c.charCodeAt(0)) >>> 0;
        return palette[h % palette.length];
    };
    const fmtTime = (d) => new Intl.DateTimeFormat('id-ID', {
        timeZone: 'Asia/Jakarta', hour: '2-digit', minute: '2-digit', hour12: false,
    }).format(d);
    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
    }[c]));

    const calendar = new Calendar(el, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        locale: idLocale,
        timeZone: 'Asia/Jakarta',
        initialView: 'dayGridMonth',
        firstDay: 1,
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay addButton',
        },
        buttonText: {
            today: 'Hari Ini',
            month: 'Bulan',
            week: 'Minggu',
            day: 'Hari',
        },
        customButtons: {
            addButton: {
                text: '+ Tambah Jadwal',
                click: () => window.teknisiSchedule.openCreate(new Date()),
            },
        },
        events: [
            (info, successCallback, failureCallback) => {
                const params = new URLSearchParams({
                    start: info.startStr,
                    end: info.endStr,
                    search: document.getElementById('filter-search')?.value || '',
                });
                fetch(`${eventsUrl}?${params.toString()}`)
                    .then((r) => r.json())
                    .then((events) => successCallback(events.map((e) => ({
                        ...e,
                        backgroundColor: colorForTechnician(e.technician_user_id),
                        borderColor: colorForTechnician(e.technician_user_id),
                        textColor: '#ffffff',
                    }))))
                    .catch(failureCallback);
            },
            {
                url: '/teknisi/jadwal/google-events',
                method: 'GET',
                failure: function (err) {
                    console.error('Google Calendar gagal dimuat', err);
                },
            },
        ],
        eventDisplay: 'block',
        eventContent: (arg) => {
            const ev = arg.event;
            const start = fmtTime(ev.start);
            const end = ev.end ? fmtTime(ev.end) : start;
            const tech = ev.extendedProps.technician ? `<div class="fc-event-line">Teknisi: ${esc(ev.extendedProps.technician)}</div>` : '';
            const cust = ev.extendedProps.customer ? `<div class="fc-event-line">Customer: ${esc(ev.extendedProps.customer)}</div>` : '';
            return {
                html: `<div class="fc-event-card">
                            <div class="fc-event-time">${start} - ${end}</div>
                            <div class="fc-event-title-text">${esc(ev.title)}</div>
                            ${cust}${tech}
                        </div>`,
            };
        },
        dateClick: (info) => window.teknisiSchedule.openCreate(info.date),
        eventClick: (info) => window.teknisiSchedule.openDetail(info.event),
        eventDidMount: (info) => {
            const el = info.el;
            const sync = info.event.extendedProps.sync_status;
            if (sync === 'error') el.style.border = '1.5px dashed #dc2626';
            if (sync === 'not_connected') el.classList.add('fc-event-not-connected');
        },
        dayMaxEvents: 3,
        moreLinkContent: (arg) => `+${arg.num} jadwal lainnya`,
    });

    calendar.render();

    window.teknisiCalendar = { calendar };
});
