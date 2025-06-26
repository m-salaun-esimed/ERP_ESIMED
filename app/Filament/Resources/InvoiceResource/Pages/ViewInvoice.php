<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Pages\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('Exporter en pdf')
                ->icon('heroicon-o-arrow-down-tray')
                ->action('exportPdf')
                ->color('primary')
        ];
    }

    public function exportPdf()
    {
        $invoice = $this->record->load('invoiceLines', 'quote.project');

        $pdf = Pdf::loadView('pdf.facture', [
            'facture' => $invoice,
        ]);

        return response()->streamDownload(
            fn () => print($pdf->output()),
            "Facture-{$invoice->invoice_number}.pdf"
        );
    }
}