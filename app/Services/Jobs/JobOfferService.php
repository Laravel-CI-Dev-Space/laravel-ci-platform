<?php

declare(strict_types=1);

namespace App\Services\Jobs;

use App\Models\Company;
use App\Models\JobFavorite;
use App\Models\JobOffer;
use App\Models\User;
use App\Services\AssetService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class JobOfferService
{
    public function __construct(private readonly AssetService $assetService) {}
    /**
     * Récupère les offres actives avec filtres et pagination.
     *
     * @param  array  $filters  — contract_type, level, is_remote, location, category_id, skill_id, search, sort
     */
    public function getOffers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = JobOffer::active()
            ->notExpired()
            ->with(['company', 'categories', 'skills'])
            ->withCount('applications');

        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                    ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        if (! empty($filters['contract_type'])) {
            $query->where('contract_type', $filters['contract_type']);
        }

        if (! empty($filters['level'])) {
            $query->where('level', $filters['level']);
        }

        if (! empty($filters['is_remote'])) {
            $query->where('is_remote', true);
        }

        if (! empty($filters['location'])) {
            $query->where('location', 'like', "%{$filters['location']}%");
        }

        if (! empty($filters['category_id'])) {
            $query->whereHas('categories', fn ($q) => $q->where('job_offer_categories.id', $filters['category_id']));
        }

        if (! empty($filters['skill_id'])) {
            $query->whereHas('skills', fn ($q) => $q->where('job_skills.id', $filters['skill_id']));
        }

        return match ($filters['sort'] ?? 'recent') {
            'urgent'  => $query->orderByDesc('is_urgent')->orderByDesc('published_at')->paginate($perPage),
            'closing' => $query->orderBy('expires_at')->paginate($perPage),
            default   => $query->orderByDesc('published_at')->paginate($perPage),
        };
    }

    /**
     * Récupère une offre par son slug.
     * Eager loads : company, categories, skills.
     */
    public function getBySlug(string $slug): JobOffer
    {
        return JobOffer::with(['company', 'categories', 'skills', 'poster'])
            ->where('slug', $slug)
            ->firstOrFail();
    }

    /**
     * Crée une offre d'emploi (status: pending — attend validation admin).
     * Attache les catégories et les compétences.
     * Définit expires_at à now() + 30 jours.
     */
    /**
     * Crée une offre d'emploi (status: pending — attend validation admin).
     * Attache les catégories et les compétences.
     * Définit expires_at à now() + 30 jours.
     * Gère l'upload de l'image de couverture et du document joint.
     *
     * @param  mixed|null  $coverImage   Fichier image Livewire / Laravel UploadedFile
     * @param  mixed|null  $attachment   Fichier PDF/DOC Livewire / Laravel UploadedFile
     */
    public function createOffer(
        array $data,
        ?Company $company = null,
        ?User $user = null,
        mixed $coverImage = null,
        mixed $attachment = null,
    ): JobOffer {
        // ── Image de couverture ──────────────────────────────────────────
        // $data['cover_image'] peut déjà contenir un filename (Filament normalise via
        // mutateFormDataBeforeCreate). $coverImage est un fichier brut (Livewire/API).
        $finalCoverImage = $data['cover_image'] ?? null;
        if ($coverImage !== null) {
            // Upload via AssetService → stocke dans public/assets/job-covers/
            $finalCoverImage = $this->assetService->upload(
                $coverImage, 'job-covers', 'cover', $company?->id ?? 0
            );
        }

        // ── Document joint (PDF/DOC) ─────────────────────────────────────
        $finalAttachmentPath = $data['attachment_path'] ?? null;
        $finalAttachmentName = $data['attachment_name'] ?? null;
        if ($attachment !== null) {
            $finalAttachmentPath = $this->assetService->upload(
                $attachment, 'job-attachments', 'doc', $company?->id ?? 0
            );
            $finalAttachmentName = method_exists($attachment, 'getClientOriginalName')
                ? $attachment->getClientOriginalName()
                : basename($finalAttachmentPath);
        }

        $offer = JobOffer::create([
            'company_id'      => $company?->id,
            'posted_by'       => $user?->id,   // nullable — les comptes entreprise n'ont pas de user lié
            'title'           => $data['title'],
            'slug'            => $this->generateUniqueSlug($data['title']),
            'description'     => $data['description'],
            'contract_type'   => $data['contract_type'],
            'level'           => $data['level'],
            'location'        => $data['location']    ?? null,
            'country'         => $data['country']     ?? null,
            'is_remote'       => (bool) ($data['is_remote']  ?? false),
            'is_hybrid'       => (bool) ($data['is_hybrid']  ?? false),
            'salary_min'      => $data['salary_min']  ?? null,
            'salary_max'      => $data['salary_max']  ?? null,
            'currency'        => $data['currency']    ?? 'XOF',
            'salary_visible'  => (bool) ($data['salary_visible'] ?? true),
            'is_urgent'       => (bool) ($data['is_urgent']  ?? false),
            'cover_image'     => $finalCoverImage,
            'attachment_path' => $finalAttachmentPath,
            'attachment_name' => $finalAttachmentName,
            'status'          => 'pending',
            'expires_at'      => now()->addDays(30),
        ]);

        if (! empty($data['categories'])) {
            $offer->categories()->sync($data['categories']);
        }

        if (! empty($data['skills'])) {
            $offer->skills()->sync($data['skills']);
        }

        return $offer;
    }

    /**
     * Incrémente le compteur de vues (une seule fois par session).
     */
    public function incrementViews(JobOffer $offer): void
    {
        $key = "job_offer_viewed_{$offer->id}";

        if (! session()->has($key)) {
            $offer->increment('views_count');
            session()->put($key, true);
        }
    }

    /**
     * Toggle favori pour un membre.
     *
     * @return bool — true si ajouté, false si retiré
     */
    public function toggleFavorite(User $user, JobOffer $offer): bool
    {
        $existing = JobFavorite::where('user_id', $user->id)
            ->where('job_offer_id', $offer->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return false;
        }

        JobFavorite::create([
            'user_id'      => $user->id,
            'job_offer_id' => $offer->id,
        ]);

        return true;
    }

    /**
     * Expire les offres dont expires_at est passé.
     * Retourne le nombre d'offres expirées.
     */
    public function expireOldOffers(): int
    {
        return JobOffer::active()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);
    }

    /**
     * Récupère des offres similaires (même catégorie ou compétences).
     */
    public function getSimilarOffers(JobOffer $offer, int $limit = 3): Collection
    {
        $categoryIds = $offer->categories->pluck('id');
        $skillIds    = $offer->skills->pluck('id');

        return JobOffer::active()
            ->notExpired()
            ->with(['company', 'categories'])
            ->where('id', '!=', $offer->id)
            ->where(function ($q) use ($categoryIds, $skillIds) {
                $q->whereHas('categories', fn ($s) => $s->whereIn('job_offer_categories.id', $categoryIds))
                    ->orWhereHas('skills', fn ($s) => $s->whereIn('job_skills.id', $skillIds));
            })
            ->limit($limit)
            ->get();
    }

    private function generateUniqueSlug(string $title): string
    {
        $slug     = Str::slug($title);
        $original = $slug;
        $count    = 1;

        while (JobOffer::where('slug', $slug)->exists()) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }
}
