<div x-data="eventCalendar()" style="width:100%;margin-bottom:2rem" @click.away="dropdown.open = false">

    {{-- Calendar card --}}
    <div style="background:#1e293b;border-radius:12px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,.4)">

        {{-- Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
            <h2 style="font-size:.875rem;font-weight:600;color:#f1f5f9;margin:0">
                Calendrier des événements
            </h2>
            <div style="display:flex;align-items:center;gap:8px">
                <button @click="prevMonth()" style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;color:#94a3b8;background:rgba(255,255,255,.05);border:none;cursor:pointer;font-size:1rem">&lsaquo;</button>
                <span style="font-size:.875rem;font-weight:600;color:#cbd5e1;min-width:150px;text-align:center" x-text="monthLabel()"></span>
                <button @click="nextMonth()" style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;color:#94a3b8;background:rgba(255,255,255,.05);border:none;cursor:pointer;font-size:1rem">&rsaquo;</button>
            </div>
        </div>

        {{-- Day headers --}}
        <div style="display:grid;grid-template-columns:repeat(7,1fr);margin-bottom:4px">
            <template x-for="d in ['Lun','Mar','Mer','Jeu','Ven','Sam','Dim']" :key="d">
                <div style="padding:4px 0;text-align:center;font-size:.7rem;font-weight:500;color:#64748b;text-transform:uppercase;letter-spacing:.05em" x-text="d"></div>
            </template>
        </div>

        {{-- Calendar grid --}}
        <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px;background:#0f172a;border-radius:8px;overflow:hidden">
            <template x-for="cell in calendarDays()" :key="cell.key">
                <div
                    style="position:relative"
                    :style="'min-height:70px;padding:6px;' +
                        (cell.day === null ? 'background:#1a2744;' : 'background:' + (cell.today ? '#1d3557' : '#1e293b') + ';') +
                        (cell.hasEvents ? 'cursor:pointer;' : 'cursor:default;')"
                    @mouseenter="cell.hasEvents && ($el.style.background='#253352')"
                    @mouseleave="cell.hasEvents && ($el.style.background = cell.today ? '#1d3557' : '#1e293b')"
                    @click.stop="cell.hasEvents && handleCellClick($event, cell)"
                >
                    <template x-if="cell.day !== null">
                        <div>
                            <span :style="'display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;font-size:.75rem;margin-bottom:4px;' +
                                (cell.today ? 'background:#e8580a;color:#fff;font-weight:700;' : 'color:#94a3b8;font-weight:400;background:transparent;')"
                                x-text="cell.day">
                            </span>
                            <template x-for="ev in cell.events.slice(0, 3)" :key="ev.id">
                                <div style="font-size:.65rem;border-radius:4px;padding:1px 4px;margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-weight:500"
                                    :style="eventStyle(ev.type)"
                                    :title="ev.title"
                                    x-text="ev.title.length > 16 ? ev.title.slice(0,16) + '…' : ev.title">
                                </div>
                            </template>
                            <div x-show="cell.events.length > 3" style="font-size:.6rem;color:#64748b" x-text="'+' + (cell.events.length - 3)"></div>
                        </div>
                    </template>

                    {{-- Dropdown inline (multi-events) --}}
                    <div
                        x-show="dropdown.open && dropdown.date === cell.date"
                        x-cloak
                        @click.stop
                        style="position:absolute;top:100%;left:0;z-index:100;min-width:260px;background:#0f172a;border:1px solid rgba(255,255,255,.1);border-radius:10px;box-shadow:0 12px 30px rgba(0,0,0,.5);overflow:hidden"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                    >
                        <div style="padding:8px 12px;border-bottom:1px solid rgba(255,255,255,.07);font-size:.7rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.05em"
                            x-text="formatDate(dropdown.date)"></div>
                        <template x-for="ev in dropdown.events" :key="ev.id">
                            <a :href="ev.view_url"
                               style="display:flex;align-items:center;gap:8px;padding:10px 12px;text-decoration:none;border-bottom:1px solid rgba(255,255,255,.05);transition:background .1s"
                               @mouseenter="$el.style.background='rgba(255,255,255,.04)'"
                               @mouseleave="$el.style.background='transparent'">
                                <span style="width:8px;height:8px;border-radius:50%;flex-shrink:0" :style="'background:' + dotColor(ev.type)"></span>
                                <div style="flex:1;min-width:0">
                                    <div style="font-size:.8rem;font-weight:500;color:#e2e8f0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" x-text="ev.title"></div>
                                    <div style="font-size:.7rem;color:#64748b;margin-top:1px" x-text="ev.starts_at + (ev.ends_at ? ' – ' + ev.ends_at : '')"></div>
                                </div>
                                <svg style="width:14px;height:14px;color:#475569;flex-shrink:0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
(function() {
    var BY_DATE = @json($byDate);
    var TODAY   = @json($today);
    var MONTHS  = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];

    window.eventCalendar = function() {
        return {
            month: {{ $initMonth }},
            year:  {{ $initYear }},
            dropdown: { open: false, date: '', events: [] },

            monthLabel() {
                return MONTHS[this.month - 1] + ' ' + this.year;
            },

            prevMonth() {
                if (this.month === 1) { this.month = 12; this.year--; }
                else { this.month--; }
                this.dropdown.open = false;
            },

            nextMonth() {
                if (this.month === 12) { this.month = 1; this.year++; }
                else { this.month++; }
                this.dropdown.open = false;
            },

            calendarDays() {
                var days = [];
                var firstDay = new Date(this.year, this.month - 1, 1);
                var lastDate = new Date(this.year, this.month, 0).getDate();
                var firstDow = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1;

                for (var i = 0; i < firstDow; i++) {
                    days.push({ key: 'pad-' + i, day: null, date: '', today: false, hasEvents: false, events: [] });
                }

                for (var d = 1; d <= lastDate; d++) {
                    var mm = String(this.month).padStart(2, '0');
                    var dd = String(d).padStart(2, '0');
                    var date = this.year + '-' + mm + '-' + dd;
                    var evts = BY_DATE[date] || [];
                    days.push({ key: date, day: d, date: date, today: date === TODAY, hasEvents: evts.length > 0, events: evts });
                }
                return days;
            },

            // Clic sur une cellule : navigation directe si 1 seul event, dropdown si plusieurs
            handleCellClick(evt, cell) {
                var evts = BY_DATE[cell.date] || [];
                if (!evts.length) return;

                if (evts.length === 1) {
                    // Navigation directe → page détail admin
                    window.location.href = evts[0].view_url;
                    return;
                }

                // Plusieurs événements → dropdown inline
                if (this.dropdown.open && this.dropdown.date === cell.date) {
                    this.dropdown.open = false;
                } else {
                    this.dropdown.date   = cell.date;
                    this.dropdown.events = evts;
                    this.dropdown.open   = true;
                }
            },

            formatDate(d) {
                if (!d) return '';
                var dt = new Date(d + 'T00:00');
                return dt.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            },

            dotColor(type) {
                var map = {
                    workshop:   '#60a5fa',
                    conference: '#a78bfa',
                    hackathon:  '#4ade80',
                    webinar:    '#38bdf8',
                };
                return map[type] || '#fdba74';
            },

            eventStyle(type) {
                var map = {
                    workshop:   'background:rgba(59,130,246,.25);color:#93c5fd;',
                    conference: 'background:rgba(139,92,246,.25);color:#c4b5fd;',
                    hackathon:  'background:rgba(34,197,94,.25);color:#86efac;',
                    webinar:    'background:rgba(56,189,248,.2);color:#7dd3fc;',
                };
                return map[type] || 'background:rgba(232,88,10,.2);color:#fdba74;';
            },
        };
    };
})();
</script>
