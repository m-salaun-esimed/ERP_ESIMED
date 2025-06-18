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
                    ->label('Invoice number'),
                Tables\Columns\TextColumn::make('invoice.payment_type')
                    ->label('Payment Type'),
                Tables\Columns\TextColumn::make('invoice.issue_date')
                    ->label('Issue date'),  
                Tables\Columns\TextColumn::make('invoice.due_date')
                    ->label('Due date'),
                Tables\Columns\TextColumn::make('invoice.payment_date')
                    ->label('Payement date'),                
                Tables\Columns\TextColumn::make('description')
                    ->label('Description'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantity'),
                Tables\Columns\TextColumn::make('unit_price')
                    ->money('EUR', locale: 'fr_FR')
                    ->label('Unite price'),
                Tables\Columns\TextColumn::make('line_total')
                    ->label('Line Total (€)')
                    ->money('EUR', locale: 'fr_FR')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->icon('heroicon-o-arrow-up-tray'),
            ])
            ->actions([
            ]);
    }

    // public function getTableHeading(): string
    // {
    //     $invoice = $this->ownerRecord; // accès à l'enregistrement parent (Invoice)
    //     return "Facture : {$invoice->invoice_number} - {$invoice->payment_type}";
    // }

}
