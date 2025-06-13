<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteLineResource\Pages;
use App\Filament\Resources\QuoteLineResource\RelationManagers;
use App\Models\QuoteLine;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;

class QuoteLineResource extends Resource
{
    protected static ?string $model = QuoteLine::class;
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description'),
                TextColumn::make('quantity'),
                TextColumn::make('unit_price'),
                TextColumn::make('quote.project.name')->label('Project'),
                TextColumn::make('quote.quote_number')->label('Quote number'),
                TextColumn::make('quote.statusQuote.name')->label('Status')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuoteLines::route('/'),
            'create' => Pages\CreateQuoteLine::route('/create'),
            'edit' => Pages\EditQuoteLine::route('/{record}/edit'),
        ];
    }

}
