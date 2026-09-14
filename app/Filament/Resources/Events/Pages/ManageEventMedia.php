<?php

declare(strict_types=1);

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use App\Models\EventMedia;
use App\Services\Events\EventMediaService;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ManageEventMedia extends ManageRelatedRecords
{
    protected static string $resource = EventResource::class;

    protected static string $relationship = 'media';

    protected static ?string $title = 'Médias du récapitulatif';

    /** Évite l'auto-découverte dans le menu de navigation Filament */
    protected static bool $isDiscovered = false;

    // ── Schéma de formulaire vide (pas de création standard, upload custom) ──

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    // ── Table des médias existants ─────────────────────────────────────────

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail_path')
                    ->label('Aperçu')
                    ->disk('r2')
                    ->height(60)
                    ->width(80)
                    ->defaultImageUrl(fn (EventMedia $m): string => $m->isVideo()
                        ? 'https://placehold.co/80x60/1e293b/94a3b8?text=MP4'
                        : '')
                    ->extraImgAttributes(['class' => 'rounded object-cover']),

                TextColumn::make('original_name')
                    ->label('Fichier')
                    ->limit(40)
                    ->searchable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->color(fn ($state) => match ($state->value) {
                        'photo' => 'success',
                        'video' => 'info',
                    }),

                TextColumn::make('file_size')
                    ->label('Taille')
                    ->formatStateUsing(fn (EventMedia $record): string => $record->humanSize()),

                TextColumn::make('caption')
                    ->label('Légende')
                    ->placeholder('—')
                    ->limit(30),

                TextColumn::make('order')
                    ->label('Ordre')
                    ->sortable(),
            ])
            ->reorderable('order')
            ->defaultSort('order')
            ->actions([
                TableAction::make('edit_caption')
                    ->label('Légende')
                    ->icon('heroicon-o-pencil')
                    ->color('gray')
                    ->form([
                        TextInput::make('caption')
                            ->label('Légende')
                            ->maxLength(255),
                    ])
                    ->fillForm(fn (EventMedia $record): array => ['caption' => $record->caption])
                    ->action(function (EventMedia $record, array $data): void {
                        $record->update(['caption' => $data['caption']]);
                        Notification::make()->title('Légende mise à jour')->success()->send();
                    }),

                TableAction::make('open')
                    ->label('Ouvrir')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (EventMedia $record): string => $record->url())
                    ->openUrlInNewTab(),

                TableAction::make('delete')
                    ->label('Supprimer')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (EventMedia $record): void {
                        app(EventMediaService::class)->delete($record);
                        Notification::make()->title('Média supprimé')->success()->send();
                    }),
            ])
            ->bulkActions([
                BulkAction::make('delete_selected')
                    ->label('Supprimer la sélection')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Collection $records): void {
                        $service = app(EventMediaService::class);
                        $records->each(fn (EventMedia $m) => $service->delete($m));
                        Notification::make()
                            ->title("{$records->count()} média(s) supprimé(s)")
                            ->success()->send();
                    }),
            ])
            ->headerActions([])        // pas d'actions "Create" standard — upload custom ci-dessous
            ->emptyStateHeading('Aucun média')
            ->emptyStateDescription('Uploadez des photos ou vidéos MP4 via les boutons ci-dessus.');
    }

    // ── Actions d'en-tête : upload photos / vidéos ─────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            Action::make('upload_photos')
                ->label('Ajouter des photos')
                ->icon('heroicon-o-photo')
                ->color('success')
                ->modalHeading('Uploader des photos')
                ->modalDescription('JPEG, PNG, WebP, GIF. Un thumbnail est généré automatiquement pour la grille.')
                ->form([
                    FileUpload::make('files')
                        ->label('Photos')
                        ->image()
                        ->multiple()
                        ->imagePreviewHeight('120')
                        ->panelLayout('grid')
                        ->disk('local')
                        ->directory('tmp/uploads')
                        ->maxSize(10240)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $service = app(EventMediaService::class);
                    $errors  = 0;

                    foreach ($data['files'] as $tmpPath) {
                        $file = new \Illuminate\Http\UploadedFile(
                            storage_path('app/private/tmp/uploads/' . $tmpPath),
                            basename($tmpPath),
                            null, null, true
                        );

                        try {
                            $service->uploadPhoto($this->record, $file);
                        } catch (\Exception $e) {
                            $errors++;
                        } finally {
                            if (file_exists($file->getRealPath())) {
                                @unlink($file->getRealPath());
                            }
                        }
                    }

                    $ok = count($data['files']) - $errors;

                    Notification::make()
                        ->title("{$ok} photo(s) uploadée(s) avec succès" . ($errors ? " — {$errors} erreur(s)" : ''))
                        ->color($errors ? 'warning' : 'success')
                        ->send();
                }),

            Action::make('upload_video')
                ->label('Ajouter une vidéo MP4')
                ->icon('heroicon-o-video-camera')
                ->color('info')
                ->modalHeading('Uploader une vidéo')
                ->modalDescription('Format MP4 uniquement. Taille maximale : 500 Mo.')
                ->form([
                    FileUpload::make('file')
                        ->label('Vidéo MP4')
                        ->disk('local')
                        ->directory('tmp/uploads')
                        ->acceptedFileTypes(['video/mp4'])
                        ->maxSize(512_000)
                        ->required(),

                    TextInput::make('caption')
                        ->label('Légende (optionnel)')
                        ->maxLength(255),
                ])
                ->action(function (array $data): void {
                    $tmpPath = $data['file'];
                    $file    = new \Illuminate\Http\UploadedFile(
                        storage_path('app/private/tmp/uploads/' . $tmpPath),
                        basename($tmpPath),
                        'video/mp4', null, true
                    );

                    try {
                        app(EventMediaService::class)->uploadVideo(
                            $this->record,
                            $file,
                            $data['caption'] ?? null
                        );

                        Notification::make()->title('Vidéo uploadée avec succès')->success()->send();
                    } catch (\Exception $e) {
                        Notification::make()->title('Erreur')->body($e->getMessage())->danger()->send();
                    } finally {
                        if (file_exists($file->getRealPath())) {
                            @unlink($file->getRealPath());
                        }
                    }
                }),

            Action::make('back')
                ->label('Retour à l\'événement')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn (): string => EventResource::getUrl('view', ['record' => $this->record])),
        ];
    }

    // ── En-tête avec statistiques ──────────────────────────────────────────

    public function getHeading(): string
    {
        $stats = app(EventMediaService::class)->stats($this->record);

        return "Médias — {$this->record->title} ({$stats['photos']} photos · {$stats['videos']} vidéos)";
    }
}
