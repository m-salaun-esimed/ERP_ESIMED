<?php
namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Invoice;

class AnnualSummary extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $year = now()->year;

        $invoices = Invoice::withSum('invoiceLines', 'line_total')->whereYear('issue_date', $year)->get();
        $caAnnuel = $invoices->where('invoice_status_id', 3)->sum('invoice_lines_sum_line_total');
        $enAttente = $invoices->where('invoice_status_id', '!=', 3)->sum('invoice_lines_sum_line_total');
        $nonEnvoyees = $invoices->where('invoice_status_id', 1)->sum('invoice_lines_sum_line_total');
        $objectifCA = 100000;
        $resteAFaire = $objectifCA - $caAnnuel;

        return [
            Card::make('CA annuel', number_format($caAnnuel, 2, ',', ' ') . ' €'),
            Card::make('Paiements en attente', number_format($enAttente, 2, ',', ' ') . ' €'),
            Card::make('Factures non envoyées', number_format($nonEnvoyees, 2, ',', ' ') . ' €'),
            Card::make('Objectif annuel', number_format($objectifCA, 2, ',', ' ') . ' €'),
            Card::make('CA restant à faire', number_format($resteAFaire, 2, ',', ' ') . ' €'),
        ];
    }
}
