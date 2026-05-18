<?php

namespace App\Livewire;

use App\Models\Profile;
use App\Services\CountryService;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Livewire\WithFileUploads;
    use App\Services\AssetService;


class EditProfile extends Component
{
    use WithFileUploads;

    // Fichiers
    public $avatarFile        = null;
    public $cvFile            = null;
    public ?string $currentAvatar = null;
    public ?string $currentCv     = null;

    // Localisation
    public string $pays    = '';
    public string $ville   = '';
    public string $commune = '';

    // Infos perso
    public string $biographie = '';

    // Technique
    public string $niveau_laravel    = '';
    public string $annees_experience = '';
    public array  $stack_technique   = [];
    public string $newStackItem      = '';

    // Académique & pro
    public string $niveau_academique = '';
    public string $poste             = '';

    // Liens
    public string $lien_portfolio = '';

    // État
    public bool  $isFirstTime    = false;
    public int   $completionRate = 0;
    public array $missingFields  = [];

    public function mount(): void
    {
        $user    = auth()->user();
        $profile = $user->profile;

        $this->isFirstTime = $profile === null;

        if ($profile) {
            $this->currentAvatar     = $profile->avatarUrl();
            $this->currentCv         = $profile->cvUrl();
            $this->pays              = $profile->pays ?? '';
            $this->ville             = $profile->ville ?? '';
            $this->commune           = $profile->commune ?? '';
            $this->biographie        = $profile->biographie ?? '';
            $this->niveau_laravel    = $profile->niveau_laravel ?? '';
            $this->annees_experience = $profile->annees_experience ?? '';
            $this->stack_technique   = $profile->stack_technique ?? [];
            $this->niveau_academique = $profile->niveau_academique ?? '';
            $this->poste             = $profile->poste ?? '';
            $this->lien_portfolio    = $profile->lien_portfolio ?? '';
            $this->completionRate    = $profile->completionRate();
            $this->missingFields     = $profile->missingFields();
        } else {
            $this->currentAvatar = $user->avatar;
        }
    }

    /**
     * Toggle item prédéfini de la stack.
     */
    public function toggleStackItem(string $item): void
    {
        if (in_array($item, $this->stack_technique)) {
            $this->stack_technique = array_values(
                array_filter($this->stack_technique, fn ($s) => $s !== $item)
            );
        } else {
            $this->stack_technique[] = $item;
        }
    }

    /**
     * Ajoute un item custom à la stack.
     */
    public function addStackItem(): void
    {
        $item = trim($this->newStackItem);
        if ($item && ! in_array($item, $this->stack_technique)) {
            $this->stack_technique[] = $item;
        }
        $this->newStackItem = '';
    }

    /**
     * Retire un item de la stack.
     */
    public function removeStackItem(string $item): void
    {
        $this->stack_technique = array_values(
            array_filter($this->stack_technique, fn ($s) => $s !== $item)
        );
    }

