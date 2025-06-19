<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteResource\Pages;
use App\Filament\Resources\QuoteResource\RelationManagers;
use App\Models\Quote;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\QuoteStatus;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\QuoteResource\RelationManagers\QuoteLinesRelationManager;
use App\Filament\Resources\QuoteResource\Pages\ViewQuote;
use Carbon\Carbon;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Devis';
    protected static ?string $label = 'Devis';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('status_id')
                    ->label('Statut du devis')
                    ->options(QuoteStatus::all()->pluck('name', 'id'))
                    ->required(),

                Select::make('project_id')
                    ->label('Projet')
                    ->options(function () {
                        $user = Auth::user();

                        return Project::whereHas('customer', function ($query) use ($user) {
                            $query->where('user_id', $user->id);
                        })->pluck('name', 'id');
                    })
                    ->required(),

                DatePicker::make('created_at')
                    ->default(now())
                    ->disabled()
                    ->dehydrated(true),

                DatePicker::make('expires_on')
                    ->default(now()->addDays(30))
                    ->required()
                    ->closeOnDateSelection(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('quote_number')
                    ->label('Numéro du devis'),
                TextColumn::make('project.name')
                    ->label('Projet'),
                TextColumn::make('statusQuote.name')
                    ->label('Statut'),
                TextColumn::make('created_at')
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('d/m/Y'))
                    ->label('Date de création'),
                TextColumn::make('expires_on')
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('d/m/Y'))
                    ->label('Date d\'expiration'),
                TextColumn::make('total_cost')
                    ->label('Total')
                    ->money('EUR', locale: 'fr_FR'),
            ])
            ->filters([
                SelectFilter::make('project_id')
                    ->label('Projet')
                    ->options(function () {
                        $user = Auth::user();
                        return Project::whereHas('customer', function ($query) use ($user) {
                            $query->where('user_id', $user->id);
                        })->pluck('name', 'id');
                    }),

                SelectFilter::make('status_id')
                    ->label('Statut')
                    ->options(QuoteStatus::all()->pluck('name', 'id')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (Quote $record) => $record->status_id != 2),
                Tables\Actions\Action::make('Voir')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => ViewQuote::getUrl(['record' => $record->getKey()])),
            ])
            ->bulkActions([
                // Pas d'actions groupées pour l'instant
            ]);
    }

    public static function getRelations(): array
    {
        return [
            QuoteLinesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'edit' => Pages\EditQuote::route('/{record}/edit'),
            'view' => Pages\ViewQuote::route('/{record}/view'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('project.customer', function (Builder $query) {
                $query->where('user_id', Auth::id());
            });
    }

    public static function getNavigationSort(): int
    {
        return 3;
    }
}
