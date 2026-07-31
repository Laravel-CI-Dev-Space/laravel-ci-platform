<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\UserRole;
use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Artisan;

class ReputationSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.reputation-settings';

    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static ?string $navigationLabel = 'Réputation & Grades';

    protected static ?string $title = 'Réputation & Grades';

    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return 'Communauté';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole([
            UserRole::SuperAdmin->value,
            UserRole::SuperAdmin->value,
        ]) ?? false;
    }

    public function mount(): void
    {
        $this->form->fill([
            'points_question'        => (int) SiteSetting::get('reputation_points_question', 5),
            'points_answer'          => (int) SiteSetting::get('reputation_points_answer', 10),
            'points_accepted_answer' => (int) SiteSetting::get('reputation_points_accepted_answer', 20),
            'points_article'         => (int) SiteSetting::get('reputation_points_article', 25),
            'points_vote'            => (int) SiteSetting::get('reputation_points_vote', 2),
            'points_comment'         => (int) SiteSetting::get('reputation_points_comment', 1),
            'points_event'           => (int) SiteSetting::get('reputation_points_event', 5),
            'points_seniority_month' => (int) SiteSetting::get('reputation_points_seniority_month', 2),
            'max_seniority_months'   => (int) SiteSetting::get('reputation_max_seniority_months', 25),
            'recalculate_frequency'  => SiteSetting::get('reputation_recalculate_frequency', 'monthly'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Points par action')
                    ->description('Nombre de points de réputation attribués pour chaque type d\'action.')
                    ->columns(3)
                    ->schema([
                        TextInput::make('points_question')
                            ->label('Question publiée')
                            ->numeric()
                            ->minValue(0)
                            ->required(),

                        TextInput::make('points_answer')
                            ->label('Réponse postée')
                            ->numeric()
                            ->minValue(0)
                            ->required(),

                        TextInput::make('points_accepted_answer')
                            ->label('Bonus réponse acceptée')
                            ->numeric()
                            ->minValue(0)
                            ->required(),

                        TextInput::make('points_article')
                            ->label('Article publié')
                            ->numeric()
                            ->minValue(0)
                            ->required(),

                        TextInput::make('points_vote')
                            ->label('Vote reçu')
                            ->numeric()
                            ->minValue(0)
                            ->required(),

                        TextInput::make('points_comment')
                            ->label('Commentaire')
                            ->numeric()
                            ->minValue(0)
                            ->required(),

                        TextInput::make('points_event')
                            ->label('Participation à un événement')
                            ->numeric()
                            ->minValue(0)
                            ->required(),

                        TextInput::make('points_seniority_month')
                            ->label('Par mois d\'ancienneté')
                            ->numeric()
                            ->minValue(0)
                            ->required(),

                        TextInput::make('max_seniority_months')
                            ->label('Plafond ancienneté (mois)')
                            ->numeric()
                            ->minValue(0)
                            ->required(),
                    ]),

                Section::make('Recalcul automatique')
                    ->description('Fréquence à laquelle les points et grades de tous les membres sont recalculés automatiquement.')
                    ->schema([
                        Select::make('recalculate_frequency')
                            ->label('Fréquence de recalcul')
                            ->options([
                                'daily'   => 'Quotidienne',
                                'weekly'  => 'Hebdomadaire',
                                'monthly' => 'Mensuelle',
                            ])
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            SiteSetting::set('reputation_' . $key, (string) $value);
        }

        Notification::make()
            ->title('Paramètres enregistrés')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('recalculate')
                ->label('Recalculer maintenant')
                ->icon(Heroicon::OutlinedArrowPath)
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Recalculer les points et grades de tous les membres ?')
                ->modalDescription('Cette opération peut prendre du temps selon le nombre de membres.')
                ->action(function (): void {
                    Artisan::call('reputation:recalculate');

                    Notification::make()
                        ->title('Recalcul effectué')
                        ->success()
                        ->send();
                }),
        ];
    }
}