    protected function rules(): array
    {
        return [
            'pays'              => ['nullable', 'string', 'max:100'],
            'ville'             => ['nullable', 'string', 'max:100'],
            'commune'           => ['nullable', 'string', 'max:100'],
            'biographie'        => ['nullable', 'string', 'max:1000'],
            'niveau_laravel'    => ['nullable', 'in:debutant,intermediaire,avance,expert,maitre'],
            'annees_experience' => ['nullable', 'in:moins_1_an,1_3_ans,3_5_ans,5_10_ans,plus_10_ans'],
            'stack_technique'   => ['nullable', 'array'],
            'niveau_academique' => ['nullable', 'in:bts,licence,master_ingenieur,doctorat'],
            'poste'             => ['nullable', 'in:en_fonction,etudiant,entrepreneur,recherche_emploi,freelance'],
            'lien_portfolio'    => ['nullable', 'url', 'max:255'],
            'avatarFile'        => ['nullable', 'image', 'max:2048'],
            'cvFile'            => ['nullable', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120'],
        ];
    }

    // public function save(): void
    // {
    //     $this->validate();

    //     $user = auth()->user();

    //     $data = [
    //         'user_id'           => $user->id,
    //         'pays'              => $this->pays ?: null,
    //         'ville'             => $this->ville ?: null,
    //         'commune'           => $this->commune ?: null,
    //         'biographie'        => $this->biographie ?: null,
    //         'niveau_laravel'    => $this->niveau_laravel ?: null,
    //         'annees_experience' => $this->annees_experience ?: null,
    //         'stack_technique'   => count($this->stack_technique) > 0 ? $this->stack_technique : null,
    //         'niveau_academique' => $this->niveau_academique ?: null,
    //         'poste'             => $this->poste ?: null,
    //         'lien_portfolio'    => $this->lien_portfolio ?: null,
    //     ];

    //     // Upload avatar
    //     if ($this->avatarFile) {
    //         if ($user->profile?->avatar) {
    //             $old = public_path('assets/avatars/' . $user->profile->avatar);
    //             if (File::exists($old)) File::delete($old);
    //         }
    //         $filename       = 'avatar_' . $user->id . '_' . time() . '.' . $this->avatarFile->getClientOriginalExtension();
    //         $this->avatarFile->move(public_path('assets/avatars'), $filename);
    //         $data['avatar'] = $filename;
    //     }

    //     // Upload CV
    //     if ($this->cvFile) {
    //         if ($user->profile?->cv) {
    //             $old = public_path('assets/cv/' . $user->profile->cv);
    //             if (File::exists($old)) File::delete($old);
    //         }
    //         $filename      = 'cv_' . $user->id . '_' . time() . '.' . $this->cvFile->getClientOriginalExtension();
    //         $this->cvFile->move(public_path('assets/cv'), $filename);
    //         $data['cv']    = $filename;
    //     }

    //     $profile = Profile::updateOrCreate(['user_id' => $user->id], $data);

    //     // Rafraîchir l'état
    //     $this->completionRate = $profile->completionRate();
    //     $this->missingFields  = $profile->missingFields();
    //     $this->currentAvatar  = $profile->avatarUrl();
    //     $this->currentCv      = $profile->cvUrl();
    //     $this->avatarFile     = null;
    //     $this->cvFile         = null;

    //     session()->flash('success', 'Profil sauvegardé avec succès.');
    // }

    public function save(AssetService $assetService): void
    {
        $this->validate();

        $user = auth()->user();

        $data = [
            'user_id'           => $user->id,
            'pays'              => $this->pays ?: null,
            'ville'             => $this->ville ?: null,
            'commune'           => $this->commune ?: null,
            'biographie'        => $this->biographie ?: null,
            'niveau_laravel'    => $this->niveau_laravel ?: null,
            'annees_experience' => $this->annees_experience ?: null,
            'stack_technique'   => count($this->stack_technique) > 0 ? $this->stack_technique : null,
            'niveau_academique' => $this->niveau_academique ?: null,
            'poste'             => $this->poste ?: null,
            'lien_portfolio'    => $this->lien_portfolio ?: null,
        ];

        // Upload avatar via AssetService
        if ($this->avatarFile) {
            $data['avatar'] = $assetService->upload(
                file:    $this->avatarFile,
                folder:  'avatars',
                prefix:  'avatar',
                userId:  $user->id,
                old:     $user->profile?->avatar,
            );
        }

        // Upload CV via AssetService
        if ($this->cvFile) {
            $data['cv'] = $assetService->upload(
                file:   $this->cvFile,
                folder: 'cv',
                prefix: 'cv',
                userId: $user->id,
                old:    $user->profile?->cv,
            );
        }

        $profile = Profile::updateOrCreate(['user_id' => $user->id], $data);

        // Rafraîchir l'état
        $this->completionRate = $profile->completionRate();
        $this->missingFields  = $profile->missingFields();
        $this->currentAvatar  = $profile->avatarUrl();
        $this->currentCv      = $profile->cvUrl();
        $this->avatarFile     = null;
        $this->cvFile         = null;

        session()->flash('success', 'Profil sauvegardé avec succès.');
    }

    public function render(CountryService $countryService)
    {
        return view('livewire.edit-profile', [
            'countries'          => $countryService->getCountries(),
            'niveauxLaravel'     => Profile::$niveauxLaravel,
            'anneesExperience'   => Profile::$anneesExperience,
            'niveauxAcademiques' => Profile::$niveauxAcademiques,
            'postes'             => Profile::$postes,
            'stackPredefined'    => Profile::$stackPredefined,
        ]);
    }
}
