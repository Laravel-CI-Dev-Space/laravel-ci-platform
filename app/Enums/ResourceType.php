<?php

declare(strict_types=1);

namespace App\Enums;

enum ResourceType: string
{
    case Boilerplate = 'boilerplate';
    case Cheatsheet  = 'cheatsheet';
    case Guide       = 'guide';
    case Pdf         = 'pdf';
    case Other       = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Boilerplate => 'Boilerplate',
            self::Cheatsheet  => 'Cheatsheet',
            self::Guide       => 'Guide',
            self::Pdf         => 'PDF',
            self::Other       => 'Autre',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Boilerplate => 'fa-solid fa-cubes',
            self::Cheatsheet  => 'fa-solid fa-list-check',
            self::Guide       => 'fa-solid fa-book-open',
            self::Pdf         => 'fa-solid fa-file-pdf',
            self::Other       => 'fa-solid fa-file',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Boilerplate => 'info',
            self::Cheatsheet  => 'warning',
            self::Guide       => 'success',
            self::Pdf         => 'danger',
            self::Other       => 'gray',
        };
    }
}
