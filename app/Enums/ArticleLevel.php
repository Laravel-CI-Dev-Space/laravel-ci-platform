<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum ArticleLevel: string
{
    use HasOptions;

    case Beginner     = 'beginner';
    case Intermediate = 'intermediate';
    case Advanced     = 'advanced';

    public function label(): string
    {
        return match ($this) {
            self::Beginner     => 'Débutant',
            self::Intermediate => 'Intermédiaire',
            self::Advanced     => 'Avancé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Beginner     => 'success',
            self::Intermediate => 'warning',
            self::Advanced     => 'danger',
        };
    }

    /** Couleur CSS utilisée pour les bordures/accents dans la vitrine. */
    public function accentColor(): string
    {
        return match ($this) {
            self::Beginner     => 'var(--green,#2ecc71)',
            self::Intermediate => 'var(--orange,#e8590c)',
            self::Advanced     => 'var(--level-advanced,#e74c3c)',
        };
    }

    /** Couleur de fond du badge dans la vitrine. */
    public function badgeBackground(): string
    {
        return match ($this) {
            self::Beginner     => '#edfaf3',
            self::Intermediate => '#fff5f0',
            self::Advanced     => '#fdeaec',
        };
    }

    /** Classe CSS de la bannière de niveau dans la vitrine. */
    public function bannerClass(): string
    {
        return match ($this) {
            self::Beginner     => 'lv-beginner',
            self::Intermediate => 'lv-intermediate',
            self::Advanced     => 'lv-advanced',
        };
    }
}
