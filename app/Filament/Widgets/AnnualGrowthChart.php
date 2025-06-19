<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AnnualGrowthChart extends ChartWidget
{
    protected static ?string $heading = 'Croissance annuelle du chiffre d\'affaires payé';

    public ?int $year = null;

    public function mount(): void
    {
        $this->year = now()->year;
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $year = $this->year ?? now()->year;

        $monthlyRevenues = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthlyRevenues[] = DB::table('invoices')
                ->join('invoice_lines', 'invoices.id', '=', 'invoice_lines.invoice_id')
                ->where('invoices.invoice_status_id', 3) // factures payées
                ->whereYear('invoices.payment_date', $year)
                ->whereMonth('invoices.payment_date', $month)
                ->sum('invoice_lines.line_total');
        }

        // Calcul du chiffre d'affaires cumulé
        $cumulativeRevenues = [];
        $sum = 0;
        foreach ($monthlyRevenues as $revenue) {
            $sum += $revenue;
            $cumulativeRevenues[] = $sum;
        }

        $labels = [
            'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
            'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
        ];

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => "Croissance cumulée du chiffre d'affaires payé en $year",
                    'data' => $cumulativeRevenues,
                    'fill' => false,
                    'borderColor' => '#3B82F6',
                    'tension' => 0.3,
                    'pointBackgroundColor' => '#2563EB',
                ],
            ],
        ];
    }
}
