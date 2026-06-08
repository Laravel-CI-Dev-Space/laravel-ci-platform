<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\HasAutoSave;
use App\Models\VitrineSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class EditVitrineSettingsPage extends Page implements HasForms
{
    use HasAutoSave;
    use InteractsWithForms;

    protected string $view = 'filament.pages.vitrine-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Paramètres globaux';

    protected static ?string $title = 'Paramètres globaux de la vitrine';

    protected static ?int $navigationSort = 3;

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return 'Vitrine';
    }

    public function mount(): void
    {
        $data                        = VitrineSetting::getGroup('global');
        $links                       = $data['global_social_links'] ?? [];
        $data['global_social_links'] = is_array($links) ? $links : (json_decode($links, true) ?: []);
        $this->form->fill($data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('SEO par défaut')
                    ->description('Valeurs utilisées sur les pages qui ne définissent pas leur propre titre/description.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextInput::make('global_default_title')
                            ->label('Titre par défaut')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('global_default_description')
                            ->label('Description par défaut')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('global_og_image')
                            ->label('Chemin OG Image (relatif à public/)')
                            ->placeholder('assets/web/img/og.png')
                            ->columnSpanFull(),
                    ]),

                Section::make('Liens sociaux')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Repeater::make('global_social_links')
                            ->label('')
                            ->schema([
                                Hidden::make('platform')
                                    ->default('github'),
                                TextInput::make('url')
                                    ->label(fn (Get $get) => match ($get('platform')) {
                                        'linkedin'  => 'LinkedIn',
                                        'whatsapp'  => 'WhatsApp',
                                        'twitter'   => 'Twitter / X',
                                        'facebook'  => 'Facebook',
                                        'youtube'   => 'YouTube',
                                        'instagram' => 'Instagram',
                                        'discord'   => 'Discord',
                                        default     => 'GitHub',
                                    })
                                    ->url()
                                    ->required()
                                    ->columnSpanFull()
                                    ->prefixActions([
                                        Action::make('selectPlatform')
                                            ->icon(fn (Get $get) => 'social-' . ($get('platform') ?: 'github'))
                                            ->color('gray')
                                            ->tooltip('Changer le réseau social')
                                            ->schema([
                                                Select::make('platform')
                                                    ->label('Réseau social')
                                                    ->options([
                                                        'github'    => 'GitHub',
                                                        'linkedin'  => 'LinkedIn',
                                                        'whatsapp'  => 'WhatsApp',
                                                        'twitter'   => 'Twitter / X',
                                                        'facebook'  => 'Facebook',
                                                        'youtube'   => 'YouTube',
                                                        'instagram' => 'Instagram',
                                                        'discord'   => 'Discord',
                                                    ])
                                                    ->required(),
                                            ])
                                            ->mountUsing(function (Schema $schema, Get $get): void {
                                                $schema->fill(['platform' => $get('platform') ?: 'github']);
                                            })
                                            ->action(function (array $data, Set $set): void {
                                                $set('platform', $data['platform']);
                                            })
                                            ->modalHeading('Choisir le réseau social')
                                            ->modalWidth('sm'),
                                    ]),
                            ])
                            ->columns(1)
                            ->reorderable()
                            ->addActionLabel('Ajouter un réseau social'),
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
            $encoded = is_array($value) ? json_encode($value) : ($value ?? '');
            VitrineSetting::where('code', $code)->update(['value' => $encoded]);
        }

        VitrineSetting::forgetGroup('global');

        Notification::make()
            ->title('Paramètres mis à jour')
            ->success()
            ->send();
    }
}
