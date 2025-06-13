<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceLineResource\Pages;
use App\Filament\Resources\InvoiceLineResource\RelationManagers;
use App\Models\InvoiceLine;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceLineResource extends Resource
{
    protected static ?string $model = InvoiceLine::class;
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

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
                //
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
            'index' => Pages\ListInvoiceLines::route('/'),
            'create' => Pages\CreateInvoiceLine::route('/create'),
            'edit' => Pages\EditInvoiceLine::route('/{record}/edit'),
        ];
    }

    public static function getNavigationSort(): int
    {
        return 6;
    }

}
