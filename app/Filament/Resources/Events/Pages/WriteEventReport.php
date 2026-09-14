<?php

declare(strict_types=1);

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WriteEventReport extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected static ?string $title = 'Rapport post-événement';

    /** Évite l'auto-découverte dans le menu de navigation Filament */
    protected static bool $isDiscovered = false;

    // ── Schéma : uniquement les champs rapport ─────────────────────────────

    public function form(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Résumé')
                ->description('Accroche courte affichée sur la page publique.')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Textarea::make('recap_summary')
                        ->label('Résumé de l\'événement')
                        ->rows(3)
                        ->maxLength(500)
                        ->placeholder('Un résumé en 2-3 phrases de ce qui s\'est passé…')
                        ->columnSpanFull(),
                ]),

            Section::make('Contenu détaillé')
                ->description('Article complet visible sur la page de l\'événement.')
                ->icon('heroicon-o-pencil-square')
                ->schema([
                    RichEditor::make('recap_content')
                        ->label('')
                        ->toolbarButtons([
                            'bold', 'italic', 'underline', 'strike',
                            'h2', 'h3',
                            'bulletList', 'orderedList',
                            'link', 'blockquote',
                        ])
                        ->columnSpanFull(),
                ]),

            Section::make('Vidéos (YouTube / Vimeo)')
                ->description('Jusqu\'à 3 liens vidéo intégrés dans la page.')
                ->icon('heroicon-o-video-camera')
                ->columns(1)
                ->schema([
                    TextInput::make('recap_video_url_1')
                        ->label('Vidéo 1')
                        ->url()
                        ->placeholder('https://youtu.be/…'),

                    TextInput::make('recap_video_url_2')
                        ->label('Vidéo 2')
                        ->url()
                        ->placeholder('https://youtu.be/…'),

                    TextInput::make('recap_video_url_3')
                        ->label('Vidéo 3')
                        ->url()
                        ->placeholder('https://youtu.be/…'),
                ]),

            Section::make('Document (PDF / Slides)')
                ->description('Fichier téléchargeable lié à l\'événement.')
                ->icon('heroicon-o-paper-clip')
                ->schema([
                    TextInput::make('recap_document_name')
                        ->label('Nom du document')
                        ->placeholder('Slides — Laravel CI Meet #01')
                        ->maxLength(100),

                    FileUpload::make('recap_document_path')
                        ->label('Fichier')
                        ->disk('assets')
                        ->directory('documents/events')
                        ->acceptedFileTypes(['application/pdf', 'application/vnd.ms-powerpoint',
                            'application/vnd.openxmlformats-officedocument.presentationml.presentation'])
                        ->maxSize(20480)
                        ->helperText('PDF ou PowerPoint · max 20 Mo'),
                ]),

        ]);
    }

    // ── Actions d'en-tête ─────────────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Retour à l\'événement')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn (): string => EventResource::getUrl('view', ['record' => $this->record])),

            Action::make('save_draft')
                ->label('Enregistrer le brouillon')
                ->icon('heroicon-o-cloud-arrow-up')
                ->color('primary')
                ->action(fn () => $this->save()),

            Action::make('publish')
                ->label('Publier le rapport')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Publier le rapport post-événement ?')
                ->modalDescription('Le rapport sera immédiatement visible sur la page publique de l\'événement.')
                ->action(function (): void {
                    $this->save();

                    $this->record->update([
                        'recap_published_at' => now(),
                        'recap_published_by' => auth()->id(),
                    ]);

                    Notification::make()
                        ->title('Rapport publié — il est maintenant visible sur la page publique')
                        ->success()
                        ->send();
                }),
        ];
    }

    // ── Pas d'actions de formulaire en bas (on utilise les header actions) ──

    protected function getFormActions(): array
    {
        return [];
    }

    // ── Après sauvegarde → retour vers la page détail ─────────────────────

    protected function getRedirectUrl(): string
    {
        return EventResource::getUrl('view', ['record' => $this->record]);
    }

    // ── Titre dynamique ───────────────────────────────────────────────────

    public function getHeading(): string
    {
        return 'Rapport post-événement — ' . $this->record->title;
    }

    // ── N'enregistrer que les champs rapport ──────────────────────────────

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ne conserver que les champs rapport, ignorer tout le reste
        $reportFields = [
            'recap_summary', 'recap_content',
            'recap_video_url_1', 'recap_video_url_2', 'recap_video_url_3',
            'recap_document_name', 'recap_document_path',
        ];

        return array_intersect_key($data, array_flip($reportFields));
    }
}
