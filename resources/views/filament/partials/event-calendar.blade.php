<div x-data="eventCalendar()" style="width:100%;margin-bottom:2rem">

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
                    :style="'min-height:70px;padding:6px;' +
                        (cell.day === null ? 'background:#1a2744;' : 'background:' + (cell.today ? '#1d3557' : '#1e293b') + ';') +
                        (cell.hasEvents ? 'cursor:pointer;' : 'cursor:default;')"
                    @mouseenter="cell.hasEvents && ($el.style.background='#253352')"
                    @mouseleave="cell.hasEvents && ($el.style.background = cell.today ? '#1d3557' : '#1e293b')"
                    @click="cell.hasEvents && selectDate(cell.date)"
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
                </div>
            </template>
        </div>
    </div>

    {{-- Modal --}}
    <div x-show="open" x-cloak style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:16px">
        <div style="position:fixed;inset:0;background:rgba(0,0,0,.65);backdrop-filter:blur(4px)" @click="open = false"></div>
        <div style="position:relative;width:100%;max-width:520px;border-radius:16px;background:#1e293b;box-shadow:0 25px 50px rgba(0,0,0,.6);border:1px solid rgba(255,255,255,.08);overflow:hidden"
            @click.stop
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100">

            <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid rgba(255,255,255,.08)">
                <div>
                    <p style="font-size:.7rem;color:#64748b;margin:0 0 2px" x-text="formatDate(selectedDate)"></p>
                    <h3 style="font-size:.875rem;font-weight:600;color:#f1f5f9;margin:0" x-text="selectedEvents.length + ' événement' + (selectedEvents.length > 1 ? 's' : '')"></h3>
                </div>
                <button @click="open = false" style="color:#64748b;background:none;border:none;cursor:pointer;font-size:1.5rem;line-height:1;padding:4px">&times;</button>
            </div>

            <div style="max-height:60vh;overflow-y:auto">
                <template x-for="ev in selectedEvents" :key="ev.id">
                    <div style="padding:16px 20px;border-bottom:1px solid rgba(255,255,255,.05)">
                        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:8px">
                            <span style="display:inline-flex;align-items:center;border-radius:9999px;padding:2px 10px;font-size:.7rem;font-weight:500"
                                :style="typeBadgeStyle(ev.type)"
                                x-text="ev.type.charAt(0).toUpperCase() + ev.type.slice(1)">
                            </span>
                            <span style="display:inline-flex;align-items:center;border-radius:9999px;padding:2px 10px;font-size:.7rem;font-weight:500"
                                :style="statusBadgeStyle(ev.status)"
                                x-text="{'published':'Publié','cancelled':'Annulé','draft':'Brouillon'}[ev.status] ?? ev.status">
                            </span>
                            <span style="display:inline-flex;align-items:center;border-radius:9999px;padding:2px 10px;font-size:.7rem;font-weight:500"
                                :style="ev.is_paid ? 'background:rgba(245,158,11,.2);color:#fcd34d' : 'background:rgba(34,197,94,.15);color:#86efac'"
                                x-text="ev.is_paid ? 'Payant · ' + Number(ev.price ?? 0).toLocaleString('fr-FR') + ' ' + (ev.currency ?? '') : 'Gratuit'">
                            </span>
                        </div>
                        <h4 style="font-size:.875rem;font-weight:600;color:#f1f5f9;margin:0 0 8px" x-text="ev.title"></h4>
                        <div style="font-size:.75rem;color:#94a3b8;display:flex;flex-direction:column;gap:4px;margin-bottom:12px">
                            <div x-text="'🕐 ' + ev.starts_at + (ev.ends_at ? ' — ' + ev.ends_at : '')"></div>
                            <div x-show="ev.location" x-text="'📍 ' + ev.location"></div>
                            <template x-if="ev.capacity">
                                <div style="display:flex;align-items:center;gap:8px">
                                    <span x-text="'👥 ' + ev.registered + ' / ' + ev.capacity + ' inscrits'"></span>
                                    <div style="flex:1;max-width:80px;height:4px;background:rgba(255,255,255,.1);border-radius:2px">
                                        <div style="height:4px;border-radius:2px"
                                            :style="'width:' + Math.min(Math.round(ev.registered/ev.capacity*100),100) + '%;background:' + (ev.registered/ev.capacity>=.9?'#ef4444':ev.registered/ev.capacity>=.7?'#f59e0b':'#22c55e')">
                                        </div>
                                    </div>
                                    <span :style="ev.registered/ev.capacity>=.9?'color:#ef4444':'color:#94a3b8'"
                                        x-text="Math.round(ev.registered/ev.capacity*100)+'%'">
                                    </span>
                                </div>
                            </template>
                        </div>
                        <div style="display:flex;gap:8px">
                            <a :href="ev.view_url" style="display:inline-flex;align-items:center;gap:6px;border-radius:8px;padding:6px 12px;font-size:.75rem;font-weight:500;background:rgba(255,255,255,.06);color:#cbd5e1;text-decoration:none">
                                👁 Voir
                            </a>
                            <a :href="ev.edit_url" style="display:inline-flex;align-items:center;gap:6px;border-radius:8px;padding:6px 12px;font-size:.75rem;font-weight:500;background:#e8580a;color:#fff;text-decoration:none">
                                ✏ Modifier
                            </a>
                        </div>
                    </div>
                </template>
            </div>
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
            open: false,
            selectedDate: '',
            selectedEvents: [],

            monthLabel() {
                return MONTHS[this.month - 1] + ' ' + this.year;
            },

            prevMonth() {
                if (this.month === 1) { this.month = 12; this.year--; }
                else { this.month--; }
            },

            nextMonth() {
                if (this.month === 12) { this.month = 1; this.year++; }
                else { this.month++; }
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

            selectDate(date) {
                var evts = BY_DATE[date] || [];
                if (!evts.length) return;
                this.selectedDate = date;
                this.selectedEvents = evts;
                this.open = true;
            },

            formatDate(d) {
                if (!d) return '';
                var dt = new Date(d + 'T00:00');
                return dt.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            },

            eventStyle(type) {
                var map = {
                    workshop:   'background:rgba(59,130,246,.25);color:#93c5fd;',
                    conference: 'background:rgba(139,92,246,.25);color:#c4b5fd;',
                    hackathon:  'background:rgba(34,197,94,.25);color:#86efac;',
                };
                return map[type] || 'background:rgba(232,88,10,.2);color:#fdba74;';
            },

            typeBadgeStyle(type) {
                var map = {
                    workshop:   'background:rgba(59,130,246,.2);color:#93c5fd;',
                    conference: 'background:rgba(139,92,246,.2);color:#c4b5fd;',
                    hackathon:  'background:rgba(34,197,94,.2);color:#86efac;',
                };
                return map[type] || 'background:rgba(232,88,10,.2);color:#fdba74;';
            },

            statusBadgeStyle(status) {
                var map = {
                    published: 'background:rgba(34,197,94,.2);color:#86efac;',
                    cancelled: 'background:rgba(239,68,68,.2);color:#fca5a5;',
                    draft:     'background:rgba(100,116,139,.2);color:#94a3b8;',
                };
                return map[status] || 'background:rgba(100,116,139,.2);color:#94a3b8;';
            },
        };
    };
})();
</script>
