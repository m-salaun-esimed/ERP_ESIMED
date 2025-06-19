<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MonthlyPaidRevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Chiffre d\'affaires payé mensuel';

    public ?int $year = null;

    public function mount(): void
    {
        $this->year = now()->year;
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $year = $this->year ?? now()->year;

        // Calcul des revenus mensuels
        $revenues = [];
        for ($month = 1; $month <= 12; $month++) {
            $revenues[] = DB::table('invoices')
                ->join('invoice_lines', 'invoices.id', '=', 'invoice_lines.invoice_id')
                ->where('invoices.invoice_status_id', 3)  // factures payées
                ->whereYear('invoices.payment_date', $year)
                ->whereMonth('invoices.payment_date', $month)
                ->sum('invoice_lines.line_total');
        }

        // Labels : noms des mois en français
        $labels = [
            'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
            'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
        ];

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => "Revenu payé en $year",
                    'data' => $revenues,
                    'backgroundColor' => [
                        '#3B82F6', '#60A5FA', '#93C5FD', '#BFDBFE', '#DBEAFE', '#EFF6FF',
                        '#3B82F6', '#60A5FA', '#93C5FD', '#BFDBFE', '#DBEAFE', '#EFF6FF',
                    ],
                ],
            ],
        ];
    }
}
