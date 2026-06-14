<div>
<form wire:submit="save">
<div class="row g-4 align-items-start">

  {{-- ═══════════════════════════════════════════════
       COLONNE GAUCHE — Profil card + Nav + Bouton
  ═══════════════════════════════════════════════ --}}
  <div class="col-lg-3 sidebar-profile">

    {{-- User card --}}
    <div class="card mb-3">
      <div class="card-body profile-card">
        <div class="avatar-wrap mx-auto" style="display:inline-block">
          @if($avatarFile)
            <img src="{{ $avatarFile->temporaryUrl() }}" class="avatar-preview" alt="preview" />
          @else
            <img src="{{ $currentAvatar ?? asset('assets/dashboard/images/avatar/avatar-1.jpg') }}"
                 class="avatar-preview" alt="{{ auth()->user()->name }}" />
          @endif
          <span class="avatar-online"></span>
        </div>
        <div class="user-name mt-2">{{ auth()->user()->name }}</div>
        <div class="user-handle">@{{ auth()->user()->github_username }}</div>
        <div class="role-badge mt-2">
          <i class="ti ti-shield-check" style="font-size:.8rem"></i> member
        </div>
      </div>
    </div>

    {{-- Completion progress (only if returning user) --}}
    @if(!$isFirstTime)
    <div class="card mb-3">
      <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
          <span class="small fw-semibold text-secondary">Profile completion</span>
          <span class="small fw-bold" style="color:var(--lci-primary)">{{ $completionRate }}%</span>
        </div>
        <div class="progress mb-2">
          <div class="progress-bar" style="width: {{ $completionRate }}%"></div>
        </div>
        @if(count($missingFields) > 0)
          <div class="d-flex flex-wrap gap-1 mt-2">
            @foreach($missingFields as $f)
              <span class="missing-badge">{{ $f }}</span>
            @endforeach
          </div>
        @else
          <p class="mb-0 small" style="color:#15803d">
            <i class="ti ti-circle-check-filled me-1"></i>Profile complete
          </p>
        @endif
      </div>
    </div>
    @endif

    {{-- Section navigation --}}
    <div class="card mb-3">
      <div class="card-body p-2">
        <p class="text-uppercase text-secondary px-2 pt-1 pb-0 mb-1" style="font-size:.68rem;font-weight:700;letter-spacing:.07em">Sections</p>
        <nav class="section-nav nav flex-column">
          <a href="#sec-photo"      class="nav-link"><i class="ti ti-camera"></i> Profile photo</a>
          <a href="#sec-location"   class="nav-link"><i class="ti ti-map-pin"></i> Location</a>
          <a href="#sec-about"      class="nav-link"><i class="ti ti-pencil"></i> About</a>
          <a href="#sec-tech"       class="nav-link"><i class="ti ti-code"></i> Tech profile</a>
          <a href="#sec-academic"   class="nav-link"><i class="ti ti-school"></i> Academic & career</a>
          <a href="#sec-links"      class="nav-link"><i class="ti ti-link"></i> Links & docs</a>
          <a href="#sec-notifications" class="nav-link"><i class="ti ti-bell"></i> Notifications</a>
        </nav>
      </div>
    </div>

    {{-- Save button --}}
    <button type="submit" class="btn-save" wire:loading.attr="disabled">
      <span wire:loading.remove>
        <i class="ti ti-device-floppy me-1"></i> Save profile
      </span>
      <span wire:loading>
        <i class="ti ti-loader-2 me-1" style="animation:spin 1s linear infinite"></i> Saving…
      </span>
    </button>

    @if(!$isFirstTime)
      <a href="{{ route('dashboard') }}"
         class="d-flex align-items-center justify-content-center gap-1 mt-2 small text-secondary text-decoration-none">
        <i class="ti ti-arrow-left"></i> Back to dashboard
      </a>
    @endif

  </div>{{-- /col-lg-3 --}}


  {{-- ═══════════════════════════════════════════════
       COLONNE DROITE — Titre + Alertes + Sections
  ═══════════════════════════════════════════════ --}}
  <div class="col-lg-9">

    {{-- Page title --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
      <div>
        @if($isFirstTime)
          <h1 class="fs-4 fw-bold mb-1">Welcome to <span style="color:var(--lci-primary)">Laravel CI</span> 🎉</h1>
          <p class="text-secondary mb-0 small">Complete your profile to access the platform. You can save at any time.</p>
        @else
          <h1 class="fs-4 fw-bold mb-1">Edit profile</h1>
          <p class="text-secondary mb-0 small">A complete profile makes you more visible in the community.</p>
        @endif
      </div>
      {{-- Mobile save button --}}
      <div class="d-lg-none">
        <button type="submit" class="btn btn-sm px-3 py-2 fw-bold text-white" style="background:var(--lci-primary);border:none;border-radius:.5rem" wire:loading.attr="disabled">
          <i class="ti ti-device-floppy me-1"></i>
          <span wire:loading.remove>Save</span>
          <span wire:loading>Saving…</span>
        </button>
      </div>
    </div>

    {{-- Alerts --}}
    @if($isFirstTime)
      <div class="alert-strip alert-strip-info d-flex align-items-center gap-2 mb-3">
        <i class="ti ti-info-circle" style="font-size:1.1rem;flex-shrink:0"></i>
        <span>You can save now and come back later to complete your profile.</span>
      </div>
    @endif

    @if(session('success'))
      <div class="alert-strip alert-strip-success d-flex align-items-center gap-2 mb-3">
        <i class="ti ti-circle-check" style="font-size:1.1rem;flex-shrink:0"></i>
        <span>{{ session('success') }}</span>
      </div>
    @endif

    {{-- ── SECTION: Profile photo ────────────────────── --}}
    <div id="sec-photo" class="card mb-3">
      <div class="card-header d-flex align-items-center">
        <span class="section-icon"><i class="ti ti-camera"></i></span>
        <h5>Profile photo</h5>
      </div>
      <div class="card-body p-4">
        <div class="row align-items-center g-4">
          <div class="col-auto">
            <div class="avatar-wrap">
              @if($avatarFile)
                <img src="{{ $avatarFile->temporaryUrl() }}" class="avatar-preview" alt="preview" />
              @else
                <img src="{{ $currentAvatar ?? asset('assets/dashboard/images/avatar/avatar-1.jpg') }}"
                     class="avatar-preview" alt="{{ auth()->user()->name }}" />
              @endif
              <span class="avatar-online"></span>
            </div>
          </div>
          <div class="col">
            <label class="form-label fw-semibold small">
              Upload a new avatar
              <span class="text-secondary fw-normal">(JPG, PNG — max 2 MB)</span>
            </label>
            <input type="file" wire:model="avatarFile" accept="image/*"
                   class="form-control form-control-file-custom">
            @error('avatarFile')
              <div class="text-danger small mt-1">
                <i class="ti ti-alert-triangle me-1"></i>{{ $message }}
              </div>
            @enderror
          </div>
        </div>
      </div>
    </div>

    {{-- ── SECTION: Location ────────────────────────── --}}
    <div id="sec-location" class="card mb-3">
      <div class="card-header d-flex align-items-center">
        <span class="section-icon"><i class="ti ti-map-pin"></i></span>
        <h5>Location</h5>
      </div>
      <div class="card-body p-4">
        <div class="mb-3">
          <label class="form-label fw-semibold small">
            Country <span class="text-secondary fw-normal">(optional)</span>
          </label>
          <select wire:model="country"
                  class="form-select @error('country') is-invalid @enderror">
            <option value="">— Select your country —</option>
            @foreach($countries as $key => $label)
              <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
          </select>
          @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold small">
              City <span class="text-secondary fw-normal">(optional)</span>
            </label>
            <input type="text" wire:model.blur="city" placeholder="e.g. Abidjan"
                   class="form-control @error('city') is-invalid @enderror">
            @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold small">
              District / Neighbourhood <span class="text-secondary fw-normal">(optional)</span>
            </label>
            <input type="text" wire:model.blur="district" placeholder="e.g. Cocody"
                   class="form-control @error('district') is-invalid @enderror">
            @error('district') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
      </div>
    </div>

    {{-- ── SECTION: About ───────────────────────────── --}}
    <div id="sec-about" class="card mb-3">
      <div class="card-header d-flex align-items-center">
        <span class="section-icon"><i class="ti ti-pencil"></i></span>
        <h5>About</h5>
      </div>
      <div class="card-body p-4">
        <label class="form-label fw-semibold small">
          Biography <span class="text-secondary fw-normal">(optional — max 1000 chars)</span>
        </label>
        <textarea wire:model.blur="bio" rows="5" maxlength="1000"
                  placeholder="Tell the community about yourself, your background, your projects…"
                  class="form-control @error('bio') is-invalid @enderror"></textarea>
        <div class="d-flex justify-content-between mt-1">
          <div>@error('bio') <div class="text-danger small">{{ $message }}</div> @enderror</div>
          <span class="text-secondary small">{{ strlen($bio) }}/1000</span>
        </div>
      </div>
    </div>

    {{-- ── SECTION: Tech profile ────────────────────── --}}
    <div id="sec-tech" class="card mb-3">
      <div class="card-header d-flex align-items-center">
        <span class="section-icon"><i class="ti ti-code"></i></span>
        <h5>Tech profile</h5>
      </div>
      <div class="card-body p-4">
        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <label class="form-label fw-semibold small">
              Laravel level <span class="text-secondary fw-normal">(optional)</span>
            </label>
            <select wire:model="laravel_level"
                    class="form-select @error('laravel_level') is-invalid @enderror">
              <option value="">— Select —</option>
              @foreach($laravelLevels as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
              @endforeach
            </select>
            @error('laravel_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold small">
              Years of experience <span class="text-secondary fw-normal">(optional)</span>
            </label>
            <select wire:model="years_experience"
                    class="form-select @error('years_experience') is-invalid @enderror">
              <option value="">— Select —</option>
              @foreach($yearsExperience as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
              @endforeach
            </select>
            @error('years_experience') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        {{-- Tech stack --}}
        <label class="form-label fw-semibold small d-block mb-3">
          Tech stack <span class="text-secondary fw-normal">(optional)</span>
        </label>

        @foreach($stackPredefined as $category => $items)
          <p class="text-uppercase text-secondary mb-2 mt-3"
             style="font-size:.68rem;font-weight:700;letter-spacing:.07em">{{ $category }}</p>
          <div class="d-flex flex-wrap gap-2 mb-1">
            @foreach($items as $item)
              <button type="button" wire:click="toggleStackItem('{{ $item }}')"
                      @class(['stack-chip', 'selected' => in_array($item, $tech_stack)])>
                {{ $item }}
              </button>
            @endforeach
          </div>
        @endforeach

        {{-- Custom stack item --}}
        <div class="d-flex gap-2 mt-3">
          <input type="text" wire:model="newStackItem"
                 wire:keydown.enter.prevent="addStackItem"
                 placeholder="Add another tool…"
                 class="form-control form-control-sm">
          <button type="button" wire:click="addStackItem"
                  class="btn btn-sm fw-semibold text-white px-3" style="background:var(--lci-navy);border:none;border-radius:.5rem;white-space:nowrap">
            <i class="ti ti-plus"></i> Add
          </button>
        </div>

        @if(count($tech_stack) > 0)
          <div class="d-flex flex-wrap gap-2 mt-3 pt-3 border-top">
            @foreach($tech_stack as $item)
              <span class="d-inline-flex align-items-center gap-1 px-2 py-1 rounded-pill text-white small fw-semibold"
                    style="background:var(--lci-navy);font-size:.75rem">
                {{ $item }}
                <button type="button" wire:click="removeStackItem('{{ $item }}')"
                        class="btn-close btn-close-white ms-1" style="font-size:.55rem;opacity:.7"></button>
              </span>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    {{-- ── SECTION: Academic & career ───────────────── --}}
    <div id="sec-academic" class="card mb-3">
      <div class="card-header d-flex align-items-center">
        <span class="section-icon"><i class="ti ti-school"></i></span>
        <h5>Academic &amp; career</h5>
      </div>
      <div class="card-body p-4">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold small">
              Academic level <span class="text-secondary fw-normal">(optional)</span>
            </label>
            <select wire:model="academic_level"
                    class="form-select @error('academic_level') is-invalid @enderror">
              <option value="">— Select —</option>
              @foreach($academicLevels as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
              @endforeach
            </select>
            @error('academic_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold small">
              Job status <span class="text-secondary fw-normal">(optional)</span>
            </label>
            <select wire:model="job_status"
                    class="form-select @error('job_status') is-invalid @enderror">
              <option value="">— Select —</option>
              @foreach($jobStatuses as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
              @endforeach
            </select>
            @error('job_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
      </div>
    </div>

    {{-- ── SECTION: Links & docs ────────────────────── --}}
    <div id="sec-links" class="card mb-4">
      <div class="card-header d-flex align-items-center">
        <span class="section-icon"><i class="ti ti-link"></i></span>
        <h5>Links &amp; documents</h5>
      </div>
      <div class="card-body p-4">
        <div class="mb-4">
          <label class="form-label fw-semibold small">
            Portfolio URL <span class="text-secondary fw-normal">(optional)</span>
          </label>
          <div class="input-group">
            <span class="input-group-text bg-white"><i class="ti ti-world text-secondary"></i></span>
            <input type="url" wire:model.blur="portfolio_url" placeholder="https://my-portfolio.com"
                   class="form-control @error('portfolio_url') is-invalid @enderror">
            @error('portfolio_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <div>
          <label class="form-label fw-semibold small">
            CV / Resume <span class="text-secondary fw-normal">(optional — PDF, Word, image — max 5 MB)</span>
          </label>
          <input type="file" wire:model="cvFile" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                 class="form-control form-control-file-custom">
          @if($currentCv && !$cvFile)
            <a href="{{ $currentCv }}" target="_blank"
               class="d-inline-flex align-items-center gap-1 mt-2 small text-primary text-decoration-none">
              <i class="ti ti-file-type-pdf"></i> View current CV
            </a>
          @endif
          @error('cvFile') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>
      </div>
    </div>

    {{-- ── SECTION: Notification preferences ───────── --}}
    <div id="sec-notifications" class="card mb-4">
      <div class="card-header d-flex align-items-center">
        <span class="section-icon"><i class="ti ti-bell"></i></span>
        <h5>Préférences de notifications</h5>
      </div>
      <div class="card-body p-4">
        <p class="text-secondary small mb-3">
          Choisissez les emails que vous souhaitez recevoir de la part de Laravel CI.
        </p>

        <div class="form-check form-switch mb-3">
          <input class="form-check-input" type="checkbox" role="switch" id="pref-new-answer"
                 wire:click="toggleNotificationPreference('new_answer')"
                 @checked($notificationPreferences['new_answer'])>
          <label class="form-check-label" for="pref-new-answer">
            Nouvelle réponse à l'une de mes questions
          </label>
        </div>

        <div class="form-check form-switch mb-3">
          <input class="form-check-input" type="checkbox" role="switch" id="pref-article-published"
                 wire:click="toggleNotificationPreference('article_published')"
                 @checked($notificationPreferences['article_published'])>
          <label class="form-check-label" for="pref-article-published">
            Publication de l'un de mes articles
          </label>
        </div>

        <div class="form-check form-switch mb-3">
          <input class="form-check-input" type="checkbox" role="switch" id="pref-event-reminder"
                 wire:click="toggleNotificationPreference('event_reminder')"
                 @checked($notificationPreferences['event_reminder'])>
          <label class="form-check-label" for="pref-event-reminder">
            Rappels avant les événements auxquels je suis inscrit(e)
          </label>
        </div>

        <div class="form-check form-switch mb-3">
          <input class="form-check-input" type="checkbox" role="switch" id="pref-job-alert"
                 wire:click="toggleNotificationPreference('job_alert')"
                 @checked($notificationPreferences['job_alert'])>
          <label class="form-check-label" for="pref-job-alert">
            Alertes emploi correspondant à mon profil
          </label>
        </div>

        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" role="switch" id="pref-mention"
                 wire:click="toggleNotificationPreference('mention')"
                 @checked($notificationPreferences['mention'])>
          <label class="form-check-label" for="pref-mention">
            Quelqu'un me mentionne avec @@mon-pseudo
          </label>
        </div>
      </div>
    </div>

    {{-- Mobile bottom save --}}
    <div class="d-lg-none">
      <button type="submit" class="btn-save" wire:loading.attr="disabled">
        <span wire:loading.remove><i class="ti ti-device-floppy me-1"></i> Save profile</span>
        <span wire:loading><i class="ti ti-loader-2 me-1"></i> Saving…</span>
      </button>
      @if(!$isFirstTime)
        <a href="{{ route('dashboard') }}"
           class="d-flex align-items-center justify-content-center gap-1 mt-2 small text-secondary text-decoration-none">
          <i class="ti ti-arrow-left"></i> Back to dashboard
        </a>
      @endif
    </div>

  </div>{{-- /col-lg-9 --}}
</div>{{-- /row --}}
</form>

<style>
  @keyframes spin { to { transform: rotate(360deg); } }
</style>
</div>
