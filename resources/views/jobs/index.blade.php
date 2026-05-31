@extends('layouts.public')

@section('title', 'Offres d\'emploi')

@push('head')
    <meta name="description" content="Offres Laravel et PHP de la communauté Laravel Côte d'Ivoire.">
    <link rel="canonical" href="{{ route('jobs.index') }}">
@endpush

@section('content')
    <div class="mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-[#1C1C2E] mb-2">Job board</h1>
            <p class="text-gray-500 max-w-2xl">
                Offres Laravel, PHP et tech publiées par la communauté Laravel CI.
            </p>
        </div>
        @auth
            @if(auth()->user()->hasRole('membre-actif'))
                <a href="{{ route('jobs.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-primary hover:bg-orange-500 text-white font-bold rounded-xl transition-colors shrink-0">
                    <i class="fa-solid fa-plus"></i>
                    Publier une offre
                </a>
            @endif
        @endauth
    </div>

    <form method="GET" action="{{ route('jobs.index') }}"
          class="flex flex-col gap-4 mb-8 p-4 bg-white rounded-xl border border-gray-200 shadow-2xs">
        <div class="flex flex-wrap gap-2">
            <span class="text-xs font-bold text-gray-400 uppercase self-center mr-1">Type</span>
            <a href="{{ route('jobs.index', array_filter(['remote' => $remote ? 1 : null, 'skill' => $skill, 'category' => $category, 'sort' => $sort])) }}"
               class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ ! $type ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Tous
            </a>
            @foreach(\App\Enums\Jobs\JobOfferType::cases() as $offerType)
                <a href="{{ route('jobs.index', array_filter(['type' => $offerType->value, 'remote' => $remote ? 1 : null, 'skill' => $skill, 'category' => $category, 'sort' => $sort])) }}"
                   class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ $type === $offerType->value ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    {{ $offerType->label() }}
                </a>
            @endforeach
        </div>

        <div class="flex flex-wrap gap-2 items-center">
            <span class="text-xs font-bold text-gray-400 uppercase mr-1">Filtres</span>
            <a href="{{ route('jobs.index', array_filter(['type' => $type, 'skill' => $skill, 'category' => $category, 'sort' => $sort])) }}"
               class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ ! $remote ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Toutes localisations
            </a>
            <a href="{{ route('jobs.index', array_filter(['type' => $type, 'remote' => 1, 'skill' => $skill, 'category' => $category, 'sort' => $sort])) }}"
               class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ $remote ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Remote uniquement
            </a>
        </div>

        @if($skills->isNotEmpty())
            <div class="flex flex-wrap gap-2">
                <span class="text-xs font-bold text-gray-400 uppercase self-center mr-1">Compétence</span>
                <a href="{{ route('jobs.index', array_filter(['type' => $type, 'remote' => $remote ? 1 : null, 'category' => $category, 'sort' => $sort])) }}"
                   class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ ! $skill ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Toutes
                </a>
                @foreach($skills as $jobSkill)
                    <a href="{{ route('jobs.index', array_filter(['type' => $type, 'remote' => $remote ? 1 : null, 'skill' => $jobSkill->slug, 'category' => $category, 'sort' => $sort])) }}"
                       class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ $skill === $jobSkill->slug ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        {{ $jobSkill->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <div class="flex flex-wrap gap-2 sm:ml-auto">
            <span class="text-xs font-bold text-gray-400 uppercase self-center mr-1">Tri</span>
            @foreach(['newest' => 'Plus récentes', 'title' => 'Titre A–Z'] as $value => $label)
                <a href="{{ route('jobs.index', array_filter(['type' => $type, 'remote' => $remote ? 1 : null, 'skill' => $skill, 'category' => $category, 'sort' => $value])) }}"
                   class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ $sort === $value ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </form>

    @if($offers->isEmpty())
        <div class="text-center py-16 bg-white rounded-xl border border-dashed border-gray-300">
            <i class="fa-solid fa-briefcase text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 font-medium">Aucune offre pour ces filtres.</p>
            <a href="{{ route('jobs.index') }}" class="inline-block mt-4 text-primary font-semibold hover:underline">
                Voir toutes les offres actives
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($offers as $offer)
                <x-card.job :job="$offer" />
            @endforeach
        </div>

        <div class="mt-10">
            {{ $offers->links() }}
        </div>
    @endif
@endsection
