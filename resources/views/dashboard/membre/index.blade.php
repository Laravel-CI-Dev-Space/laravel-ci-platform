@extends('layouts.dashboard')

@section('title', 'Mon espace')

@section('content')

    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-[#1C1C2E]">
            Bonjour, <span class="text-primary">{{ auth()->user()->name }}</span>
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            Bienvenue dans votre espace membre.
        </p>
    </div>

    @php
        /** @var \App\Models\User $user */
        $user    = auth()->user();
        $profile = $user->profile;
        $rate    = $profile?->completionRate() ?? 0;
    @endphp

    {{-- Complétion du profil --}}
    @if($rate < 100)
        <div class="bg-white rounded-xl border border-primary/30 shadow-sm p-5 mb-6 flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="flex-1">
                <p class="text-sm font-semibold text-gray-700 mb-2">
                    <i class="fa-solid fa-circle-user text-primary mr-1.5"></i>
                    Votre profil est complété à <strong class="text-primary">{{ $rate }}%</strong>
                </p>
                <x-progress-bar label="" value="{{ $rate }}%" :percent="$rate" />
            </div>
            <a href="{{ route('profile.edit') }}"
               class="shrink-0 inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-orange-500 transition-colors">
                <i class="fa-solid fa-pen"></i>
                Compléter mon profil
            </a>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
        <x-card.stat value="—" label="Sujets du forum">
            <x-slot:icon>
                <i class="fa-solid fa-comments text-white text-xl"></i>
            </x-slot:icon>
        </x-card.stat>

        <x-card.stat value="—" label="Événements à venir" accent="bg-blue-500">
            <x-slot:icon>
                <i class="fa-solid fa-calendar-days text-white text-xl"></i>
            </x-slot:icon>
        </x-card.stat>

        <x-card.stat value="—" label="Offres d'emploi" accent="bg-emerald-500">
            <x-slot:icon>
                <i class="fa-solid fa-briefcase text-white text-xl"></i>
            </x-slot:icon>
        </x-card.stat>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <p class="text-sm text-gray-400 text-center py-8">
            <i class="fa-solid fa-rocket text-gray-300 text-3xl mb-3 block"></i>
            Les fonctionnalités de la plateforme arrivent bientôt.
        </p>
    </div>

@endsection
