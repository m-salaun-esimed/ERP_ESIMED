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
use App\Models\InvoiceStatus;
use App\Models\Quote;
use Filament\Forms\Components\DatePicker;
use App\Filament\Resources\InvoiceResource\RelationManagers\InvoiceLinesRelationManager;
use App\Models\QuoteLine;
use App\Models\InvoiceLine;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Customer;
use App\Models\Project;
use Filament\Notifications\Notification;
use App\Filament\Resources\InvoiceResource\Pages\ViewInvoice;
use Carbon\Carbon;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Factures';
    protected static ?string $label = 'Factures';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('invoice_status_id')
                    ->label('Statut de la facture')
                    ->options(InvoiceStatus::all()->pluck('name', 'id'))
                    ->required()
                    ->reactive() // rend le champ "reactif" pour déclencher les effets dynamiques
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state == 3) {
                            Notification::make()
                                ->title('Attention')
                                ->body('Une fois la facture marquée comme payée, elle ne peut plus être modifiée ni supprimée.')
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
                    ->requiredIf('invoice_status_id', 3) // validation obligatoire si statut = 3
                    ->disabled(fn ($get) => $get('invoice_status_id') != 3) // grisé sauf si statut = 3

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withSum('invoiceLines', 'line_total'))
            ->columns([
                TextColumn::make('invoice_number')->label('Numéro de facture')->searchable(),
                TextColumn::make('quote.quote_number')->label('Numéro de devis')->searchable(),
                TextColumn::make('status.name')->label('Statut')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'brouillon' => 'gray',
                        'envoyée' => 'warning',
                        'payée' => 'success',
                    }),
                TextColumn::make('quote.project.name')->label('Projet')->searchable(),
                TextColumn::make('quote.project.customer.name')->label('Client')->searchable(),
                TextColumn::make('invoice_lines_sum_line_total')
                    ->label('Total des lignes (€)')
                    ->money('EUR', locale: 'fr_FR')
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Date d\'échéance')
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
                SelectFilter::make('customer_id')
                    ->label('Client')
                    ->options(
                        \App\Models\Customer::where('user_id', Auth::id())->pluck('name', 'id')
                    )
                    ->query(function (Builder $query, array $data): Builder {
                        if ($data['value']) {
                            return $query->whereHas('quote.project.customer', function (Builder $q) use ($data) {
                                $q->where('id', $data['value']);
                            });
                        }

                        return $query;
                    }),
                SelectFilter::make('project_id')
                    ->label('Projet')
                    ->options(
                        Project::whereHas('customer', function ($q) {
                            $q->where('user_id', Auth::id());
                        })->pluck('name', 'id')
                    )
                    ->query(function (Builder $query, array $data): Builder {
                        if ($data['value']) {
                            return $query->whereHas('quote.project', function ($q) use ($data) {
                                $q->where('id', $data['value']);
                            });
                        }

                        return $query;
                    }),
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
            ->actions([
                Tables\Actions\Action::make('Voir')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => ViewInvoice::getUrl(['record' => $record->getKey()])),
                Tables\Actions\EditAction::make()
                    ->visible(fn (Invoice $record) => $record->invoice_status_id != 3),
                Tables\Actions\Action::make('Relancer')
                    ->label('Relancer par mail')
                    ->icon('heroicon-o-envelope')
                    ->color('danger')
                    ->visible(fn (Invoice $record) =>
                        $record->due_date < now() && $record->invoice_status_id != 3 && $record->invoice_status_id != 1
                    )
                    ->action(function (Invoice $record) {
                        \Mail::to($record->quote->project->customer->email)->send(new \App\Mail\InvoiceReminderMail($record));

                        \Filament\Notifications\Notification::make()
                            ->title('Mail envoyé')
                            ->body('Une relance a été envoyée à ' . $record->quote->project->customer->email)
                            ->success()
                            ->send();
                    })
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            InvoiceLinesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
            'view' => Pages\ViewInvoice::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('quote.project.customer', function (Builder $query) {
                $query->where('user_id', Auth::id());
            });
    }

    public static function getNavigationSort(): int
    {
        return 5;
    }

}
