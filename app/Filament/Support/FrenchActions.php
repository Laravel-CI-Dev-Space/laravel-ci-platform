<?php

declare(strict_types=1);

namespace App\Filament\Support;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

final class FrenchActions
{
    public static function edit(): EditAction
    {
        return EditAction::make()->label('Modifier');
    }

    public static function delete(): DeleteAction
    {
        return DeleteAction::make()
            ->label('Supprimer')
            ->modalHeading('Supprimer cet enregistrement')
            ->modalDescription('Cette action est irréversible.')
            ->modalSubmitActionLabel('Supprimer')
            ->modalCancelActionLabel('Annuler');
    }

    public static function create(): CreateAction
    {
        return CreateAction::make()->label('Créer');
    }

    public static function view(): ViewAction
    {
        return ViewAction::make()->label('Voir');
    }

    /**
     * Ouvre la page ViewRecord de la ressource (pas une modale vide).
     *
     * @param  class-string  $resource
     */
    public static function viewPage(string $resource): Action
    {
        return Action::make('view')
            ->label('Détails')
            ->icon(Heroicon::OutlinedEye)
            ->color('gray')
            ->url(fn (Model $record): string => $resource::getUrl('view', ['record' => $record]));
    }

    public static function confirm(Action $action, string $heading, ?string $description = null): Action
    {
        return $action
            ->modalHeading($heading)
            ->when(
                $description !== null,
                fn (Action $configured) => $configured->modalDescription($description),
            )
            ->modalSubmitActionLabel('Confirmer')
            ->modalCancelActionLabel('Annuler');
    }
}
