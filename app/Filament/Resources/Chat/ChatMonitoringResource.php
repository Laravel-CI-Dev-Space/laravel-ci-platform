<?php

declare(strict_types=1);

namespace App\Filament\Resources\Chat;

use App\Filament\Resources\Chat\ChatMonitoringResource\Pages;
use App\Models\Chat\ChatTokenBudget;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ChatMonitoringResource extends Resource
{
    protected static ?string $model = ChatTokenBudget::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;
    protected static ?int $navigationSort = 5;
    protected static ?string $modelLabel = 'Budget tokens';
    protected static ?string $pluralModelLabel = 'Monitoring tokens';

    public static function getNavigationGroup(): string { return 'Assistant IA'; }
    public static function getNavigationLabel(): string { return 'Monitoring tokens'; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                ChatTokenBudget::query()
                    ->with('user')
                    ->orderByDesc('date')
                    ->orderByDesc('id')
            )
            ->columns([
                TextColumn::make('date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Utilisateur')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('public_tokens_used')
                    ->label('Tokens publics')
                    ->numeric(thousandsSeparator: ' ')
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('dashboard_tokens_used')
                    ->label('Tokens dashboard')
                    ->numeric(thousandsSeparator: ' ')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('total')
                    ->label('Total')
                    ->state(fn ($record) => $record->totalUsed())
                    ->numeric(thousandsSeparator: ' ')
                    ->sortable(query: fn (Builder $q, string $dir) =>
                        $q->orderByRaw("(public_tokens_used + dashboard_tokens_used) {$dir}")
                    )
                    ->weight('bold'),

                TextColumn::make('daily_limit')
                    ->label('Limite / jour')
                    ->numeric(thousandsSeparator: ' '),

                TextColumn::make('percent')
                    ->label('Usage %')
                    ->state(fn ($record) => $record->usagePercent() . '%')
                    ->badge()
                    ->color(fn ($record) => match (true) {
                        $record->usagePercent() > 80 => 'danger',
                        $record->usagePercent() > 50 => 'warning',
                        default                      => 'success',
                    }),
            ])
            ->filters([
                Filter::make('today')
                    ->label("Aujourd'hui")
                    ->query(fn (Builder $q) => $q->whereDate('date', today()))
                    ->default(),

                Filter::make('this_week')
                    ->label('Cette semaine')
                    ->query(fn (Builder $q) => $q->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])),

                Filter::make('this_month')
                    ->label('Ce mois')
                    ->query(fn (Builder $q) => $q->whereMonth('date', now()->month)->whereYear('date', now()->year)),

                Filter::make('last_30_days')
                    ->label('30 derniers jours')
                    ->query(fn (Builder $q) => $q->where('date', '>=', now()->subDays(30)->startOfDay())),

                SelectFilter::make('user_id')
                    ->label('Utilisateur')
                    ->options(fn () => User::orderBy('name')->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->placeholder('Tous les utilisateurs'),

                Filter::make('high_usage')
                    ->label('Usage > 80%')
                    ->query(fn (Builder $q) => $q->whereRaw('(public_tokens_used + dashboard_tokens_used) / daily_limit > 0.8')
                        ->where('daily_limit', '>', 0)),

                Filter::make('has_public_tokens')
                    ->label('Avec tokens publics')
                    ->query(fn (Builder $q) => $q->where('public_tokens_used', '>', 0)),

                Filter::make('has_dashboard_tokens')
                    ->label('Avec tokens dashboard')
                    ->query(fn (Builder $q) => $q->where('dashboard_tokens_used', '>', 0)),
            ])
            ->filtersLayout(\Filament\Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->actions([
                Action::make('set_limit')
                    ->label('Modifier limite')
                    ->icon(Heroicon::OutlinedPencil)
                    ->form([
                        TextInput::make('daily_limit')
                            ->label('Limite journalière (tokens)')
                            ->numeric()
                            ->required()
                            ->minValue(1000),
                    ])
                    ->action(fn ($record, array $data) => $record->update(['daily_limit' => $data['daily_limit']])),
            ])
            ->defaultSort('date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChatMonitoring::route('/'),
        ];
    }
}
