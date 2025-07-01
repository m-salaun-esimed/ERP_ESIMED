<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use Filament\Forms\Validation\ValidationException;

use App\Filament\Resources\QuoteResource\Pages;
use App\Filament\Resources\QuoteResource\RelationManagers;
use App\Models\Quote;
use Filament\Resources\Resource;
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

class QuotesRelationManager extends RelationManager
{
    protected static string $relationship = 'quotes';
    protected static ?string $title = 'Devis';
    protected static ?string $modelLabel       = 'Devis';
    protected static ?string $pluralModelLabel = 'Devis';
    public static function getLabel(): string
    {
        return 'Devis';
    }

    public function form(Form $form): Form
    {
        return $form
           ->schema([
                Select::make('status_id')
                    ->label('Statut du devis')
                    ->options(QuoteStatus::all()->pluck('name', 'id'))
                    ->required(),

                DatePicker::make('created_at')
                    ->label('Date de création')
                    ->default(now())
                    ->disabled()
                    ->dehydrated(true),
                DatePicker::make('expires_on')
                    ->label('Expire le')
                    ->default(now()->addDays(30))
                    ->required()
                    ->closeOnDateSelection(),
                Repeater::make('quoteLines')
                    ->relationship()
                    ->label('Lignes du devis')
                    ->schema([
                        TextInput::make('description')->required()->label('Description'),
                        TextInput::make('quantity')->numeric()->required()->label('Quantité'),
                        TextInput::make('unit_price')->numeric()->required()->label('Prix unitaire'),
                    ])
                    ->columns(3)
                    ->collapsible()
                    ->defaultItems(1)
                    ->visible(fn (?Quote $record) => $record === null || $record->status_id != 2),
                    ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('quote_number')
            ->columns([
                Tables\Columns\TextColumn::make('quote_number')
                    ->label('Numéro du devis'),
                Tables\Columns\TextColumn::make('statusQuote.name')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'envoyé' => 'gray',
                        'en attente' => 'warning',
                        'accepté' => 'success',
                        'refusé' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date de création')
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('d/m/Y')),
                Tables\Columns\TextColumn::make('expires_on')
                    ->label('Expire le')
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('d/m/Y')),
                Tables\Columns\TextColumn::make('total_cost')
                    ->label('Total des lignes (€)')
                    ->money('EUR', locale: 'fr_FR'),
            ])
            ->filters([
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Créer'),
            ])
            ->actions([
                Tables\Actions\Action::make('Voir')
                    ->label('Voir')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => ViewQuote::getUrl(['record' => $record->getKey()])),
                Tables\Actions\EditAction::make()
                    ->label('Modifier')
                    ->visible(fn (Quote $record) => $record->status_id != 2),
                Tables\Actions\DeleteAction::make()
                    ->label('Supprimer')
                    ->visible(fn (Quote $record) => $record->invoice === null),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Supprimer en masse'),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('project.customer', function (Builder $query) {
                $query->where('user_id', Auth::id());
            });
    }

    public static function getPages(): array
    {
        return [
            'view' => Pages\ViewQuote::route('/{record}/view')
        ];
    }
}
