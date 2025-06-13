<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


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

class QuotesRelationManager extends RelationManager
{
    protected static string $relationship = 'quotes';

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
                    ->label('Line quote')
                    ->schema([
                        TextInput::make('description')->required(),
                        TextInput::make('quantity')->numeric()->required(),
                        TextInput::make('unit_price')->numeric()->required(),
                    ])
                    ->columns(3)
                    ->collapsible()
                    ->defaultItems(1)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('quote_number')
            ->columns([
                Tables\Columns\TextColumn::make('quote_number'),
                Tables\Columns\TextColumn::make('statusQuote.name')
                    ->label('Status'),
                Tables\Columns\TextColumn::make('created_at'),
                Tables\Columns\TextColumn::make('expires_on'),
                Tables\Columns\TextColumn::make('total_cost_formatted')->label('Total (€)')
            ])
            ->filters([
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(fn ($record) => $record->status_id === 2),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
}
