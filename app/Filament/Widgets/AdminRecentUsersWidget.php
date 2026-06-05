<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AdminRecentUsersWidget extends BaseWidget
{
    protected static ?string $heading = 'Nouveaux membres';

    protected static ?int $sort = 5;

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole([
            UserRole::SuperAdmin->value,
            UserRole::Admin->value,
            UserRole::Moderator->value,
        ]) ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::with('roles')
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                ImageColumn::make('avatar')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn (User $u) => 'https://ui-avatars.com/api/?name=' . urlencode($u->name) . '&color=fff&background=e8590c')
                    ->size(36),

                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('github_username')
                    ->label('GitHub')
                    ->formatStateUsing(fn ($state) => $state ? "@{$state}" : '—')
                    ->color('gray')
                    ->url(fn (User $u) => $u->github_username ? "https://github.com/{$u->github_username}" : null)
                    ->openUrlInNewTab(),

                TextColumn::make('roles.name')
                    ->label('Rôle')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'super-admin' => 'danger',
                        'admin'       => 'warning',
                        'moderator'   => 'primary',
                        'member'      => 'success',
                        default       => 'gray',
                    }),

                TextColumn::make('is_active')
                    ->label('Statut')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn ($state) => $state ? 'Actif' : 'Inactif'),

                TextColumn::make('created_at')
                    ->label('Inscrit le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->color('gray'),
            ])
            ->paginated(false);
    }
}
