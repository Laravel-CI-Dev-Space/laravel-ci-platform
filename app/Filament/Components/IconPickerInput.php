<?php

declare(strict_types=1);

namespace App\Filament\Components;

use App\Services\FontAwesomeService;
use Filament\Forms\Components\Select;

/**
 * Factory that returns a pre-configured Filament Select for picking
 * a Font Awesome icon. Uses the bundled catalog by default, or the
 * full catalog downloaded via `php artisan icons:download`.
 *
 * Usage:
 *   IconPickerInput::make('icon')->label('Icône')
 */
class IconPickerInput
{
    public static function make(string $name): Select
    {
        /** @var FontAwesomeService $service */
        $service = app(FontAwesomeService::class);

        return Select::make($name)
            ->searchable()
            ->placeholder('Rechercher une icône...')
            ->noSearchResultsMessage('Aucune icône trouvée.')
            ->searchPrompt('Tapez un nom (ex: users, rocket, github)…')
            ->live()
            ->getSearchResultsUsing(function (string $search) use ($service): array {
                return $service->forSelect($search);
            })
            ->getOptionLabelUsing(function (mixed $value) use ($service): string {
                if (! is_string($value) || $value === '') {
                    return '';
                }

                return $service->label($value);
            })
            ->helperText(function (mixed $state) use ($service): string|\Illuminate\Contracts\Support\Htmlable {
                if (! is_string($state) || $state === '') {
                    return 'Tapez un mot-clé pour rechercher une icône Font Awesome 6 Free.';
                }

                return $service->preview($state);
            });
    }
}
