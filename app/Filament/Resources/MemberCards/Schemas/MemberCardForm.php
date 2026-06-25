<?php

namespace App\Filament\Resources\MemberCards\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MemberCardForm
{
    public static function configure(Schema $schema): Schema
    {
        $levelNames = config('member-card.level_names', [1 => 'Initié', 2 => 'Bâtisseur', 3 => 'Maître Artisan']);

        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Membre')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

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
