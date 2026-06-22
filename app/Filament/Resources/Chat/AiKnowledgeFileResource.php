<?php

declare(strict_types=1);

namespace App\Filament\Resources\Chat;

use App\Filament\Resources\Chat\AiKnowledgeFileResource\Pages;
use App\Models\Chat\AiKnowledgeFile;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AiKnowledgeFileResource extends Resource
{
    protected static ?string $model = AiKnowledgeFile::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;
    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel = 'Fichier de connaissance';
    protected static ?string $pluralModelLabel = 'Fichiers de connaissance';

    public static function getNavigationGroup(): string { return 'Assistant IA'; }
    public static function getNavigationLabel(): string { return 'Fichiers de connaissance'; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('type')
                ->label('Type')
                ->required()
                ->options([
                    AiKnowledgeFile::TYPE_BEHAVIOR => 'Comportement (style, limites, ton)',
                    AiKnowledgeFile::TYPE_PLATFORM => 'Plateforme Laravel CI',
                    AiKnowledgeFile::TYPE_LARAVEL  => 'Connaissance Laravel / PHP',
                ])
                ->helperText('Détermine quand ce fichier est injecté dans le system prompt.'),

            TextInput::make('label')
                ->label('Nom affiché')
                ->required()
                ->placeholder('Guide comportement v1'),

            FileUpload::make('disk_path')
                ->label('Fichier (Markdown ou texte)')
                ->disk('local')
                ->directory('ai-knowledge')
                ->acceptedFileTypes(['text/plain', 'text/markdown', 'text/x-markdown'])
                ->maxSize(512)
                ->helperText('Fichier .md ou .txt, max 512 Ko.')
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $filename = basename($state);
                        $content  = \Storage::disk('local')->get('ai-knowledge/' . $filename);
                        $set('filename', $filename);
                        $set('content', $content ?? '');
                    }
                })
                ->live(),

            TextInput::make('filename')
                ->label('Nom du fichier')
                ->disabled()
                ->dehydrated(),

            Textarea::make('content')
                ->label('Contenu (éditable directement)')
                ->rows(12)
                ->helperText('Vous pouvez éditer le contenu directement ici sans uploader un fichier.')
                ->required(),

            Toggle::make('is_active')
                ->label('Actif (injecté dans le system prompt)')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'behavior' => 'warning',
                        'platform' => 'info',
                        'laravel'  => 'success',
                        default    => 'gray',
                    }),
                TextColumn::make('label')->label('Nom')->searchable()->sortable(),
                TextColumn::make('filename')->label('Fichier')->color('gray')->size('sm'),
                TextColumn::make('content')->label('Aperçu')->limit(60)->color('gray'),
                IconColumn::make('is_active')->label('Actif')->boolean(),
                TextColumn::make('uploader.name')->label('Uploadé par')->sortable(),
                TextColumn::make('updated_at')->label('Mis à jour')->since()->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'behavior' => 'Comportement',
                        'platform' => 'Plateforme',
                        'laravel'  => 'Laravel / PHP',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAiKnowledgeFiles::route('/'),
            'create' => Pages\CreateAiKnowledgeFile::route('/create'),
            'edit'   => Pages\EditAiKnowledgeFile::route('/{record}/edit'),
        ];
    }
}
