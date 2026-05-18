<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'avatar',
    'pays',
    'ville',
    'commune',
    'biographie',
    'niveau_laravel',
    'annees_experience',
    'stack_technique',
    'niveau_academique',
    'poste',
    'lien_portfolio',
    'cv',
])]
class Profile extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'stack_technique' => 'array',
        ];
    }

    // ─── LABELS ──────────────────────────────────────────

    public static array $niveauxLaravel = [
        'debutant'      => 'Débutant (moins de 5 projets)',
        'intermediaire' => 'Intermédiaire (6 à 14 projets)',
        'avance'        => 'Avancé (15 à 25 projets)',
        'expert'        => 'Expert (26 à 40 projets)',
        'maitre'        => 'Maître (plus de 50 projets)',
    ];

    public static array $anneesExperience = [
        'moins_1_an'  => 'Moins d\'1 an',
        '1_3_ans'     => '1 à 3 ans',
        '3_5_ans'     => '3 à 5 ans',
        '5_10_ans'    => '5 à 10 ans',
        'plus_10_ans' => 'Plus de 10 ans',
    ];

    public static array $niveauxAcademiques = [
        'bts'              => 'BTS',
        'licence'          => 'Licence',
        'master_ingenieur' => 'Master / Ingénieur',
        'doctorat'         => 'Doctorat',
    ];

    public static array $postes = [
        'en_fonction'      => 'En fonction',
        'etudiant'         => 'Étudiant',
        'entrepreneur'     => 'Entrepreneur',
        'recherche_emploi' => 'En recherche d\'emploi',
        'freelance'        => 'Freelance',
    ];

    public static array $stackPredefined = [
        'Langages'   => ['PHP', 'JavaScript', 'TypeScript', 'Python', 'Go', 'Rust', 'Java', 'C#', 'Swift', 'Kotlin'],
        'Frameworks' => ['Laravel', 'Livewire', 'Vue.js', 'React', 'Next.js', 'Nuxt.js', 'Angular', 'Symfony', 'Django', 'FastAPI'],
        'Outils'     => ['Git', 'Docker', 'Linux', 'MySQL', 'PostgreSQL', 'MongoDB', 'Redis', 'Nginx', 'AWS', 'Tailwind CSS'],
    ];

    // ─── TAUX DE COMPLÉTION ───────────────────────────────

    protected array $completionFields = [
        'avatar',
        'pays',
        'ville',
        'biographie',
        'niveau_laravel',
        'annees_experience',
        'stack_technique',
        'niveau_academique',
        'poste',
        'lien_portfolio',
        'cv',
    ];

    /**
     * Calcule le taux de complétion du profil en pourcentage.
     */
    public function completionRate(): int
    {
        $filled = collect($this->completionFields)
            ->filter(function ($field) {
                $value = $this->$field;
                if (is_array($value)) return count($value) > 0;
                return ! empty($value);
            })
            ->count();

        return (int) round(($filled / count($this->completionFields)) * 100);
    }

    /**
     * Retourne les champs manquants.
     */
    public function missingFields(): array
    {
        $labels = [
            'avatar'            => 'Photo de profil',
            'pays'              => 'Pays',
            'ville'             => 'Ville',
            'biographie'        => 'Biographie',
            'niveau_laravel'    => 'Niveau Laravel',
            'annees_experience' => 'Années d\'expérience',
            'stack_technique'   => 'Stack technique',
            'niveau_academique' => 'Niveau académique',
            'poste'             => 'Situation professionnelle',
            'lien_portfolio'    => 'Lien portfolio',
            'cv'                => 'CV',
        ];

        return collect($this->completionFields)
            ->filter(function ($field) {
                $value = $this->$field;
                if (is_array($value)) return count($value) === 0;
                return empty($value);
            })
            ->map(fn ($field) => $labels[$field] ?? $field)
            ->values()
            ->toArray();
    }

    // ─── RELATIONS ───────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── HELPERS ─────────────────────────────────────────

    /**
     * URL avatar — uploadé ou GitHub par défaut.
     */
    public function avatarUrl(): string
    {
        if ($this->avatar) {
            return asset('assets/avatars/' . $this->avatar);
        }
        return $this->user->avatar;
    }

    /**
     * URL du CV.
     */
    public function cvUrl(): ?string
    {
        if ($this->cv) {
            return asset('assets/cv/' . $this->cv);
        }
        return null;
    }

    public function niveauLaravelLabel(): string
    {
        return static::$niveauxLaravel[$this->niveau_laravel] ?? '—';
    }

    public function anneesExperienceLabel(): string
    {
        return static::$anneesExperience[$this->annees_experience] ?? '—';
    }

    public function niveauAcademiqueLabel(): string
    {
        return static::$niveauxAcademiques[$this->niveau_academique] ?? '—';
    }

    public function posteLabel(): string
    {
        return static::$postes[$this->poste] ?? '—';
    }
}
