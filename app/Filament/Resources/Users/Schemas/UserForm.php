<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Http;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('GitHub Information')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Full name')
                        ->required(),

                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required(),

                    TextInput::make('github_username')
                        ->label('GitHub username')
                        ->prefix('@')
                        ->required()
                        ->suffixAction(
                            Action::make('resolveGithubId')
                                ->icon('heroicon-o-magnifying-glass')
                                ->tooltip('Fetch GitHub ID from this username')
                                ->action(function (Get $get, Set $set) {
                                    $username = trim((string) $get('github_username'));

                                    if (! $username) {
                                        Notification::make()
                                            ->title('Enter a GitHub username first')
                                            ->warning()
                                            ->send();

                                        return;
                                    }

                                    $response = Http::withHeaders([
                                        'Accept'     => 'application/vnd.github.v3+json',
                                        'User-Agent' => 'LaravelCI-Admin',
                                    ])->get("https://api.github.com/users/{$username}");

                                    if ($response->successful()) {
                                        $githubId = (string) $response->json('id');
                                        $set('github_id', $githubId);

                                        Notification::make()
                                            ->title("GitHub ID resolved: {$githubId}")
                                            ->success()
                                            ->send();
                                    } else {
                                        Notification::make()
                                            ->title("GitHub user \"{$username}\" not found")
                                            ->danger()
                                            ->send();
                                    }
                                })
                        ),

                    TextInput::make('github_id')
                        ->label('GitHub ID')
                        ->placeholder('Click 🔍 to resolve from username')
                        ->required()
                        ->unique(ignoreRecord: true),
                ]),

            Section::make('Account status')
                ->columns(2)
                ->schema([
                    Toggle::make('is_active')
                        ->label('Active account')
                        ->helperText('Deactivating = permanent ban')
                        ->onColor('success')
                        ->offColor('danger'),

                    DateTimePicker::make('suspended_until')
                        ->label('Suspended until')
                        ->helperText('Leave empty for no suspension')
                        ->nullable(),

                    Select::make('roles')
                        ->label('Role')
                        ->relationship('roles', 'name')
                        ->preload()
                        ->searchable(),

                    DateTimePicker::make('last_login_at')
                        ->label('Last login')
                        ->disabled(),
                ]),
        ]);
    }
}
