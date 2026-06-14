<?php

namespace App\Livewire;

use App\Enums\AcademicLevel;
use App\Enums\JobStatus;
use App\Enums\LaravelLevel;
use App\Enums\YearsExperience;
use App\Models\Profile;
use App\Models\User;
use App\Services\AssetService;
use App\Services\CountryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('layouts.profile-setup')]
class EditProfile extends Component
{
    use WithFileUploads;

    /** @var TemporaryUploadedFile|null */
    public $avatarFile = null;

    /** @var TemporaryUploadedFile|null */
    public $cvFile = null;

    public ?string $currentAvatar = null;

    public ?string $currentCv = null;

    // Localisation
    public string $country = '';

    public string $city = '';

    public string $district = '';

    // Infos perso
    public string $bio = '';

    // Technique
    public string $laravel_level = '';

    public string $years_experience = '';

    public array $tech_stack = [];

    public string $newStackItem = '';

    // Académique & pro
    public string $academic_level = '';

    public string $job_status = '';

    // Liens
    public string $portfolio_url = '';

    // Préférences de notifications
    public array $notificationPreferences = [
        'new_answer'        => true,
        'article_published' => true,
        'event_reminder'    => true,
        'job_alert'         => true,
        'mention'           => true,
    ];

    // État
    public bool $isFirstTime = false;

    public int $completionRate = 0;

    public array $missingFields = [];

    public function mount(): void
    {
        /** @var User $user */
        $user    = Auth::user();
        $profile = $user->profile;

        $this->isFirstTime = $profile === null;

        if ($profile) {
            $this->currentAvatar           = $profile->avatarUrl($user->avatar);
            $this->currentCv               = $profile->cvUrl();
            $this->country                 = $profile->country          ?? '';
            $this->city                    = $profile->city             ?? '';
            $this->district                = $profile->district         ?? '';
            $this->bio                     = $profile->bio              ?? '';
            $this->laravel_level           = $profile->laravel_level    ?? '';
            $this->years_experience        = $profile->years_experience ?? '';
            $this->tech_stack              = $profile->tech_stack       ?? [];
            $this->academic_level          = $profile->academic_level   ?? '';
            $this->job_status              = $profile->job_status       ?? '';
            $this->portfolio_url           = $profile->portfolio_url    ?? '';
            $this->completionRate          = $profile->completionRate();
            $this->missingFields           = $profile->missingFields();
            $this->notificationPreferences = array_merge(
                $this->notificationPreferences,
                $profile->notification_preferences ?? []
            );
        } else {
            $this->currentAvatar = $user->avatar;
        }
    }

    /**
     * Bascule une préférence de notification (true/false).
     */
    public function toggleNotificationPreference(string $key): void
    {
        if (array_key_exists($key, $this->notificationPreferences)) {
            $this->notificationPreferences[$key] = ! $this->notificationPreferences[$key];
        }
    }

    public function toggleStackItem(string $item): void
    {
        if (in_array($item, $this->tech_stack)) {
            $this->tech_stack = array_values(
                array_filter($this->tech_stack, fn ($s) => $s !== $item)
            );
        } else {
            $this->tech_stack[] = $item;
        }
    }

    public function addStackItem(): void
    {
        $item = trim($this->newStackItem);
        if ($item && ! in_array($item, $this->tech_stack)) {
            $this->tech_stack[] = $item;
        }
        $this->newStackItem = '';
    }

    public function removeStackItem(string $item): void
    {
        $this->tech_stack = array_values(
            array_filter($this->tech_stack, fn ($s) => $s !== $item)
        );
    }

    protected function rules(): array
    {
        return [
            'country'          => ['nullable', 'string', 'max:100'],
            'city'             => ['nullable', 'string', 'max:100'],
            'district'         => ['nullable', 'string', 'max:100'],
            'bio'              => ['nullable', 'string', 'max:1000'],
            'laravel_level'    => ['nullable', 'in:' . implode(',', array_column(LaravelLevel::cases(), 'value'))],
            'years_experience' => ['nullable', 'in:' . implode(',', array_column(YearsExperience::cases(), 'value'))],
            'tech_stack'       => ['nullable', 'array'],
            'tech_stack.*'     => ['string', 'max:50'],
            'academic_level'   => ['nullable', 'in:' . implode(',', array_column(AcademicLevel::cases(), 'value'))],
            'job_status'       => ['nullable', 'in:' . implode(',', array_column(JobStatus::cases(), 'value'))],
            'portfolio_url'    => ['nullable', 'url', 'max:255'],
            'avatarFile'       => ['nullable', 'image', 'max:2048'],
            'cvFile'           => ['nullable', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function save(AssetService $assetService): void
    {
        $this->validate();

        /** @var User $user */
        $user = Auth::user();

        $data = [
            'user_id'                  => $user->id,
            'country'                  => $this->country ?: null,
            'city'                     => $this->city ?: null,
            'district'                 => $this->district ?: null,
            'bio'                      => $this->bio ?: null,
            'laravel_level'            => $this->laravel_level ?: null,
            'years_experience'         => $this->years_experience ?: null,
            'tech_stack'               => count($this->tech_stack) > 0 ? $this->tech_stack : null,
            'academic_level'           => $this->academic_level ?: null,
            'job_status'               => $this->job_status ?: null,
            'portfolio_url'            => $this->portfolio_url ?: null,
            'notification_preferences' => $this->notificationPreferences,
        ];

        if ($this->avatarFile) {
            $data['avatar'] = $assetService->upload(
                file: $this->avatarFile,
                folder: 'avatars',
                prefix: 'avatar',
                userId: $user->id,
                old: $user->profile?->avatar,
            );
        }

        if ($this->cvFile) {
            $data['cv'] = $assetService->uploadPrivate(
                file: $this->cvFile,
                folder: 'cv',
                prefix: 'cv',
                userId: $user->id,
                old: $user->profile?->cv,
            );
        }

        $profile = Profile::updateOrCreate(['user_id' => $user->id], $data);

        // Invalidate the profile-existence cache used by EnsureProfileComplete middleware
        Cache::forget("user_has_profile_{$user->id}");

        $this->completionRate = $profile->completionRate();
        $this->missingFields  = $profile->missingFields();
        $this->currentAvatar  = $profile->avatarUrl($user->avatar);
        $this->currentCv      = $profile->cvUrl();
        $this->avatarFile     = null;
        $this->cvFile         = null;

        session()->flash('success', 'Profil sauvegardé avec succès.');
    }

    public function countries(): array
    {
        return app(CountryService::class)->getCountries();
    }

    public function render(): View
    {
        return view('livewire.edit-profile', [
            'countries'       => $this->countries(),
            'laravelLevels'   => LaravelLevel::labels(),
            'yearsExperience' => YearsExperience::labels(),
            'academicLevels'  => AcademicLevel::labels(),
            'jobStatuses'     => JobStatus::labels(),
            'stackPredefined' => Profile::$stackPredefined,
        ]);
    }
}
