<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('Invoices')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Invoices')
            ->columns([
                TextColumn::make('invoice_number'),
                TextColumn::make('status.name')->label('Status')->searchable(),
                TextColumn::make('quote.project.customer.name')->label('Customer')->searchable(),
                TextColumn::make('total_cost_formatted')->label('Total (€)')
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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if ($this->record->invoice_status_id === 3) {
            Notification::make()
                ->title('Cette facture est déjà payée')
                ->body('Vous ne pouvez plus la modifier.')
                ->danger()
                ->send();

            $this->redirect(InvoiceResource::getUrl('view', ['record' => $this->record]));
        }

        return $data;
    }
}
