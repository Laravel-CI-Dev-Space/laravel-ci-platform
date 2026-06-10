@extends('layouts.public')

@section('title', $event->title)

@push('head')
    <meta name="description" content="{{ str(strip_tags($event->description))->limit(160) }}">
    <meta property="og:title" content="{{ $event->title }} — Laravel CI">
    <meta property="og:description" content="{{ str(strip_tags($event->description))->limit(160) }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('events.show', ['event' => $event->resolveSlug()]) }}">
    <link rel="canonical" href="{{ route('events.show', ['event' => $event->resolveSlug()]) }}">
@endpush

@section('content')
    <a href="{{ route('events.index') }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-primary mb-6 transition-colors">
        <i class="fa-solid fa-arrow-left"></i>
        Retour aux événements
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Colonne principale --}}
        <div class="lg:col-span-2 space-y-6">
            <article class="bg-white rounded-xl border border-gray-200 shadow-2xs overflow-hidden">
                <div class="h-48 sm:h-56 bg-gradient-to-br from-orange-50 to-gray-100 flex items-center justify-center relative">
                    <i class="fa-regular fa-calendar-days text-6xl text-primary/20"></i>
                    <div class="absolute top-4 left-4 bg-white/90 backdrop-blur px-3 py-1 rounded-lg text-center shadow-sm">
                        <div class="text-primary font-bold text-xl leading-none">{{ $event->start_date->format('d') }}</div>
                        <div class="text-[10px] uppercase text-gray-500 font-bold">{{ $event->start_date->translatedFormat('M') }}</div>
                    </div>
                    @if($event->type)
                        <span class="absolute top-4 right-4 bg-primary/10 text-primary text-xs font-bold px-3 py-1 rounded-full">
                            {{ $event->type->name }}
                        </span>
                    @endif
                </div>

                <div class="p-6 sm:p-8">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1C1C2E] mb-4">{{ $event->title }}</h1>

                    <div class="flex flex-col gap-2 text-sm text-gray-500 mb-6">
                        <span class="inline-flex items-center gap-2">
                            <i class="fa-regular fa-clock text-primary w-4"></i>
                            {{ $event->start_date->translatedFormat('l j F Y · H:i') }}
                            — {{ $event->end_date->format('H:i') }}
                        </span>
                        @if($event->location)
                            <span class="inline-flex items-center gap-2">
                                <i class="fa-solid fa-location-dot text-primary w-4"></i>
                                {{ $event->location }}
                            </span>
                        @endif
                        @if($event->meeting_link)
                            <a href="{{ $event->meeting_link }}" target="_blank" rel="noopener"
                               class="inline-flex items-center gap-2 text-primary font-semibold hover:underline">
                                <i class="fa-solid fa-video w-4"></i>
                                Rejoindre en ligne
                            </a>
                        @endif
                    </div>

                    <div class="prose prose-sm max-w-none text-gray-600 whitespace-pre-line">{{ $event->description }}</div>
                </div>
            </article>

            @if($event->speakers->isNotEmpty())
                <section class="bg-white rounded-xl border border-gray-200 shadow-2xs p-6 sm:p-8">
                    <h2 class="text-lg font-bold text-[#1C1C2E] mb-4">Intervenants</h2>
                    <ul class="space-y-4">
                        @foreach($event->speakers as $speaker)
                            <li class="flex gap-4 items-start">
                                @if($speaker->avatar)
                                    <img src="{{ $speaker->avatar }}" alt="{{ $speaker->name }}"
                                         class="w-12 h-12 rounded-full object-cover shrink-0">
                                @else
                                    <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-user text-primary"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-bold text-gray-900">{{ $speaker->name }}</p>
                                    @if($speaker->bio)
                                        <p class="text-sm text-gray-500 mt-0.5">{{ $speaker->bio }}</p>
                                    @endif
                                    <div class="flex gap-3 mt-2 text-sm">
                                        @if($speaker->github)
                                            <a href="{{ $speaker->github }}" target="_blank" rel="noopener" class="text-gray-400 hover:text-primary">
                                                <i class="fa-brands fa-github"></i>
                                            </a>
                                        @endif
                                        @if($speaker->linkedin)
                                            <a href="{{ $speaker->linkedin }}" target="_blank" rel="noopener" class="text-gray-400 hover:text-primary">
                                                <i class="fa-brands fa-linkedin"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif
        </div>

        {{-- Sidebar inscription --}}
        <aside class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-200 shadow-2xs p-6 sticky top-24 space-y-4">
                <h2 class="font-bold text-[#1C1C2E]">Participation</h2>

                @php
                    $taken = (int) ($event->confirmed_registrations_count ?? $event->confirmedRegistrationsCount());
                    $total = $event->capacity ?? 0;
                    $percent = $total > 0 ? min(100, ($taken / $total) * 100) : 0;
                @endphp

                @if($total > 0)
                    <x-progress-bar
                        label="{{ $taken }} / {{ $total }} inscrits"
                        value="{{ max(0, $total - $taken) }} place(s) restante(s)"
                        :percent="$percent"
                    />
                @else
                    <p class="text-sm text-gray-500">Places illimitées</p>
                @endif

                @if($registration)
                    <div class="flex items-start gap-2 bg-green-50 border border-green-200 rounded-lg p-4 text-sm text-green-800">
                        <i class="fa-solid fa-circle-check mt-0.5"></i>
                        <span>Vous êtes inscrit à cet événement.</span>
                    </div>
                @elseif($waitlist)
                    <div class="flex items-start gap-2 bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-900">
                        <i class="fa-solid fa-hourglass-half mt-0.5"></i>
                        <span>Liste d'attente — position <strong>#{{ $waitlist->position }}</strong>.</span>
                    </div>
                @elseif($canRegister)
                    <form method="POST" action="{{ route('events.register', $event) }}">
                        @csrf
                        <button type="submit"
                                class="w-full bg-primary hover:bg-orange-500 text-white font-bold py-3 rounded-xl transition-colors">
                            {{ $event->isFull() ? 'Rejoindre la liste d\'attente' : 'Confirmer mon inscription' }}
                        </button>
                    </form>
                    <p class="text-xs text-gray-400 text-center">Réservé aux membres actifs</p>
                @elseif(auth()->check() && ! auth()->user()->hasRole('membre-actif'))
                    <p class="text-sm text-gray-500">
                        Seuls les membres actifs peuvent s'inscrire.
                    </p>
                @elseif(! $event->isRegisterable())
                    <p class="text-sm text-gray-500">
                        Les inscriptions sont closes pour cet événement.
                    </p>
                @else
                    <a href="{{ route('login') }}"
                       class="block w-full text-center bg-[#1C1C2E] hover:bg-gray-800 text-white font-bold py-3 rounded-xl transition-colors">
                        <i class="fa-brands fa-github mr-2"></i>
                        Se connecter pour s'inscrire
                    </a>
                @endif

                @if($errors->any())
                    <div class="text-sm text-red-600 space-y-1">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
            </div>
        </aside>
    </div>
@endsection
