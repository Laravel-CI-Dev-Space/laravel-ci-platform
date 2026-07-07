<?php

namespace App\Filament\Resources\MemberCards\Schemas;

use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Schemas\Schema;

class MemberCardForm
{
    public static function configure(Schema $schema): Schema
    {
        $levelNames = config('member-card.level_names', [1 => 'Initié', 2 => 'Bâtisseur', 3 => 'Maître Artisan']);
        $thresholds = config('member-card.thresholds', [1 => 300, 2 => 600, 3 => 900]);

        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Membre')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live(),

                Placeholder::make('member_points_info')
                    ->label('Points actuels')
                    ->content(function (Get $get) use ($thresholds): string {
                        $userId = $get('user_id');
                        if (! $userId) {
                            return '— sélectionnez un membre';
                        }
                        $user   = User::with('profile')->find($userId);
                        $points = $user?->profile?->points ?? 0;
                        $hints  = collect($thresholds)
                            ->map(fn ($pts, $lvl) => "N{$lvl} ≥ {$pts} pts")
                            ->join(' · ');

                        $ok = collect($thresholds)
                            ->filter(fn ($pts) => $points >= $pts)
                            ->count();

                        $badge = $ok > 0 ? "✅ éligible niveau {$ok}" : '⚠️ aucun niveau atteint';

                        return "{$points} pts — {$badge} ({$hints})";
                    }),

                Select::make('level')
                    ->label('Niveau')
                    ->options($levelNames)
                    ->required()
                    ->default(1),

                TextInput::make('poste')
                    ->label('Poste affiché')
                    ->maxLength(120)
                    ->placeholder('ex: Développeur Full-Stack'),

                TextInput::make('card_avatar')
                    ->label('Avatar personnalisé (URL)')
                    ->url()
                    ->maxLength(500),

                Toggle::make('is_active')
                    ->label('Carte active')
                    ->inline(false),

                Toggle::make('forced_by_admin')
                    ->label('Activée manuellement par admin')
                    ->helperText('Ignorer le seuil de réputation')
                    ->inline(false),

                DateTimePicker::make('activated_at')
                    ->label('Activée le'),
            ]);
    }
}
