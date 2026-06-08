<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\HasAutoSave;
use App\Models\VitrineSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class EditAboutPage extends Page implements HasForms
{
    use InteractsWithForms;
    use HasAutoSave;

    protected string $view = 'filament.pages.vitrine-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInformationCircle;

    protected static ?string $navigationLabel = 'Page About';

    protected static ?string $title = 'Éditer la page About';

    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return 'Vitrine';
    }

    public function mount(): void
    {
        $this->form->fill(VitrineSetting::getGroup('about'));
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
                        TextInput::make('about_hero_badge')->label('Badge')->required(),
                        TextInput::make('about_hero_cta')->label('Texte du bouton CTA'),
                        TextInput::make('about_hero_title')->label('Titre')->required()->columnSpanFull(),
                        Textarea::make('about_hero_lead')->label('Sous-titre')->rows(3)->required()->columnSpanFull(),
                    ]),

                Section::make('Mission & Vision')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextInput::make('about_mission_title')->label('Titre Mission')->required(),
                        TextInput::make('about_vision_title')->label('Titre Vision')->required(),
                        Textarea::make('about_mission_body')->label('Texte Mission')->rows(4)->required(),
                        Textarea::make('about_vision_body')->label('Texte Vision')->rows(4)->required(),
                    ]),

                Section::make('Valeurs')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Repeater::make('about_values_items')
                            ->label('Cartes de valeurs')
                            ->schema([
                                TextInput::make('icon')->label('Icône FontAwesome')->placeholder('fa-solid fa-award')->required(),
                                TextInput::make('title')->label('Titre')->required(),
                                Textarea::make('body')->label('Description')->rows(2)->required(),
                            ])
                            ->columns(3)
                            ->reorderable()
                            ->addActionLabel('Ajouter une valeur'),
                    ]),

                Section::make('Équipe fondatrice')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Repeater::make('about_team_members')
                            ->label('Membres')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nom complet')
                                    ->required(),
                                TextInput::make('role')
                                    ->label('Rôle')
                                    ->required(),
                                TextInput::make('github')
                                    ->label('URL GitHub')
                                    ->url(),

                                Select::make('avatar_type')
                                    ->label('Type d\'avatar')
                                    ->options([
                                        'initials' => 'Initiales',
                                        'upload'   => 'Upload de photo',
                                        'url'      => 'URL externe',
                                    ])
                                    ->default('initials')
                                    ->live()
                                    ->required(),
                                TextInput::make('initials')
                                    ->label('Initiales')
                                    ->maxLength(2)
                                    ->required(fn (Get $get) => $get('avatar_type') === 'initials' || ! $get('avatar_type'))
                                    ->visible(fn (Get $get) => ($get('avatar_type') ?? 'initials') === 'initials'),
                                TextInput::make('avatar_class')
                                    ->label('Classe de couleur')
                                    ->placeholder('av-1')
                                    ->visible(fn (Get $get) => ($get('avatar_type') ?? 'initials') === 'initials'),

                                TextInput::make('avatar_url')
                                    ->label('URL de la photo')
                                    ->url()
                                    ->placeholder('https://avatars.githubusercontent.com/u/…')
                                    ->columnSpanFull()
                                    ->visible(fn (Get $get) => $get('avatar_type') === 'url'),

                                FileUpload::make('avatar_photo')
                                    ->label('Photo')
                                    ->disk('public')
                                    ->directory('team/' . date('Y'))
                                    ->image()
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('1:1')
                                    ->imageResizeTargetWidth('200')
                                    ->imageResizeTargetHeight('200')
                                    ->fetchFileInformation(false)
                                    ->columnSpanFull()
                                    ->visible(fn (Get $get) => $get('avatar_type') === 'upload'),
                            ])
                            ->columns(3)
                            ->reorderable()
                            ->addActionLabel('Ajouter un membre'),
                    ]),

                Section::make('CTA final')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextInput::make('about_cta_title')->label('Titre')->required(),
                        Textarea::make('about_cta_lead')->label('Description')->rows(2)->required(),
                        TextInput::make('about_cta_button')->label('Texte du bouton'),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->clearCacheAction(),
            $this->autoSaveAction(),
            Action::make('save')
                ->label('Enregistrer')
                ->icon('heroicon-o-check')
                ->action(fn () => $this->save()),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $code => $value) {
            $encoded = is_array($value) ? json_encode($value) : $value;
            VitrineSetting::where('code', $code)->update(['value' => $encoded]);
        }

        VitrineSetting::forgetGroup('about');

        Notification::make()
            ->title('Page About mise à jour')
            ->success()
            ->send();
    }
}
