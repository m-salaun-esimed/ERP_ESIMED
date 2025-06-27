<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Widgets\ChartWidget;

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
        $userId = auth()->id();
        $year = $this->year ?? now()->year;

        $monthlyRevenues = [];

        for ($month = 1; $month <= 12; $month++) {
            $sum = Invoice::withSum('invoiceLines', 'line_total')
                ->where('invoice_status_id', 3)
                ->whereYear('payment_date', $year)
                ->whereMonth('payment_date', $month)
                ->whereHas('quote.project.customer', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->get()
                ->sum('invoice_lines_sum_line_total');

            $monthlyRevenues[] = $sum;
        }

        $cumulativeRevenues = [];
        $total = 0;
        foreach ($monthlyRevenues as $revenue) {
            $total += $revenue;
            $cumulativeRevenues[] = $total;
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
