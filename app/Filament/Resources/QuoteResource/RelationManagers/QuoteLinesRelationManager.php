<?php

namespace App\Filament\Resources\QuoteResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;

class QuoteLinesRelationManager extends RelationManager
{
    protected static string $relationship = 'quoteLines';
    protected static ?string $title = 'Lignes du devis';
    protected static ?string $modelLabel       = 'Devis';
    protected static ?string $pluralModelLabel = 'Devis';
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('description')->required()->label('Description'),
                TextInput::make('quantity')->numeric()->required()->label('Quantité'),
                TextInput::make('unit_price')->numeric()->required()->label('Prix unitaire'),
            ]);
    }
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                Tables\Columns\TextColumn::make('description')->label('Description'),
                Tables\Columns\TextColumn::make('quantity')->label('Quantité'),
                Tables\Columns\TextColumn::make('unit_price')->label('Prix unitaire')->money('EUR', locale: 'fr_FR'),
                Tables\Columns\TextColumn::make('line_total')
                    ->money('EUR', locale: 'fr_FR')
                    ->label('Total ligne'),
            ])
            ->filters([
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Créer'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Modifier'),
                Tables\Actions\DeleteAction::make()->label('Supprimer'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Supprimer en masse'),
                ]),
            ]);
    }
}
