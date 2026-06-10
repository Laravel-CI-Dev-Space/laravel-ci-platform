@extends('layouts.public')

@section('title', 'Publier une offre')

@section('content')
    <a href="{{ route('jobs.index') }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-primary mb-6 transition-colors">
        <i class="fa-solid fa-arrow-left"></i>
        Retour au job board
    </a>

    <div class="max-w-2xl">
        <h1 class="text-3xl font-extrabold text-[#1C1C2E] mb-2">Publier une offre</h1>
        <p class="text-gray-500 mb-8">
            Votre offre sera enregistrée en brouillon et validée par l'équipe avant publication.
        </p>

        <form method="POST" action="{{ route('jobs.store') }}"
              class="bg-white rounded-xl border border-gray-200 shadow-2xs p-6 sm:p-8 space-y-5">
            @csrf

            <fieldset class="space-y-4">
                <legend class="text-sm font-bold text-gray-700 uppercase tracking-wide">Entreprise</legend>
                <div>
                    <label for="company_name" class="block text-sm font-semibold text-gray-700 mb-1">Nom *</label>
                    <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" required
                           class="w-full rounded-lg border border-gray-200 focus:border-primary focus:ring-primary">
                    @error('company_name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="company_description" class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                    <textarea id="company_description" name="company_description" rows="2"
                              class="w-full rounded-lg border border-gray-200 focus:border-primary focus:ring-primary">{{ old('company_description') }}</textarea>
                </div>
            </fieldset>

            <fieldset class="space-y-4">
                <legend class="text-sm font-bold text-gray-700 uppercase tracking-wide">Offre</legend>
                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-1">Intitulé du poste *</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required
                           class="w-full rounded-lg border border-gray-200 focus:border-primary focus:ring-primary">
                    @error('title')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="type" class="block text-sm font-semibold text-gray-700 mb-1">Type de contrat *</label>
                    <select id="type" name="type" required
                            class="w-full rounded-lg border border-gray-200 focus:border-primary focus:ring-primary">
                        <option value="">Choisir…</option>
                        @foreach(\App\Enums\Jobs\JobOfferType::cases() as $offerType)
                            <option value="{{ $offerType->value }}" @selected(old('type') === $offerType->value)>
                                {{ $offerType->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="location" class="block text-sm font-semibold text-gray-700 mb-1">Localisation *</label>
                    <input type="text" id="location" name="location" value="{{ old('location') }}" required
                           class="w-full rounded-lg border border-gray-200 focus:border-primary focus:ring-primary"
                           placeholder="Abidjan, Remote…">
                    @error('location')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Description *</label>
                    <textarea id="description" name="description" rows="8" required
                              class="w-full rounded-lg border border-gray-200 focus:border-primary focus:ring-primary">{{ old('description') }}</textarea>
                    @error('description')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </fieldset>

            <button type="submit"
                    class="w-full bg-primary hover:bg-orange-500 text-white font-bold py-3 rounded-xl transition-colors">
                Soumettre l'offre
            </button>
        </form>
    </div>
@endsection
