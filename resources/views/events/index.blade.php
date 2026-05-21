@extends('layouts.public')

@section('title', 'Événements')

@push('head')
    <meta name="description" content="Meetups, webinars et hackathons de la communauté Laravel Côte d'Ivoire.">
    <link rel="canonical" href="{{ route('events.index') }}">
@endpush

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-[#1C1C2E] mb-2">Événements</h1>
        <p class="text-gray-500 max-w-2xl">
            Meetups, webinars et hackathons de la communauté Laravel CI. Inscrivez-vous en un clic.
        </p>
    </div>

    {{-- Filtres --}}
    <form method="GET" action="{{ route('events.index') }}"
          class="flex flex-col sm:flex-row flex-wrap gap-3 mb-8 p-4 bg-white rounded-xl border border-gray-200 shadow-2xs">
        <div class="flex flex-wrap gap-2">
            <span class="text-xs font-bold text-gray-400 uppercase self-center mr-1">Période</span>
            @foreach(['upcoming' => 'À venir', 'past' => 'Passés', 'all' => 'Tous'] as $value => $label)
                <a href="{{ route('events.index', array_filter(['period' => $value, 'type' => $type])) }}"
                   class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ $period === $value ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="flex flex-wrap gap-2 sm:ml-auto">
            <span class="text-xs font-bold text-gray-400 uppercase self-center mr-1">Type</span>
            <a href="{{ route('events.index', ['period' => $period]) }}"
               class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ ! $type ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Tous
            </a>
            @foreach($types as $eventType)
                <a href="{{ route('events.index', ['period' => $period, 'type' => $eventType->slug]) }}"
                   class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ $type === $eventType->slug ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    {{ $eventType->name }}
                </a>
            @endforeach
        </div>
    </form>

    @if($events->isEmpty())
        <div class="text-center py-16 bg-white rounded-xl border border-dashed border-gray-300">
            <i class="fa-regular fa-calendar-xmark text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 font-medium">Aucun événement pour ces filtres.</p>
            <a href="{{ route('events.index') }}" class="inline-block mt-4 text-primary font-semibold hover:underline">
                Voir tous les événements à venir
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($events as $event)
                <x-card.event :event="$event" />
            @endforeach
        </div>

        <div class="mt-10">
            {{ $events->links() }}
        </div>
    @endif
@endsection
