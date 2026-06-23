<div
    x-data="{
        open: false,
        events: [],
        selectedDate: '',
        selectDate(date, evts) {
            if (!evts || evts.length === 0) return;
            this.selectedDate = date;
            this.events = evts;
            this.open = true;
        },
        closeModal() { this.open = false; }
    }"
    style="width:100%;margin-bottom:2rem"
>
    {{-- Calendar card --}}
    <div style="background:#1e293b;border-radius:12px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,.4)">

        {{-- Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
            <h2 style="font-size:.875rem;font-weight:600;color:#f1f5f9;margin:0">
                Calendrier des événements
            </h2>
            <div style="display:flex;align-items:center;gap:8px">
                <a href="{{ $prevUrl }}" style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;color:#94a3b8;background:rgba(255,255,255,.05);text-decoration:none;font-size:1rem">&lsaquo;</a>
                <span style="font-size:.875rem;font-weight:600;color:#cbd5e1;min-width:140px;text-align:center">
                    {{ $currentDate->translatedFormat('F Y') }}
                </span>
                <a href="{{ $nextUrl }}" style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;color:#94a3b8;background:rgba(255,255,255,.05);text-decoration:none;font-size:1rem">&rsaquo;</a>
            </div>
        </div>

        {{-- Day headers --}}
        <div style="display:grid;grid-template-columns:repeat(7,1fr);margin-bottom:4px">
            @foreach(['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'] as $dow)
                <div style="padding:4px 0;text-align:center;font-size:.7rem;font-weight:500;color:#64748b;text-transform:uppercase;letter-spacing:.05em">{{ $dow }}</div>
            @endforeach
        </div>

        {{-- Calendar grid --}}
        <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px;background:#0f172a;border-radius:8px;overflow:hidden">
            @foreach($calendarDays as $cell)
                @if($cell['day'] === null)
                    <div style="background:#1a2744;min-height:70px"></div>
                @else
                    @php
                        $hasEvents = !empty($cell['events']);
                        $cursor = $hasEvents ? 'pointer' : 'default';
                        $bg = $cell['today'] ? '#1d3557' : '#1e293b';
                        $hoverNote = $hasEvents ? "style='cursor:pointer'" : '';
                        $eventsJson = htmlspecialchars(json_encode(array_values($cell['events'])), ENT_QUOTES);
                    @endphp
                    <div
                        style="background:{{ $bg }};min-height:70px;padding:6px;cursor:{{ $cursor }};{{ $hasEvents ? 'transition:background .15s;' : '' }}"
                        @if($hasEvents)
                            @mouseenter="$el.style.background='#253352'"
                            @mouseleave="$el.style.background='{{ $bg }}'"
                            @click="selectDate('{{ $cell['date'] }}', {{ $eventsJson }})"
                        @endif
                    >
                        <span style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;font-size:.75rem;font-weight:{{ $cell['today'] ? '700' : '400' }};color:{{ $cell['today'] ? '#fff' : '#94a3b8' }};background:{{ $cell['today'] ? '#e8580a' : 'transparent' }};margin-bottom:4px">
                            {{ $cell['day'] }}
                        </span>
                        @foreach(array_slice($cell['events'], 0, 3) as $ev)
                            <div style="font-size:.65rem;border-radius:4px;padding:1px 4px;margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-weight:500;
                                background:{{ ['workshop'=>'rgba(59,130,246,.25)','conference'=>'rgba(139,92,246,.25)','hackathon'=>'rgba(34,197,94,.25)'][$ev['type']] ?? 'rgba(232,88,10,.2)' }};
                                color:{{ ['workshop'=>'#93c5fd','conference'=>'#c4b5fd','hackathon'=>'#86efac'][$ev['type']] ?? '#fdba74' }}"
                                title="{{ $ev['title'] }}">
                                {{ Str::limit($ev['title'], 16) }}
                            </div>
                        @endforeach
                        @if(count($cell['events']) > 3)
                            <div style="font-size:.6rem;color:#64748b">+{{ count($cell['events']) - 3 }}</div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    {{-- Modal --}}
    <div
        x-show="open"
        x-cloak
        style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:16px"
    >
        <div style="position:fixed;inset:0;background:rgba(0,0,0,.65);backdrop-filter:blur(4px)" @click="closeModal()"></div>
        <div
            style="position:relative;width:100%;max-width:520px;border-radius:16px;background:#1e293b;box-shadow:0 25px 50px rgba(0,0,0,.6);border:1px solid rgba(255,255,255,.08);overflow:hidden"
            @click.stop
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
        >
            {{-- Modal header --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid rgba(255,255,255,.08)">
                <div>
                    <p style="font-size:.7rem;color:#64748b;margin:0 0 2px" x-text="selectedDate ? new Date(selectedDate + 'T00:00').toLocaleDateString('fr-FR',{weekday:'long',day:'numeric',month:'long',year:'numeric'}) : ''"></p>
                    <h3 style="font-size:.875rem;font-weight:600;color:#f1f5f9;margin:0" x-text="events.length + ' événement' + (events.length > 1 ? 's' : '')"></h3>
                </div>
                <button @click="closeModal()" style="color:#64748b;background:none;border:none;cursor:pointer;padding:4px;border-radius:8px;font-size:1.25rem;line-height:1">&times;</button>
            </div>

            {{-- Modal body --}}
            <div style="max-height:60vh;overflow-y:auto">
                <template x-for="ev in events" :key="ev.id">
                    <div style="padding:16px 20px;border-bottom:1px solid rgba(255,255,255,.05)">
                        {{-- Badges --}}
                        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:8px">
                            <span style="display:inline-flex;align-items:center;border-radius:9999px;padding:2px 10px;font-size:.7rem;font-weight:500"
                                :style="'background:' + {'workshop':'rgba(59,130,246,.2)','conference':'rgba(139,92,246,.2)','hackathon':'rgba(34,197,94,.2)'}[ev.type] ?? 'rgba(232,88,10,.2)' + ';color:' + {'workshop':'#93c5fd','conference':'#c4b5fd','hackathon':'#86efac'}[ev.type] ?? '#fdba74'"
                                x-text="ev.type.charAt(0).toUpperCase() + ev.type.slice(1)">
                            </span>
                            <span style="display:inline-flex;align-items:center;border-radius:9999px;padding:2px 10px;font-size:.7rem;font-weight:500"
                                :style="'background:' + (ev.status==='published' ? 'rgba(34,197,94,.2)' : ev.status==='cancelled' ? 'rgba(239,68,68,.2)' : 'rgba(100,116,139,.2)') + ';color:' + (ev.status==='published' ? '#86efac' : ev.status==='cancelled' ? '#fca5a5' : '#94a3b8')"
                                x-text="ev.status==='published' ? 'Publié' : ev.status==='cancelled' ? 'Annulé' : 'Brouillon'">
                            </span>
                            <span style="display:inline-flex;align-items:center;border-radius:9999px;padding:2px 10px;font-size:.7rem;font-weight:500"
                                :style="ev.is_paid ? 'background:rgba(245,158,11,.2);color:#fcd34d' : 'background:rgba(34,197,94,.15);color:#86efac'"
                                x-text="ev.is_paid ? 'Payant · ' + (ev.price ?? 0).toLocaleString('fr-FR') + ' ' + (ev.currency ?? '') : 'Gratuit'">
                            </span>
                        </div>
                        {{-- Title --}}
                        <h4 style="font-size:.875rem;font-weight:600;color:#f1f5f9;margin:0 0 8px" x-text="ev.title"></h4>
                        {{-- Meta --}}
                        <div style="font-size:.75rem;color:#94a3b8;display:flex;flex-direction:column;gap:4px;margin-bottom:12px">
                            <div>🕐 <span x-text="ev.starts_at + (ev.ends_at ? ' — ' + ev.ends_at : '')"></span></div>
                            <div x-show="ev.location">📍 <span x-text="ev.location"></span></div>
                            <div x-show="ev.capacity" style="display:flex;align-items:center;gap:8px">
                                👥 <span x-text="ev.registered + ' / ' + ev.capacity + ' inscrits'"></span>
                                <div style="flex:1;max-width:80px;height:4px;background:rgba(255,255,255,.1);border-radius:2px">
                                    <div style="height:4px;border-radius:2px;transition:width .3s"
                                        :style="'width:' + Math.min(Math.round((ev.registered / ev.capacity) * 100), 100) + '%;background:' + (ev.registered/ev.capacity >= .9 ? '#ef4444' : ev.registered/ev.capacity >= .7 ? '#f59e0b' : '#22c55e')">
                                    </div>
                                </div>
                                <span :style="'color:' + (ev.registered/ev.capacity >= .9 ? '#ef4444' : '#94a3b8')" x-text="Math.round((ev.registered/ev.capacity)*100) + '%'"></span>
                            </div>
                        </div>
                        {{-- Actions --}}
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
