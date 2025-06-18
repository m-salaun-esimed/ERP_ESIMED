<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AnnualGrowthChart extends ChartWidget
{
    protected static ?string $heading = 'Annual Paid Revenue Growth';

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
                ->where('invoices.invoice_status_id', 3) // paid invoices
                ->whereYear('invoices.payment_date', $year)
                ->whereMonth('invoices.payment_date', $month)
                ->sum('invoice_lines.line_total');
        }

        // Calculate the cumulative revenue
        $cumulativeRevenues = [];
        $sum = 0;
        foreach ($monthlyRevenues as $revenue) {
            $sum += $revenue;
            $cumulativeRevenues[] = $sum;
        }

        $labels = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => "Cumulative paid revenue growth in $year",
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
