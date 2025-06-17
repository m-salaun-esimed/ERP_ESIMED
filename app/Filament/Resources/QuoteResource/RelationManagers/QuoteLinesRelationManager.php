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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('description')->required(),
                TextInput::make('quantity')->numeric()->required(),
                TextInput::make('unit_price')->numeric()->required(),
            ]);
    }
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('unit_price'),
                Tables\Columns\TextColumn::make('line_total')
                    ->money('EUR', locale: 'fr_FR'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
