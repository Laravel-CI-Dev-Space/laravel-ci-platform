<div class="min-h-screen bg-gray-50">
@php
    $field  = 'w-full border-[1.5px] rounded-lg px-3 py-2.5 text-sm text-[#1C1C2E] bg-white focus:outline-none focus:border-primary transition-colors';
    $select = $field . ' appearance-none';

    $sectionIcon = 'flex items-center justify-center w-8 h-8 rounded-lg bg-primary/10 shrink-0';
@endphp

    {{-- ══ Navbar ══════════════════════════════════════════════ --}}
    <nav class="sticky top-0 z-20 bg-[#1C1C2E] px-6 py-3 flex items-center justify-between shadow-lg">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
            <img src="{{ asset('assets/logo.jpeg') }}" alt="Laravel CI"
                 class="w-8 h-8 rounded-full border-2 border-primary object-cover">
            <span class="font-extrabold text-lg text-white">
                Laravel <span class="text-primary">CI</span>
            </span>
        </a>
        <x-avatar
            :src="$currentAvatar"
            :name="auth()->user()->name"
            :subtitle="'@' . auth()->user()->github_username"
            class="[&_img]:border-primary [&_p]:text-white [&_p:last-child]:text-gray-400"
        />
    </nav>

    {{-- ══ En-tête page ════════════════════════════════════════ --}}
    <div class="max-w-5xl mx-auto px-4 sm:px-6 pt-8 pb-4">
        @if($isFirstTime)
            <h1 class="text-2xl font-extrabold text-[#1C1C2E]">
                Bienvenue sur <span class="text-primary">Laravel CI</span> !
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Complétez votre profil pour accéder à la plateforme. Vous pouvez sauvegarder à tout moment.
            </p>
        @else
            <h1 class="text-2xl font-extrabold text-[#1C1C2E]">
                Mon <span class="text-primary">profil</span>
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Plus votre profil est complet, plus vous êtes visible dans la communauté.
            </p>
        @endif

        {{-- Alertes --}}
        @if($isFirstTime)
            <div class="flex items-start gap-2 bg-blue-50 border-l-4 border-blue-400 rounded-md p-3 mt-4 text-sm text-blue-700">
                <i class="fa-solid fa-circle-info mt-0.5 shrink-0"></i>
                <span>Vous pouvez sauvegarder maintenant et revenir compléter votre profil plus tard.</span>
            </div>
        @endif

        @if(session('success'))
            <div class="flex items-start gap-2 bg-green-50 border-l-4 border-green-500 rounded-md p-3 mt-4 text-sm text-green-700">
                <i class="fa-solid fa-circle-check mt-0.5 shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
    </div>

    {{-- ══ Layout 2 colonnes ═══════════════════════════════════ --}}
    <form wire:submit="save">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 pb-12 flex flex-col lg:flex-row gap-6 items-start">

        {{-- ── Sidebar (sticky) ───────────────────────────────── --}}
        <aside class="w-full lg:w-72 shrink-0 lg:sticky lg:top-15.25 space-y-4">

            {{-- Avatar + identité --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                <div class="relative inline-block mb-3">
                    <img src="{{ $currentAvatar }}" alt="{{ auth()->user()->name }}"
                         class="w-20 h-20 rounded-full border-[3px] border-primary object-cover mx-auto">
                    @if($avatarFile)
                        <img src="{{ $avatarFile->temporaryUrl() }}" alt="preview"
                             class="absolute inset-0 w-20 h-20 rounded-full border-[3px] border-primary object-cover">
                    @endif
                    <span class="absolute bottom-0.5 right-0.5 w-4 h-4 bg-green-400 border-2 border-white rounded-full"></span>
                </div>
                <p class="font-bold text-[#1C1C2E] text-sm">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ '@' . auth()->user()->github_username }}</p>
                <x-badge-rounded label="membre-actif" color="green" class="mt-2" />
            </div>

            {{-- Stat complétion (uniquement si profil existant) --}}
            @if(!$isFirstTime)
                <x-card.stat :value="$completionRate . '%'" label="Profil complété">
                    <x-slot:icon>
                        <i class="fa-solid fa-circle-user text-white text-lg"></i>
                    </x-slot:icon>
                </x-card.stat>

                {{-- Progress bar séparée --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <x-progress-bar
                        label="Complétion"
                        value="{{ $completionRate }}%"
                        :percent="$completionRate"
                    />

                    @if(count($missingFields) > 0)
                        <p class="text-xs text-gray-400 mt-3 mb-2 font-semibold">Champs manquants</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($missingFields as $f)
                                <x-badge-rounded :label="$f" color="orange" />
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-green-600 font-semibold mt-3 flex items-center gap-1">
                            <i class="fa-solid fa-circle-check"></i> Profil complet
                        </p>
                    @endif
                </div>
            @endif

            {{-- Navigation rapide --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 hidden lg:block">
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Sections</p>
                <nav class="space-y-0.5 text-sm">
                    @foreach([
                        ['photo',       'fa-camera',          'Photo de profil'],
                        ['localisation','fa-location-dot',    'Localisation'],
                        ['apropos',     'fa-pen-nib',         'À propos'],
                        ['technique',   'fa-code',            'Profil technique'],
                        ['academique',  'fa-graduation-cap',  'Académique & pro'],
                        ['liens',       'fa-link',            'Liens & Documents'],
                    ] as [$id, $icon, $title])
                        <a href="#{{ $id }}"
                           class="flex items-center gap-2 text-gray-500 hover:text-primary hover:bg-primary/5 px-2 py-1.5 rounded-lg transition-colors">
                            <i class="fa-solid fa-{{ $icon }} w-4 text-center text-xs"></i>
                            {{ $title }}
                        </a>
                    @endforeach
                </nav>
            </div>

            {{-- Bouton sauvegarde (desktop) --}}
            <div class="hidden lg:block">
                <button type="submit" wire:loading.attr="disabled"
                        class="w-full flex items-center justify-center gap-2 py-3 bg-primary text-white font-bold text-sm rounded-xl hover:bg-orange-500 transition-colors disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed shadow-lg shadow-primary/20">
                    <span wire:loading.remove>
                        <i class="fa-solid fa-floppy-disk mr-1"></i>Sauvegarder
                    </span>
                    <span wire:loading class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin"></i>Enregistrement...
                    </span>
                </button>

                @if(!$isFirstTime)
                    <a href="{{ route('dashboard') }}"
                       class="mt-2 flex items-center justify-center gap-1.5 text-xs text-gray-400 hover:text-primary transition-colors">
                        <i class="fa-solid fa-arrow-left"></i>Retour au dashboard
                    </a>
                @endif
            </div>

        </aside>

        {{-- ── Sections de formulaire ──────────────────────────── --}}
        <div class="flex-1 min-w-0 space-y-4">

            {{-- ─ Photo de profil ─ --}}
            <section id="photo" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center gap-2.5 mb-5">
                    <div class="{{ $sectionIcon }}">
                        <i class="fa-solid fa-camera text-primary text-sm"></i>
                    </div>
                    <h2 class="text-sm font-bold text-gray-900">Photo de profil</h2>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">
                        Changer l'avatar
                        <span class="normal-case font-normal text-gray-400 ml-1">(JPG, PNG — max 2 Mo)</span>
                    </label>
                    <input type="file" wire:model="avatarFile" accept="image/*"
                           class="w-full text-sm text-gray-500 border-2 border-dashed border-gray-200 rounded-lg p-2.5 bg-gray-50 cursor-pointer hover:border-primary transition-colors file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary">
                    @error('avatarFile')
                        <p class="text-xs text-red-600 mt-1.5 flex items-center gap-1">
                            <i class="fa-solid fa-triangle-exclamation"></i>{{ $message }}
                        </p>
                    @enderror
                </div>
            </section>

            {{-- ─ Localisation ─ --}}
            <section id="localisation" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center gap-2.5 mb-5">
                    <div class="{{ $sectionIcon }}">
                        <i class="fa-solid fa-location-dot text-primary text-sm"></i>
                    </div>
                    <h2 class="text-sm font-bold text-gray-900">Localisation</h2>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">
                            Pays <span class="normal-case font-normal text-gray-400">(optionnel)</span>
                        </label>
                        <select wire:model="pays"
                                @class([$select, 'border-red-400' => $errors->has('pays'), 'border-gray-200' => !$errors->has('pays')])>
                            <option value="">-- Sélectionnez votre pays --</option>
                            @foreach($countries as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('pays') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">
                                Ville <span class="normal-case font-normal text-gray-400">(optionnel)</span>
                            </label>
                            <input type="text" wire:model="ville" placeholder="Ex : Abidjan"
                                   @class([$field, 'border-red-400' => $errors->has('ville'), 'border-gray-200' => !$errors->has('ville')])>
                            @error('ville') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">
                                Commune <span class="normal-case font-normal text-gray-400">(optionnel)</span>
                            </label>
                            <input type="text" wire:model="commune" placeholder="Ex : Cocody"
                                   @class([$field, 'border-red-400' => $errors->has('commune'), 'border-gray-200' => !$errors->has('commune')])>
                            @error('commune') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </section>

            {{-- ─ À propos ─ --}}
            <section id="apropos" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center gap-2.5 mb-5">
                    <div class="{{ $sectionIcon }}">
                        <i class="fa-solid fa-pen-nib text-primary text-sm"></i>
                    </div>
                    <h2 class="text-sm font-bold text-gray-900">À propos</h2>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">
                        Biographie <span class="normal-case font-normal text-gray-400">(optionnel — max 1000 caractères)</span>
                    </label>
                    <textarea wire:model="biographie"
                              placeholder="Parlez de vous, de votre parcours, de vos projets..."
                              maxlength="1000" rows="5"
                              @class([$field . ' resize-y', 'border-red-400' => $errors->has('biographie'), 'border-gray-200' => !$errors->has('biographie')])></textarea>
                    <div class="flex items-center justify-between mt-1">
                        <div>@error('biographie') <p class="text-xs text-red-600">{{ $message }}</p> @enderror</div>
                        <span class="text-xs text-gray-400">{{ strlen($biographie) }}/1000</span>
                    </div>
                </div>
            </section>

            {{-- ─ Profil technique ─ --}}
            <section id="technique" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center gap-2.5 mb-5">
                    <div class="{{ $sectionIcon }}">
                        <i class="fa-solid fa-code text-primary text-sm"></i>
                    </div>
                    <h2 class="text-sm font-bold text-gray-900">Profil technique</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">
                            Niveau Laravel <span class="normal-case font-normal text-gray-400">(optionnel)</span>
                        </label>
                        <select wire:model="niveau_laravel"
                                @class([$select, 'border-red-400' => $errors->has('niveau_laravel'), 'border-gray-200' => !$errors->has('niveau_laravel')])>
                            <option value="">-- Sélectionnez --</option>
                            @foreach($niveauxLaravel as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('niveau_laravel') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">
                            Années d'expérience <span class="normal-case font-normal text-gray-400">(optionnel)</span>
                        </label>
                        <select wire:model="annees_experience"
                                @class([$select, 'border-red-400' => $errors->has('annees_experience'), 'border-gray-200' => !$errors->has('annees_experience')])>
                            <option value="">-- Sélectionnez --</option>
                            @foreach($anneesExperience as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('annees_experience') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Stack technique ─────────────────────────── --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-3 uppercase tracking-wide">
                        Stack technique <span class="normal-case font-normal text-gray-400">(optionnel)</span>
                    </label>

                    @foreach($stackPredefined as $category => $items)
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2 mt-3 first:mt-0">
                            {{ $category }}
                        </p>
                        <div class="flex flex-wrap gap-1.5 mb-1">
                            @foreach($items as $item)
                                <button type="button" wire:click="toggleStackItem('{{ $item }}')"
                                        @class([
                                            'px-2.5 py-1 rounded-full text-xs font-medium border-[1.5px] transition-all',
                                            'bg-primary text-white border-primary shadow-sm' => in_array($item, $stack_technique),
                                            'bg-gray-50 text-gray-500 border-gray-200 hover:border-primary hover:text-primary' => !in_array($item, $stack_technique),
                                        ])>
                                    {{ $item }}
                                </button>
                            @endforeach
                        </div>
                    @endforeach

                    {{-- Ajout custom --}}
                    <div class="flex gap-2 mt-4">
                        <input type="text" wire:model="newStackItem"
                               wire:keydown.enter.prevent="addStackItem"
                               placeholder="Ajouter un autre outil..."
                               class="flex-1 border-[1.5px] border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-primary transition-colors">
                        <button type="button" wire:click="addStackItem"
                                class="px-3 py-2 bg-[#1C1C2E] hover:bg-primary text-white text-sm font-semibold rounded-lg transition-colors whitespace-nowrap">
                            <i class="fa-solid fa-plus mr-1"></i>Ajouter
                        </button>
                    </div>

                    {{-- Tags sélectionnés --}}
                    @if(count($stack_technique) > 0)
                        <div class="flex flex-wrap gap-1.5 mt-3 pt-3 border-t border-gray-100">
                            @foreach($stack_technique as $item)
                                <span class="inline-flex items-center gap-1.5 bg-[#1C1C2E] text-white text-xs font-medium px-2.5 py-1 rounded-full">
                                    {{ $item }}
                                    <button type="button" wire:click="removeStackItem('{{ $item }}')"
                                            class="text-primary hover:text-orange-300">
                                        <i class="fa-solid fa-xmark text-[10px]"></i>
                                    </button>
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>

            {{-- ─ Académique & pro ─ --}}
            <section id="academique" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center gap-2.5 mb-5">
                    <div class="{{ $sectionIcon }}">
                        <i class="fa-solid fa-graduation-cap text-primary text-sm"></i>
                    </div>
                    <h2 class="text-sm font-bold text-gray-900">Parcours académique &amp; professionnel</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">
                            Niveau académique <span class="normal-case font-normal text-gray-400">(optionnel)</span>
                        </label>
                        <select wire:model="niveau_academique"
                                @class([$select, 'border-red-400' => $errors->has('niveau_academique'), 'border-gray-200' => !$errors->has('niveau_academique')])>
                            <option value="">-- Sélectionnez --</option>
                            @foreach($niveauxAcademiques as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('niveau_academique') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">
                            Situation professionnelle <span class="normal-case font-normal text-gray-400">(optionnel)</span>
                        </label>
                        <select wire:model="poste"
                                @class([$select, 'border-red-400' => $errors->has('poste'), 'border-gray-200' => !$errors->has('poste')])>
                            <option value="">-- Sélectionnez --</option>
                            @foreach($postes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('poste') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            {{-- ─ Liens & Documents ─ --}}
            <section id="liens" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center gap-2.5 mb-5">
                    <div class="{{ $sectionIcon }}">
                        <i class="fa-solid fa-link text-primary text-sm"></i>
                    </div>
                    <h2 class="text-sm font-bold text-gray-900">Liens &amp; Documents</h2>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">
                            Portfolio <span class="normal-case font-normal text-gray-400">(optionnel)</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 pointer-events-none">
                                <i class="fa-solid fa-globe text-xs"></i>
                            </span>
                            <input type="url" wire:model="lien_portfolio"
                                   placeholder="https://mon-portfolio.com"
                                   @class([$field . ' pl-8', 'border-red-400' => $errors->has('lien_portfolio'), 'border-gray-200' => !$errors->has('lien_portfolio')])>
                        </div>
                        @error('lien_portfolio') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">
                            CV <span class="normal-case font-normal text-gray-400">(optionnel — PDF, Word, image — max 5 Mo)</span>
                        </label>
                        <input type="file" wire:model="cvFile"
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               class="w-full text-sm text-gray-500 border-2 border-dashed border-gray-200 rounded-lg p-2.5 bg-gray-50 cursor-pointer hover:border-primary transition-colors file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary">
                        @if($currentCv && !$cvFile)
                            <a href="{{ $currentCv }}" target="_blank"
                               class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 text-xs font-medium px-3 py-1.5 rounded-lg mt-2 hover:bg-blue-100 transition-colors">
                                <i class="fa-solid fa-file-pdf"></i>
                                CV actuel — voir
                            </a>
                        @endif
                        @error('cvFile') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            {{-- ─ Bouton sauvegarde (mobile) ─ --}}
            <div class="lg:hidden">
                <button type="submit" wire:loading.attr="disabled"
                        class="w-full flex items-center justify-center gap-2 py-3.5 bg-primary text-white font-bold rounded-xl hover:bg-orange-500 transition-colors disabled:bg-gray-200 disabled:text-gray-400 shadow-lg shadow-primary/20">
                    <span wire:loading.remove>
                        <i class="fa-solid fa-floppy-disk mr-1"></i>Sauvegarder le profil
                    </span>
                    <span wire:loading class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin"></i>Enregistrement...
                    </span>
                </button>
                @if(!$isFirstTime)
                    <a href="{{ route('dashboard') }}"
                       class="mt-3 flex items-center justify-center gap-1.5 text-sm text-gray-400 hover:text-primary transition-colors">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                        Retour au dashboard
                    </a>
                @endif
            </div>

        </div>{{-- /form sections --}}

    </div>{{-- /layout --}}
    </form>

</div>
