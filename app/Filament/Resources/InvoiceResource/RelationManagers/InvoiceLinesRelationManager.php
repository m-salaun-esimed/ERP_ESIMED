<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;

class InvoiceLinesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoiceLines';
    protected static ?string $title = 'Lignes de la facture';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('invoice.invoice_number')
                    ->label('Numéro de facture'),
                Tables\Columns\TextColumn::make('invoice.payment_type')
                    ->label('Type de paiement'),
                Tables\Columns\TextColumn::make('invoice.issue_date')
                    ->label('Date d\'émission'),
                Tables\Columns\TextColumn::make('invoice.due_date')
                    ->label('Date d\'échéance'),
                Tables\Columns\TextColumn::make('invoice.payment_date')
                    ->label('Date de paiement'),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantité'),
                Tables\Columns\TextColumn::make('unit_price')
                    ->money('EUR', locale: 'fr_FR')
                    ->label('Prix unitaire'),
                Tables\Columns\TextColumn::make('line_total')
                    ->label('Total ligne (€)')
                    ->money('EUR', locale: 'fr_FR')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
            ]);
    }
}
