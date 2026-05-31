@extends('layouts.public')

@section('title', $jobOffer->title)

@push('head')
    <meta name="description" content="{{ str(strip_tags($jobOffer->description))->limit(160) }}">
    <meta property="og:title" content="{{ $jobOffer->title }} — Laravel CI">
    <meta property="og:description" content="{{ str(strip_tags($jobOffer->description))->limit(160) }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('jobs.show', $jobOffer) }}">
    <link rel="canonical" href="{{ route('jobs.show', $jobOffer) }}">
@endpush

@section('content')
    <a href="{{ route('jobs.index') }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-primary mb-6 transition-colors">
        <i class="fa-solid fa-arrow-left"></i>
        Retour aux offres
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <article class="bg-white rounded-xl border border-gray-200 shadow-2xs p-6 sm:p-8">
                <div class="flex items-start gap-4 mb-6">
                    @if($jobOffer->company->logo)
                        <img src="{{ $jobOffer->company->logo }}" alt="{{ $jobOffer->company->name }}"
                             class="w-14 h-14 rounded-xl object-contain bg-gray-50 border border-gray-100 shrink-0">
                    @else
                        <div class="w-14 h-14 rounded-xl bg-primary/10 flex items-center justify-center text-primary font-bold text-xl shrink-0">
                            {{ strtoupper(substr($jobOffer->company->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1C1C2E]">{{ $jobOffer->title }}</h1>
                        <p class="text-gray-500 font-medium mt-1">{{ $jobOffer->company->name }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2 mb-6">
                    <x-badge-rounded :label="$jobOffer->type->label()" color="green" />
                    @if($jobOffer->location)
                        <x-badge-rounded :label="$jobOffer->location" color="gray" />
                    @endif
                    @if($jobOffer->salary)
                        <x-badge-rounded :label="$jobOffer->salary" color="blue" />
                    @endif
                    @if($jobOffer->deadline)
                        <x-badge-rounded label="Date limite {{ $jobOffer->deadline->format('d/m/Y') }}" color="orange" />
                    @endif
                </div>

                @if($jobOffer->skills->isNotEmpty())
                    <div class="flex flex-wrap gap-1.5 mb-6">
                        @foreach($jobOffer->skills as $skill)
                            <x-badge :label="$skill->name" />
                        @endforeach
                    </div>
                @endif

                <div class="prose prose-sm max-w-none text-gray-600 whitespace-pre-line">{{ $jobOffer->description }}</div>

                @if($jobOffer->company->website)
                    <a href="{{ $jobOffer->company->website }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 mt-6 text-sm text-primary font-semibold hover:underline">
                        <i class="fa-solid fa-globe"></i>
                        Site de l'entreprise
                    </a>
                @endif
            </article>
        </div>

        <aside class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-200 shadow-2xs p-6 sticky top-24 space-y-4">
                <h2 class="font-bold text-[#1C1C2E]">Candidature</h2>

                @if($application)
                    <div class="flex items-start gap-2 bg-green-50 border border-green-200 rounded-lg p-4 text-sm text-green-800">
                        <i class="fa-solid fa-circle-check mt-0.5"></i>
                        <span>Vous avez déjà postulé à cette offre.</span>
                    </div>
                @elseif($canApply)
                    <form method="POST" action="{{ route('jobs.apply', $jobOffer) }}" class="space-y-3">
                        @csrf
                        <label for="cover_letter" class="block text-sm font-semibold text-gray-700">
                            Lettre de motivation (optionnel)
                        </label>
                        <textarea id="cover_letter" name="cover_letter" rows="4"
                                  class="w-full rounded-lg border border-gray-200 text-sm focus:border-primary focus:ring-primary"
                                  placeholder="Présentez-vous en quelques lignes…">{{ old('cover_letter') }}</textarea>
                        <button type="submit"
                                class="w-full bg-primary hover:bg-orange-500 text-white font-bold py-3 rounded-xl transition-colors">
                            Envoyer ma candidature
                        </button>
                    </form>
                    <p class="text-xs text-gray-400 text-center">Réservé aux membres actifs</p>
                @elseif(auth()->check() && ! auth()->user()->hasRole('membre-actif'))
                    <p class="text-sm text-gray-500">Seuls les membres actifs peuvent postuler.</p>
                @elseif(! $jobOffer->isApplyable())
                    <p class="text-sm text-gray-500">Les candidatures sont closes pour cette offre.</p>
                @else
                    <a href="{{ route('login') }}"
                       class="block w-full text-center bg-[#1C1C2E] hover:bg-gray-800 text-white font-bold py-3 rounded-xl transition-colors">
                        <i class="fa-brands fa-github mr-2"></i>
                        Se connecter pour postuler
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
