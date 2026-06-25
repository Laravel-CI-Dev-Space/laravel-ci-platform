<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\UserRole;
use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Artisan;

class CardSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.card-settings';

    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static ?string $navigationLabel = 'Cartes membres — Seuils';

    protected static ?string $title = 'Paramètres des cartes membres';

    protected static ?int $navigationSort = 6;

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return 'Communauté';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole([
            UserRole::SuperAdmin->value,
            UserRole::Admin->value,
        ]) ?? false;
    }

    public function mount(): void
    {
        $defaults = config('member-card.thresholds', [1 => 300, 2 => 600, 3 => 900]);

        $this->form->fill([
            'card_level_1_points' => (int) (SiteSetting::firstWhere('key', 'card_level_1_points')?->value ?? $defaults[1]),
            'card_level_2_points' => (int) (SiteSetting::firstWhere('key', 'card_level_2_points')?->value ?? $defaults[2]),
            'card_level_3_points' => (int) (SiteSetting::firstWhere('key', 'card_level_3_points')?->value ?? $defaults[3]),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $levelNames = config('member-card.level_names', [1 => 'Initié', 2 => 'Bâtisseur', 3 => 'Maître Artisan']);

        return $schema
            ->components([
                Section::make('Seuils de réputation pour débloquer les cartes')
                    ->description('Nombre minimum de points de réputation requis pour qu\'une carte soit débloquée automatiquement. Les admins peuvent toujours forcer l\'activation sans seuil.')
                    ->columns(3)
                    ->schema([
                        TextInput::make('card_level_1_points')
                            ->label("Niveau 1 — {$levelNames[1]}")
                            ->helperText('Points pour débloquer la carte Initié')
                            ->numeric()
                            ->minValue(1)
                            ->required(),

                        TextInput::make('card_level_2_points')
                            ->label("Niveau 2 — {$levelNames[2]}")
                            ->helperText('Points pour débloquer la carte Bâtisseur')
                            ->numeric()
                            ->minValue(1)
                            ->required(),

                        TextInput::make('card_level_3_points')
                            ->label("Niveau 3 — {$levelNames[3]}")
                            ->helperText('Points pour débloquer la carte Maître Artisan')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => (string) $value]
            );
        }

        Notification::make()
            ->title('Seuils mis à jour')
            ->body('Les nouveaux seuils seront appliqués lors des prochains recalculs de réputation.')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sync_cards')
                ->label('Synchroniser toutes les cartes')
                ->icon(Heroicon::OutlinedArrowPath)
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Synchroniser les cartes de tous les membres ?')
                ->modalDescription('Parcourt tous les membres et crée les cartes manquantes selon leur réputation actuelle. Peut prendre du temps.')
                ->action(function (): void {
                    Artisan::call('cards:sync');

                    Notification::make()
                        ->title('Synchronisation effectuée')
                        ->success()
                        ->send();
                }),
        ];
    }
}
