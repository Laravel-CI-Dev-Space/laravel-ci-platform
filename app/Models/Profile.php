<?php

namespace App\Models;

use App\Enums\AcademicLevel;
use App\Enums\JobStatus;
use App\Enums\LaravelLevel;
use App\Enums\YearsExperience;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'user_id',
    'avatar',
    'country',
    'city',
    'district',
    'bio',
    'laravel_level',
    'years_experience',
    'tech_stack',
    'academic_level',
    'job_status',
    'portfolio_url',
    'cv',
])]
class Profile extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'tech_stack' => 'array',
        ];
    }

    // ─── DONNÉES DE RÉFÉRENCE ─────────────────────────────

    public static array $stackPredefined = [
        'Langages'   => ['PHP', 'JavaScript', 'TypeScript', 'Python', 'Go', 'Rust', 'Java', 'C#', 'Swift', 'Kotlin'],
        'Frameworks' => ['Laravel', 'Livewire', 'Vue.js', 'React', 'Next.js', 'Nuxt.js', 'Angular', 'Symfony', 'Django', 'FastAPI'],
        'Outils'     => ['Git', 'Docker', 'Linux', 'MySQL', 'PostgreSQL', 'MongoDB', 'Redis', 'Nginx', 'AWS', 'Tailwind CSS'],
    ];

    // ─── TAUX DE COMPLÉTION ───────────────────────────────

    protected array $completionFields = [
        'avatar',
        'country',
        'city',
        'bio',
        'laravel_level',
        'years_experience',
        'tech_stack',
        'academic_level',
        'job_status',
        'portfolio_url',
        'cv',
    ];

    public function completionRate(): int
    {
        $filled = collect($this->completionFields)
            ->filter(function ($field) {
                $value = $this->$field;
                if (is_array($value)) {
                    return count($value) > 0;
                }

                return ! empty($value);
            })
            ->count();

        return (int) round(($filled / count($this->completionFields)) * 100);
    }

    public function missingFields(): array
    {
        $labels = [
            'avatar'           => 'Photo de profil',
            'country'          => 'Pays',
            'city'             => 'Ville',
            'bio'              => 'Biographie',
            'laravel_level'    => 'Niveau Laravel',
            'years_experience' => "Années d'expérience",
            'tech_stack'       => 'Stack technique',
            'academic_level'   => 'Niveau académique',
            'job_status'       => 'Situation professionnelle',
            'portfolio_url'    => 'Lien portfolio',
            'cv'               => 'CV',
        ];

        return collect($this->completionFields)
            ->filter(function ($field) {
                $value = $this->$field;
                if (is_array($value)) {
                    return count($value) === 0;
                }

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
     * URL avatar — uploadé (disque public) ou GitHub par défaut.
     * Passer $fallback depuis le contexte appelant évite le lazy load inverse vers User.
     */
    public function avatarUrl(?string $fallback = null): string
    {
        if ($this->avatar) {
            return Storage::disk('public')->url("avatars/{$this->avatar}");
        }

        if ($fallback !== null) {
            return $fallback;
        }

        return $this->relationLoaded('user')
            ? $this->user->avatar
            : ($this->user()->value('avatar') ?? '');
    }

    /**
     * Route sécurisée vers le CV (authentification requise).
     */
    public function cvUrl(): ?string
    {
        return $this->cv ? route('cv.download', $this->user_id) : null;
    }

    public function laravelLevelLabel(): string
    {
        return LaravelLevel::tryFrom($this->laravel_level ?? '')?->label() ?? '—';
    }

    public function yearsExperienceLabel(): string
    {
        return YearsExperience::tryFrom($this->years_experience ?? '')?->label() ?? '—';
    }

    public function academicLevelLabel(): string
    {
        return AcademicLevel::tryFrom($this->academic_level ?? '')?->label() ?? '—';
    }

    public function jobStatusLabel(): string
    {
        return JobStatus::tryFrom($this->job_status ?? '')?->label() ?? '—';
    }
}
