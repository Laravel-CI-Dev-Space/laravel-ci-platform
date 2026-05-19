@extends('layouts.dashboard')

@section('title', 'Dashboard Modérateur')

@section('content')

    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-[#1C1C2E]">
            Bonjour, <span class="text-primary">{{ auth()->user()->name }}</span>
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            Espace modérateur &mdash; gérez le contenu de la communauté.
        </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
        <x-card.stat value="—" label="Fils de discussion">
            <x-slot:icon>
                <i class="fa-solid fa-comments text-white text-xl"></i>
            </x-slot:icon>
        </x-card.stat>

        <x-card.stat value="—" label="Articles publiés" accent="bg-blue-500">
            <x-slot:icon>
                <i class="fa-solid fa-newspaper text-white text-xl"></i>
            </x-slot:icon>
        </x-card.stat>

        <x-card.stat value="—" label="Signalements" accent="bg-amber-500">
            <x-slot:icon>
                <i class="fa-solid fa-flag text-white text-xl"></i>
            </x-slot:icon>
        </x-card.stat>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <p class="text-sm text-gray-400 text-center py-8">
            <i class="fa-solid fa-wrench text-gray-300 text-3xl mb-3 block"></i>
            Les outils de modération seront disponibles prochainement.
        </p>
    </div>

@endsection
