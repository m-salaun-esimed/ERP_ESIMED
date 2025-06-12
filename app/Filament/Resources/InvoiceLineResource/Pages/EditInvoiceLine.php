<?php

namespace App\Filament\Resources\InvoiceLineResource\Pages;

use App\Filament\Resources\InvoiceLineResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoiceLine extends EditRecord
{
    protected static string $resource = InvoiceLineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
