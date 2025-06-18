<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class MonthlyPaidRevenueChart extends Widget
{
    protected static string $view = 'filament.widgets.monthly-paid-revenue-chart';

    public ?int $year = null;

    public function mount(): void
    {
        $this->year = now()->year;
    }

    public function getMonthlyPaidRevenue(): array
    {
        $year = $this->year;  // ici, sans []

        $revenues = [];

        for ($month = 1; $month <= 12; $month++) {
            $revenues[$month] = DB::table('invoices')
                ->join('invoice_lines', 'invoices.id', '=', 'invoice_lines.invoice_id')
                ->where('invoices.invoice_status_id', 3)
                ->whereYear('invoices.payment_date', $year)
                ->whereMonth('invoices.payment_date', $month)
                ->sum('invoice_lines.line_total');
        }

        return array_values($revenues); // indexé 0..11 pour JS
    }


    protected function getViewData(): array
    {
        return [
            'year' => $this->year,
            'monthlyPaidRevenue' => $this->getMonthlyPaidRevenue(),
        ];
    }
}
