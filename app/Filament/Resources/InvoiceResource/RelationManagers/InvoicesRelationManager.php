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
use App\Models\Invoice;
use Filament\Forms\Components\DatePicker;
use App\Models\InvoiceStatus;
use App\Models\Quote;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\CreateAction;
use App\Models\InvoiceLine;
use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\Pages\ViewInvoice;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Project;
use Carbon\Carbon;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('invoice_status_id')
                    ->label('Statut de la facture')
                    ->options(InvoiceStatus::all()->pluck('name', 'id'))
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state == 3) {
                            Notification::make()
                                ->title('Attention')
                                ->body('Une fois la facture marquée comme payée, elle ne peut plus être modifiée ou supprimée.')
                                ->warning()
                                ->persistent()
                                ->send();
                        }
                    }),
                Select::make('quote_id')
                    ->label('Numéro de devis')
                    ->options(function () {
                        return Quote::where('status_id', 2)
                            ->whereHas('quoteLines')
                            ->whereHas('project.customer', function ($query) {
                                $query->where('user_id', Auth::id());
                            })
                            ->pluck('quote_number', 'id');
                    })
                    ->required(),
                Select::make('payment_type')
                    ->label('Type de paiement')
                    ->options([
                        'chèque' => 'Chèque',
                        'virement' => 'Virement',
                        'paypal' => 'PayPal',
                        'autre' => 'Autre',
                    ])
                    ->required(),
                DatePicker::make('issue_date')
                    ->label('Date d\'émission')
                    ->required(),

                DatePicker::make('due_date')
                    ->label('Date d\'échéance')
                    ->required(),

                DatePicker::make('payment_date')
                    ->label('Date de paiement')
                    ->requiredIf('invoice_status_id', 3)
                    ->visible(fn (Forms\Get $get) => $get('invoice_status_id') == 3)
                ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Factures')
            ->columns([
                TextColumn::make('invoice_number')->label('Numéro de facture'),
                TextColumn::make('status.name')->label('Statut')->searchable(),
                TextColumn::make('total_cost')
                    ->label('Total lignes facture (€)')
                    ->money('EUR', locale: 'fr_FR'),
                TextColumn::make('due_date')
                    ->label('Date maximale')
                    ->sortable()
                    ->color(function (Invoice $record) {
                        $isOverdue = $record->due_date < now() && $record->invoice_status_id != 3;
                        return $isOverdue ? 'danger' : null;
                    })
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('d/m/Y')),
                TextColumn::make('due_date_status')
                    ->label('En retard ?')
                    ->getStateUsing(function (Invoice $record) {
                        $isOverdue = $record->due_date < now() && $record->invoice_status_id != 3;
                        return $isOverdue ? '⚠️ Oui' : 'Non';
                    })
                    ->color(function (Invoice $record) {
                        $isOverdue = $record->due_date < now() && $record->invoice_status_id != 3;
                        return $isOverdue ? 'danger' : 'success';
                    }),
            ])
            ->filters([
                SelectFilter::make('retard')
                    ->label('Factures en retard')
                    ->options([
                        'yes' => 'Oui',
                        'no' => 'Non',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (($data['value'] ?? null) === 'yes') {
                            return $query->where('due_date', '<', Carbon::now())
                                        ->where('invoice_status_id', '!=', 3);
                        } elseif (($data['value'] ?? null) === 'no') {
                            return $query->where(function ($q) {
                                $q->where('due_date', '>=', Carbon::now())
                                ->orWhere('invoice_status_id', 3);
                            });
                        }
                        return $query;
                    }),
            ])
            ->headerActions([
                CreateAction::make()
                    ->after(function (Invoice $record) {
                        $quote = $record->quote;

                        if ($quote && $quote->quoteLines->count()) {
                            foreach ($quote->quoteLines as $line) {
                                InvoiceLine::create([
                                    'invoice_id' => $record->id,
                                    'description' => $line->description,
                                    'unit_price' => $line->unit_price,
                                    'quantity' => $line->quantity,
                                    'line_total' => $line->line_total,
                                    'line_order' => $line->line_order,
                                ]);
                            }
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('Voir')
                            ->icon('heroicon-o-eye')
                            ->url(fn ($record) => ViewInvoice::getUrl(['record' => $record->getKey()])),
                Tables\Actions\EditAction::make()
                    ->visible(fn (Invoice $record) => $record->invoice_status_id != 3),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Invoice $record) => $record->invoice_status_id != 3),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Supprimer'),
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

    public static function getPages(): array
    {
        return [
            'view' => Pages\ViewInvoice::route('/{record}/view')
        ];
    }
}
