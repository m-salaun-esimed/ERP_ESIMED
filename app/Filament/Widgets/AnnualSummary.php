<?php
namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

class AnnualSummary extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $year = now()->year;

        $invoices = Invoice::withSum('invoiceLines', 'line_total')
            ->whereYear('issue_date', $year)
            ->get();

        $caAnnuel = $invoices->where('invoice_status_id', 3)->sum('invoice_lines_sum_line_total');
        $enAttente = $invoices->where('invoice_status_id', '!=', 3)->sum('invoice_lines_sum_line_total');
        $nonEnvoyees = $invoices->where('invoice_status_id', 1)->sum('invoice_lines_sum_line_total');

        $objectifCA = Auth::user()->max_annual_revenue ?? 0;

        $resteAFaire = $objectifCA - $caAnnuel;

        return [
            Card::make('Annual Revenue', number_format($caAnnuel, 2, ',', ' ') . ' €'),
            Card::make('Pending Payments', number_format($enAttente, 2, ',', ' ') . ' €'),
            Card::make('Unsent Invoices', number_format($nonEnvoyees, 2, ',', ' ') . ' €'),
            Card::make('Annual Target', number_format($objectifCA, 2, ',', ' ') . ' €'),
            Card::make('Remaining Revenue', number_format($resteAFaire, 2, ',', ' ') . ' €'),
        ];
    }
}
