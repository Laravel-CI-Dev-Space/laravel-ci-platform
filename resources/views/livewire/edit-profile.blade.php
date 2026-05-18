<div>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: sans-serif; background: #f5f5f5; color: #1C1C2E; }

        .navbar {
            background: #1C1C2E; padding: 1rem 2rem;
            display: flex; justify-content: space-between; align-items: center;
        }
        .navbar .brand { color: #FF6600; font-weight: bold; font-size: 1.2rem; }
        .navbar .user  { display: flex; align-items: center; gap: 1rem; color: #fff; font-size: 0.9rem; }
        .navbar img    { width: 36px; height: 36px; border-radius: 50%; border: 2px solid #FF6600; object-fit: cover; }

        .container { max-width: 720px; margin: 2rem auto; padding: 0 1rem; }

        .page-header { margin-bottom: 1.5rem; }
        .page-title  { font-size: 1.4rem; font-weight: 800; color: #1C1C2E; }
        .page-title span { color: #FF6600; }
        .page-subtitle { font-size: 0.9rem; color: #888; margin-top: 0.25rem; }

        .completion-card {
            background: #fff; border-radius: 12px;
            padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .completion-header {
            display: flex; justify-content: space-between;
            align-items: center; margin-bottom: 0.75rem;
        }
        .completion-header span { font-size: 0.85rem; font-weight: 600; color: #444; }
        .completion-header strong { font-size: 1.1rem; color: #FF6600; }
        .progress-bar { height: 8px; background: #eee; border-radius: 99px; overflow: hidden; }
        .progress-bar .fill {
            height: 100%; border-radius: 99px;
            background: linear-gradient(90deg, #FF6600, #ff9340);
            transition: width 0.5s ease;
        }
        .missing-fields { display: flex; flex-wrap: wrap; gap: 0.4rem; margin-top: 0.75rem; }
        .missing-tag {
            background: #FFF0E6; color: #FF6600;
            font-size: 0.75rem; padding: 0.2rem 0.6rem;
            border-radius: 99px; border: 1px solid #FFD0B0;
        }

        .card {
            background: #fff; border-radius: 12px;
            padding: 1.75rem; margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .card-title {
            font-size: 0.82rem; text-transform: uppercase;
            letter-spacing: 1px; color: #888; margin-bottom: 1.25rem;
            padding-bottom: 0.5rem; border-bottom: 2px solid #FF6600; display: inline-block;
        }

        .avatar-row { display: flex; align-items: center; gap: 1.5rem; margin-bottom: 1.25rem; }
        .avatar-row img { width: 80px; height: 80px; border-radius: 50%; border: 3px solid #FF6600; object-fit: cover; }
        .avatar-info strong { display: block; font-size: 0.95rem; color: #1C1C2E; }
        .avatar-info small  { color: #888; font-size: 0.82rem; }

        .form-group { margin-bottom: 1.1rem; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; color: #444; margin-bottom: 0.4rem; }
        .optional { color: #aaa; font-weight: 400; font-size: 0.78rem; }

        .form-control {
            width: 100%; padding: 0.65rem 0.9rem;
            border: 1.5px solid #ddd; border-radius: 8px;
            font-size: 0.95rem; color: #1C1C2E;
            background: #fafafa; transition: border-color 0.2s; appearance: none;
        }
        .form-control:focus { outline: none; border-color: #FF6600; background: #fff; }
        .form-control.is-error { border-color: #C0392B; }
        textarea.form-control { resize: vertical; min-height: 100px; }

        .error-msg { font-size: 0.8rem; color: #C0392B; margin-top: 0.3rem; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

        .file-input {
            width: 100%; padding: 0.5rem;
            border: 1.5px dashed #ddd; border-radius: 8px;
            background: #fafafa; font-size: 0.85rem; cursor: pointer;
        }
        .file-input:focus { outline: none; border-color: #FF6600; }

        .avatar-preview {
            width: 55px; height: 55px; border-radius: 50%;
            object-fit: cover; border: 2px solid #FF6600; margin-top: 0.5rem;
        }

        .cv-current {
            display: inline-flex; align-items: center; gap: 0.5rem;
            background: #EAF4FB; color: #1a5276; padding: 0.4rem 0.8rem;
            border-radius: 6px; font-size: 0.82rem; text-decoration: none; margin-top: 0.5rem;
        }

        .stack-predefined { margin-bottom: 1rem; }
        .stack-category { font-size: 0.78rem; color: #888; margin-bottom: 0.4rem; font-weight: 600; text-transform: uppercase; }
        .stack-tags { display: flex; flex-wrap: wrap; gap: 0.4rem; margin-bottom: 0.75rem; }
        .stack-tag {
            padding: 0.3rem 0.75rem; border-radius: 99px;
            font-size: 0.82rem; cursor: pointer; border: 1.5px solid #ddd;
            background: #fafafa; color: #555; transition: all 0.15s;
        }
        .stack-tag:hover    { border-color: #FF6600; color: #FF6600; }
        .stack-tag.selected { background: #FF6600; color: #fff; border-color: #FF6600; }

        .stack-selected { display: flex; flex-wrap: wrap; gap: 0.4rem; margin-top: 0.75rem; }
        .stack-selected-tag {
            display: inline-flex; align-items: center; gap: 0.4rem;
            background: #1C1C2E; color: #fff; padding: 0.3rem 0.75rem;
            border-radius: 99px; font-size: 0.82rem;
        }
        .stack-selected-tag button {
            background: none; border: none; color: #FF6600;
            cursor: pointer; font-size: 1rem; line-height: 1; padding: 0;
        }

        .stack-custom { display: flex; gap: 0.5rem; margin-top: 0.75rem; }
        .stack-custom input { flex: 1; }
        .stack-custom button {
            padding: 0.5rem 1rem; background: #1C1C2E; color: #fff;
            border: none; border-radius: 8px; cursor: pointer;
            font-size: 0.85rem; font-weight: 600; white-space: nowrap;
        }
        .stack-custom button:hover { background: #FF6600; }

        .alert-info {
            background: #EAF4FB; border-left: 4px solid #2980B9;
            border-radius: 6px; padding: 0.75rem 1rem;
            font-size: 0.85rem; color: #1a5276; margin-bottom: 1.5rem;
        }
        .alert-success {
            background: #D5F5E3; border-left: 4px solid #2ECC71;
            border-radius: 6px; padding: 0.75rem 1rem;
            font-size: 0.85rem; color: #1E8449; margin-bottom: 1.5rem;
        }

        .btn-submit {
            width: 100%; padding: 0.85rem; background: #FF6600;
            color: #fff; border: none; border-radius: 8px;
            font-size: 1rem; font-weight: 700; cursor: pointer;
            transition: background 0.2s, transform 0.1s;
        }
        .btn-submit:hover    { background: #e05500; transform: translateY(-1px); }
        .btn-submit:disabled { background: #ddd; color: #999; cursor: not-allowed; transform: none; }

        .back-link { display: inline-block; margin-top: 1rem; font-size: 0.85rem; color: #888; text-decoration: none; }
        .back-link:hover { color: #FF6600; }

        @media (max-width: 580px) { .form-row { grid-template-columns: 1fr; } }
    </style>

    {{-- NAVBAR --}}
    <nav class="navbar">
        <span class="brand">🐘 Laravel CI</span>
        <div class="user">
            <img src="{{ $currentAvatar }}" alt="avatar">
            <span>{{ auth()->user()->name }}</span>
        </div>
    </nav>

    <div class="container">

        {{-- HEADER --}}
        <div class="page-header">
            @if($isFirstTime)
                <h1 class="page-title">Bienvenue sur <span>Laravel CI</span> ! 🎉</h1>
                <p class="page-subtitle">Complétez votre profil pour accéder à la plateforme. Vous pouvez sauvegarder à tout moment et revenir plus tard.</p>
            @else
                <h1 class="page-title">Mon <span>profil</span></h1>
                <p class="page-subtitle">Mettez à jour vos informations. Plus votre profil est complet, plus vous êtes visible dans la communauté.</p>
            @endif
        </div>

        {{-- MESSAGES --}}
        @if($isFirstTime)
            <div class="alert-info">ℹ️ Vous pouvez sauvegarder maintenant et revenir compléter votre profil plus tard.</div>
        @endif

        @if(session('success'))
            <div class="alert-success">✅ {{ session('success') }}</div>
        @endif

        {{-- TAUX DE COMPLÉTION --}}
        @if(!$isFirstTime)
            <div class="completion-card">
                <div class="completion-header">
                    <span>Complétion du profil</span>
                    <strong>{{ $completionRate }}%</strong>
                </div>
                <div class="progress-bar">
                    <div class="fill" style="width: {{ $completionRate }}%"></div>
                </div>
                @if(count($missingFields) > 0)
                    <div class="missing-fields">
                        @foreach($missingFields as $field)
                            <span class="missing-tag">{{ $field }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <form wire:submit="save">

            {{-- AVATAR --}}
            <div class="card">
                <div class="card-title">Photo de profil</div>
                <div class="avatar-row">
                    <img src="{{ $currentAvatar }}" alt="avatar">
                    <div class="avatar-info">
                        <strong>{{ auth()->user()->name }}</strong>
                        <small>{{ '@' . auth()->user()->github_username }}</small>
                    </div>
                </div>
                <div class="form-group">
                    <label>Changer l'avatar <span class="optional">(JPG, PNG — max 2Mo)</span></label>
                    <input type="file" class="file-input" wire:model="avatarFile" accept="image/*">
                    @if($avatarFile)
                        <img src="{{ $avatarFile->temporaryUrl() }}" class="avatar-preview" alt="preview">
                    @endif
                    @error('avatarFile') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- LOCALISATION --}}
            <div class="card">
                <div class="card-title">Localisation</div>
                <div class="form-group">
                    <label>Pays <span class="optional">(optionnel)</span></label>
                    <select wire:model="pays" class="form-control @error('pays') is-error @enderror">
                        <option value="">-- Sélectionnez votre pays --</option>
                        @foreach($countries as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('pays') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Ville <span class="optional">(optionnel)</span></label>
                        <input type="text" wire:model="ville" placeholder="Ex: Abidjan"
                               class="form-control @error('ville') is-error @enderror">
                        @error('ville') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Commune <span class="optional">(optionnel)</span></label>
                        <input type="text" wire:model="commune" placeholder="Ex: Cocody"
                               class="form-control @error('commune') is-error @enderror">
                        @error('commune') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- BIOGRAPHIE --}}
            <div class="card">
                <div class="card-title">À propos</div>
                <div class="form-group">
                    <label>Biographie <span class="optional">(optionnel — max 1000 caractères)</span></label>
                    <textarea wire:model="biographie"
                              placeholder="Parlez de vous, de votre parcours, de vos projets..."
                              class="form-control @error('biographie') is-error @enderror"
                              maxlength="1000"></textarea>
                    <small style="color:#aaa; font-size:0.78rem;">{{ strlen($biographie) }} / 1000</small>
                    @error('biographie') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- PROFIL TECHNIQUE --}}
            <div class="card">
                <div class="card-title">Profil technique</div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Niveau en Laravel <span class="optional">(optionnel)</span></label>
                        <select wire:model="niveau_laravel" class="form-control @error('niveau_laravel') is-error @enderror">
                            <option value="">-- Sélectionnez --</option>
                            @foreach($niveauxLaravel as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('niveau_laravel') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Années d'expérience <span class="optional">(optionnel)</span></label>
                        <select wire:model="annees_experience" class="form-control @error('annees_experience') is-error @enderror">
                            <option value="">-- Sélectionnez --</option>
                            @foreach($anneesExperience as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('annees_experience') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- STACK TECHNIQUE --}}
                <div class="form-group">
                    <label>Stack technique <span class="optional">(optionnel)</span></label>
                    <div class="stack-predefined">
                        @foreach($stackPredefined as $category => $items)
                            <div class="stack-category">{{ $category }}</div>
                            <div class="stack-tags">
                                @foreach($items as $item)
                                    <span wire:click="toggleStackItem('{{ $item }}')"
                                          class="stack-tag {{ in_array($item, $stack_technique) ? 'selected' : '' }}">
                                        {{ $item }}
                                    </span>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                    <div class="stack-custom">
                        <input type="text" wire:model="newStackItem"
                               wire:keydown.enter.prevent="addStackItem"
                               placeholder="Ajouter un autre outil..."
                               class="form-control">
                        <button type="button" wire:click="addStackItem">+ Ajouter</button>
                    </div>
                    @if(count($stack_technique) > 0)
                        <div class="stack-selected">
                            @foreach($stack_technique as $item)
                                <span class="stack-selected-tag">
                                    {{ $item }}
                                    <button type="button" wire:click="removeStackItem('{{ $item }}')">×</button>
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- ACADÉMIQUE & PRO --}}
            <div class="card">
                <div class="card-title">Parcours académique & professionnel</div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Niveau académique <span class="optional">(optionnel)</span></label>
                        <select wire:model="niveau_academique" class="form-control @error('niveau_academique') is-error @enderror">
                            <option value="">-- Sélectionnez --</option>
                            @foreach($niveauxAcademiques as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('niveau_academique') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Situation professionnelle <span class="optional">(optionnel)</span></label>
                        <select wire:model="poste" class="form-control @error('poste') is-error @enderror">
                            <option value="">-- Sélectionnez --</option>
                            @foreach($postes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('poste') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- LIENS & CV --}}
            <div class="card">
                <div class="card-title">Liens & Documents</div>
                <div class="form-group">
                    <label>Lien portfolio <span class="optional">(optionnel)</span></label>
                    <input type="url" wire:model="lien_portfolio"
                           placeholder="https://mon-portfolio.com"
                           class="form-control @error('lien_portfolio') is-error @enderror">
                    @error('lien_portfolio') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>CV <span class="optional">(optionnel — PDF, Word, image — max 5Mo)</span></label>
                    <input type="file" class="file-input" wire:model="cvFile"
                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    @if($currentCv && !$cvFile)
                        <a href="{{ $currentCv }}" target="_blank" class="cv-current">
                            📄 CV actuel — voir
                        </a>
                    @endif
                    @error('cvFile') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- SUBMIT --}}
            <button type="submit" class="btn-submit" wire:loading.attr="disabled">
                <span wire:loading.remove>💾 Sauvegarder le profil</span>
                <span wire:loading>Enregistrement en cours...</span>
            </button>

            @if(!$isFirstTime)
                <a href="{{ route('dashboard') }}" class="back-link">← Retour au dashboard</a>
            @endif

        </form>
    </div>
</div>
