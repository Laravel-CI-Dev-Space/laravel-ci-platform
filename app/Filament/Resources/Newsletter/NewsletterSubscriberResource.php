<?php

declare(strict_types=1);

namespace App\Filament\Resources\Newsletter;

use App\Filament\Resources\Concerns\AuthorizesViaPermission;
use App\Filament\Resources\Newsletter\Pages\CreateNewsletterSubscriber;
use App\Filament\Resources\Newsletter\Pages\EditNewsletterSubscriber;
use App\Filament\Resources\Newsletter\Pages\ListNewsletterSubscribers;
use App\Models\NewsletterSubscriber;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NewsletterSubscriberResource extends Resource
{
    use AuthorizesViaPermission;

    protected static ?string $model = NewsletterSubscriber::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $navigationLabel = 'Newsletter';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return 'Blog';
    }

    protected static ?string $modelLabel = 'Abonné';

    protected static ?string $pluralModelLabel = 'Abonnés newsletter';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Prénom')
                ->maxLength(100),

            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Prénom')
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),

                IconColumn::make('unsubscribed_at')
                    ->label('Actif')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedXCircle)
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->getStateUsing(fn (NewsletterSubscriber $record): bool => $record->isActive()),

                TextColumn::make('created_at')
                    ->label('Inscrit le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('unsubscribed_at')
                    ->label('Désabonné le')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('active')
                    ->label('Actifs uniquement')
                    ->query(fn (Builder $query) => $query->whereNull('unsubscribed_at'))
                    ->default(),

                Filter::make('unsubscribed')
                    ->label('Désabonnés')
                    ->query(fn (Builder $query) => $query->whereNotNull('unsubscribed_at')),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListNewsletterSubscribers::route('/'),
            'create' => CreateNewsletterSubscriber::route('/create'),
            'edit'   => EditNewsletterSubscriber::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) NewsletterSubscriber::active()->count();
    }
}
