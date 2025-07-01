<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\InvoiceLine;
use Filament\Notifications\Notification;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        return $data;
    }

    protected function afterCreate(): void
    {
      $quote = $this->record->quote;

        if ($quote && $quote->quoteLines->count()) {
            foreach ($quote->quoteLines as $line) {
                InvoiceLine::create([
                    'invoice_id' => $this->record->id,
                    'description' => $line->description,
                    'unit_price' => $line->unit_price,
                    'quantity' => $line->quantity,
                    'line_total' => $line->line_total,
                    'line_order' => $line->line_order,
                ]);
            }
        }
    }
    
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Facture créée')
            ->body('La nouvelle facture a bien été enregistrée.')
            ->success()
            ->icon('heroicon-o-document-plus');
    }

}
