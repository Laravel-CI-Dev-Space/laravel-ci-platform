<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\UserRole;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

/**
 * Page d'action permettant à un admin/super-admin/modérateur de basculer
 * instantanément en mode "Naviguer en tant que Membre" depuis le panel admin.
 */
class ViewAsMember extends Page
{
    protected string $view = 'filament.pages.view-as-member';

    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedEye;

    protected static ?string $navigationLabel = 'Voir en tant que membre';

    protected static ?string $title = 'Voir en tant que membre';

    protected static ?int $navigationSort = 99;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole([
            UserRole::SuperAdmin->value,
            UserRole::SuperAdmin->value,
        ]) ?? false;
    }

    /**
     * Active immédiatement le mode "Naviguer en tant que Membre" et redirige vers l'accueil.
     */
    public function mount()
    {
        session(['viewing_as_member' => true]);

        return redirect()->route('dashboard.member.overview')
            ->with('success', 'Vous naviguez désormais en tant que membre.');
    }
}
