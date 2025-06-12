<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Repeater;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'éditée' => 'éditée',
                        'envoyée' => 'envoyée',
                        'payée' => 'payée',
                    ])
                    ->required()
                    ->searchable(false)
                    ->reactive(), 
                Select::make('project_id')
                    ->label('Project')
                    ->options(function () {
                        $user = Auth::user();

                        if (!$user) {
                            return [];
                        }

                        return $user->projects()->pluck('name', 'id')->toArray();
                    })
                    ->searchable()
                    ->required(),
                Repeater::make('invoiceLines')
                    ->label('Lignes de facture')
                    ->relationship()
                    ->schema([
                        TextInput::make('description')->required(),
                        TextInput::make('quantity')->numeric()->required(),
                        TextInput::make('unit_price')->numeric()->required(),
                    ])
                    ->defaultItems(1)
                    ->createItemButtonLabel('Ajouter une ligne')
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')->searchable(),
                TextColumn::make('status')->searchable(),
                TextColumn::make('project.name')->label('Project')->searchable(),
                TextColumn::make('project.customer.name')->label('Customer')->searchable(),
                TextColumn::make('total_cost_formatted')->label('Total (€)')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }
    
    public static function getNavigationSort(): int
    {
        return 5;
    }

}
