<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\HasAutoSave;
use App\Models\VitrineSetting;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class EditHomePage extends Page implements HasForms
{
    use InteractsWithForms;
    use HasAutoSave;

    protected string $view = 'filament.pages.vitrine-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'Page Home';

    protected static ?string $title = 'Éditer la page Home';

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return 'Vitrine';
    }

    public function mount(): void
    {
        $this->form->fill(VitrineSetting::getGroup('home'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Hero')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextInput::make('home_hero_badge')->label('Badge')->required(),
                        TextInput::make('home_hero_title')->label('Titre')->required()->columnSpanFull(),
                        Textarea::make('home_hero_lead')->label('Sous-titre')->rows(3)->required()->columnSpanFull(),
                        TextInput::make('home_hero_cta_primary')->label('Bouton principal'),
                        TextInput::make('home_hero_cta_secondary')->label('Bouton secondaire'),
                    ]),

                Section::make('Statistiques')
                    ->columns(4)
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextInput::make('home_stats_members')->label('Membres')->numeric()->required(),
                        TextInput::make('home_stats_questions')->label('Questions')->numeric()->required(),
                        TextInput::make('home_stats_events')->label('Événements')->numeric()->required(),
                        TextInput::make('home_stats_articles')->label('Articles')->numeric()->required(),
                    ]),

                Section::make('CTA final')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextInput::make('home_cta_title')->label('Titre')->required(),
                        Textarea::make('home_cta_lead')->label('Description')->rows(2)->required(),
                        TextInput::make('home_cta_button')->label('Texte du bouton'),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->clearCacheAction(),
            $this->autoSaveAction(),
            \Filament\Actions\Action::make('save')
                ->label('Enregistrer')
                ->icon('heroicon-o-check')
                ->action(fn () => $this->save()),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $code => $value) {
            VitrineSetting::where('code', $code)->update(['value' => $value]);
        }

        VitrineSetting::forgetGroup('home');

        Notification::make()
            ->title('Page Home mise à jour')
            ->success()
            ->send();
    }
}
