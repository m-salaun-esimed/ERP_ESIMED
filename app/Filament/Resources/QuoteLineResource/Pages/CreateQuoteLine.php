<?php

namespace App\Filament\Resources\QuoteLineResource\Pages;

use App\Filament\Resources\QuoteLineResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateQuoteLine extends CreateRecord
{
    protected static string $resource = QuoteLineResource::class;
    
    protected function afterCreate(): void
    {
        $quote_line = $this->record;
        dd($quote_line);
        // if ($quote && $quote->quoteLines->count()) {
        //     foreach ($quote->quoteLines as $line) {
        //         InvoiceLine::create([
        //             'invoice_id' => $this->record->id,
        //             'description' => $line->description,
        //             'unit_price' => $line->unit_price,
        //             'quantity' => $line->quantity,
        //             'line_total' => $line->line_total,
        //             'line_order' => $line->line_order,
        //         ]);
        //     }
        // }
    }
}
