<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Widgets\ChartWidget;

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
        $userId = auth()->id();
        $year = $this->year ?? now()->year;

        $revenues = [];

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

            $revenues[] = $sum;
        }

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
