<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class QuarterlySummary extends Widget
{
    protected static string $view = 'filament.widgets.quarterly-summary';

    public ?int $year = null;
    public ?int $quarter = null;
    public float $chargeRate = 0;

    public function mount(): void
    {
        $now = now();
        $this->year = $now->year;
        $this->quarter = intval(ceil($now->month / 3));

        $this->chargeRate = auth()->user()->charge_rate ?? 0;
    }

    public function getStartAndEndDates(): array
    {
        $start = Carbon::create($this->year)->startOfYear()->addMonths(($this->quarter - 1) * 3)->startOfDay();
        $end = (clone $start)->addMonths(3)->subSecond();

        return [$start, $end];
    }

    public function getPaidRevenue(): float
    {
        [$start, $end] = $this->getStartAndEndDates();

        return DB::table('invoices')
            ->join('invoice_lines', 'invoices.id', '=', 'invoice_lines.invoice_id')
            ->where('invoices.invoice_status_id', 3)
            ->whereBetween('invoices.payment_date', [$start, $end])
            ->sum('invoice_lines.line_total');
    }

    public function getEstimatedRevenue(): float
    {
        [$start, $end] = $this->getStartAndEndDates();

        $paidRevenue = $this->getPaidRevenue();

        $sentRevenue = DB::table('invoices')
            ->join('invoice_lines', 'invoices.id', '=', 'invoice_lines.invoice_id')
            ->where('invoices.invoice_status_id', 2)
            ->whereBetween('invoices.due_date', [$start, $end])
            ->sum('invoice_lines.line_total');

        return $paidRevenue + $sentRevenue;
    }

    public function getChargesToPay(): float
    {
        return $this->getPaidRevenue() * ($this->chargeRate / 100);
    }

    public function getEstimatedChargesToPay(): float
    {
        return $this->getEstimatedRevenue() * ($this->chargeRate / 100);
    }

    public function previousQuarter(): void
    {
        if ($this->quarter === 1) {
            $this->quarter = 4;
            $this->year--;
        } else {
            $this->quarter--;
        }
    }

    public function nextQuarter(): void
    {
        if ($this->quarter === 4) {
            $this->quarter = 1;
            $this->year++;
        } else {
            $this->quarter++;
        }
    }

    protected function getViewData(): array
    {
        [$start, $end] = $this->getStartAndEndDates();

        return [
            'start' => $start,
            'end' => $end,
            'paidRevenue' => $this->getPaidRevenue(),
            'estimatedRevenue' => $this->getEstimatedRevenue(),
            'chargesToPay' => $this->getChargesToPay(),
            'estimatedChargesToPay' => $this->getEstimatedChargesToPay(),
            'year' => $this->year,
            'quarter' => $this->quarter,
            'chargeRate' => $this->chargeRate,
        ];
    }
}
