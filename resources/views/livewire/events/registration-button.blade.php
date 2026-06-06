<div>
    @if ($isPast)
        {{-- Événement terminé --}}
        <button class="btn w-100 mb-2" disabled
                style="background:#f1f5f9;color:#94a3b8;cursor:not-allowed">
            <i class="fa-solid fa-flag-checkered me-1"></i> Événement terminé
        </button>

    @elseif ($isRegistered && $status === 'confirmed')
        {{-- Membre inscrit (confirmé) --}}
        <div class="d-flex align-items-center gap-2 mb-3 p-3"
             style="background:#f0fdf4;border-radius:.75rem;border:1px solid #bbf7d0">
            <i class="fa-solid fa-circle-check text-success" style="font-size:1.3rem"></i>
            <div>
                <div style="font-weight:600;color:#166534">Inscrit ✓</div>
                <div class="text-muted-2" style="font-size:.82rem">Votre place est confirmée</div>
            </div>
        </div>

        @if ($ticketNumber)
            <div class="mb-2 rounded-lg px-3 py-2 text-center"
                 style="background:#fef3c7;border:1px solid #fde68a">
                <div class="mb-1" style="font-size:.72rem;color:#92400e;font-weight:600;text-transform:uppercase;letter-spacing:.05em">
                    Numéro de ticket
                </div>
                <div style="font-family:monospace;font-size:1rem;font-weight:700;color:#78350f;letter-spacing:.08em">
                    {{ $ticketNumber }}
                </div>
            </div>
        @endif

        @if ($registrationId)
            <a href="{{ route('events.ical', $registrationId) }}"
               class="btn btn-ghost w-100 mb-2">
                <i class="fa-regular fa-calendar-plus me-1"></i> Télécharger iCal (.ics)
            </a>
        @endif

        @if ($registrationId)
            <form id="cancel-form-{{ $registrationId }}"
                  action="{{ route('events.cancel', $registrationId) }}"
                  method="POST" style="display:none">
                @csrf
                @method('DELETE')
            </form>
            <button type="button"
                    onclick="Swal.fire({
                        title: 'Annuler mon inscription ?',
                        text: 'Cette action est irréversible.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Oui, annuler',
                        cancelButtonText: 'Non, garder',
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#64748b',
                        reverseButtons: true
                    }).then(r => { if(r.isConfirmed) document.getElementById('cancel-form-{{ $registrationId }}').submit() })"
                    class="btn w-100"
                    style="color:#dc2626;font-size:.82rem;background:none;border:none;text-decoration:underline;padding:.25rem">
                Annuler mon inscription
            </button>
        @endif

    @elseif ($isRegistered && $status === 'waitlisted')
        {{-- En liste d'attente --}}
        <div class="d-flex align-items-center gap-2 mb-3 p-3"
             style="background:#fffbeb;border-radius:.75rem;border:1px solid #fde68a">
            <i class="fa-solid fa-clock" style="font-size:1.3rem;color:#d97706"></i>
            <div>
                <div style="font-weight:600;color:#92400e">En liste d'attente</div>
                <div class="text-muted-2" style="font-size:.82rem">
                    Vous serez notifié si une place se libère
                </div>
            </div>
        </div>

        @if ($registrationId)
            <form id="cancel-waitlist-form-{{ $registrationId }}"
                  action="{{ route('events.cancel', $registrationId) }}"
                  method="POST" style="display:none">
                @csrf
                @method('DELETE')
            </form>
            <button type="button"
                    onclick="Swal.fire({
                        title: 'Quitter la liste d\'attente ?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Oui, quitter',
                        cancelButtonText: 'Non',
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#64748b',
                        reverseButtons: true
                    }).then(r => { if(r.isConfirmed) document.getElementById('cancel-waitlist-form-{{ $registrationId }}').submit() })"
                    class="btn btn-outline-navy w-100"
                    style="font-size:.88rem">
                <i class="fa-solid fa-xmark me-1"></i> Quitter la liste d'attente
            </button>
        @endif

    @elseif (! auth()->check() && $guestRegistrationEnabled && ! $isFull)
        {{-- Non connecté — inscription invité disponible --}}
        <div class="mb-3" x-data="{ openGuestModal: false }">
            <a href="{{ route('login') }}"
               class="btn btn-brand w-100 mb-2">
                <i class="fa-brands fa-github me-1"></i> Se connecter pour s'inscrire
            </a>
            <div class="d-flex align-items-center my-2">
                <hr class="flex-grow-1" style="border-color:#e2e8f0">
                <span class="px-2" style="font-size:.78rem;color:#94a3b8">ou</span>
                <hr class="flex-grow-1" style="border-color:#e2e8f0">
            </div>
            <button @click="openGuestModal = true"
                    class="btn w-100"
                    style="background:#fff;border:1.5px solid #e2e8f0;color:#374151;font-size:.88rem;font-weight:500">
                <i class="fa-solid fa-user-plus me-1" style="color:#f97316"></i>
                Je ne suis pas membre, je veux participer
            </button>

            {{-- Modal inscription invité --}}
            <template x-teleport="body">
                <div x-show="openGuestModal"
                     x-cloak
                     style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.55);padding:1rem"
                     @keydown.escape.window="openGuestModal = false"
                     @click.self="openGuestModal = false">
                    <div style="background:#fff;border-radius:1rem;width:100%;max-width:500px;max-height:92vh;overflow-y:auto;box-shadow:0 25px 50px -12px rgba(0,0,0,.3)">
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:1.1rem 1.5rem;border-bottom:1px solid #f1f5f9;position:sticky;top:0;background:#fff;z-index:1">
                            <div>
                                <div style="font-weight:600;font-size:.95rem;color:#111827">Inscription sans compte</div>
                                <div style="font-size:.75rem;color:#94a3b8;margin-top:.1rem">Participez sans créer de compte</div>
                            </div>
                            <button @click="openGuestModal = false"
                                    style="background:none;border:none;cursor:pointer;color:#94a3b8;padding:.3rem;border-radius:.5rem;line-height:1"
                                    aria-label="Fermer">
                                <i class="fa-solid fa-xmark" style="font-size:1.1rem"></i>
                            </button>
                        </div>
                        <div style="padding:1.5rem">
                            @livewire('events.guest-registration-form', ['eventId' => $eventId], key('guest-form-' . $eventId))
                        </div>
                    </div>
                </div>
            </template>
        </div>

    @elseif (! auth()->check())
        {{-- Non connecté — inscription invité non disponible --}}
        <a href="{{ route('login') }}" class="btn btn-brand w-100 mb-2">
            <i class="fa-brands fa-github me-1"></i> Connectez-vous pour vous inscrire
        </a>

    @elseif (! $isFull)
        {{-- Membre — place disponible --}}
        <form action="{{ route('events.register', $eventId) }}" method="POST">
            @csrf
            <input type="hidden" name="event_id" value="{{ $eventId }}" />
            @if ($spotsLeft !== null)
                <div class="spots-label mb-2">
                    <span style="color:#64748b;font-size:.85rem">
                        <i class="fa-solid fa-user-group me-1 text-orange"></i>
                        {{ $spotsLeft }} place(s) restante(s)
                    </span>
                </div>
            @endif
            <button type="submit" class="btn btn-brand w-100 mb-2">
                <i class="fa-solid fa-ticket me-1"></i> S'inscrire maintenant
            </button>
        </form>

    @elseif ($isFull && $waitlistEnabled)
        {{-- Complet — liste d'attente disponible --}}
        <form action="{{ route('events.register', $eventId) }}" method="POST">
            @csrf
            <input type="hidden" name="event_id" value="{{ $eventId }}" />
            <button type="submit" class="btn btn-outline-navy w-100 mb-2">
                <i class="fa-solid fa-hourglass-half me-1"></i> Rejoindre la liste d'attente
            </button>
        </form>
        <p class="text-muted-2 text-center mb-0" style="font-size:.82rem">
            L'événement est complet. Vous serez notifié si une place se libère.
        </p>

    @else
        {{-- Complet sans liste d'attente --}}
        <button class="btn w-100 mb-2" disabled
                style="background:#f1f5f9;color:#94a3b8;cursor:not-allowed">
            <i class="fa-solid fa-ban me-1"></i> Complet
        </button>
    @endif

    @error('event_id')
        <div class="alert alert-danger mt-2" style="font-size:.85rem">
            <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $message }}
        </div>
    @enderror
</div>
