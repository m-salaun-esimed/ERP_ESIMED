<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\AnnualSummary;
use App\Filament\Widgets\QuarterlySummary;
use App\Filament\Widgets\MonthlyPaidRevenueChart;
use App\Filament\Widgets\AnnualGrowthChart;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.pages.dashboard';
    protected static ?string $title = 'Tableau de bord'; // Titre en français 

    public function getHeaderWidgets(): array
    {
        return [
            AnnualSummary::class,         // Résumé Annuel
            QuarterlySummary::class,      // Résumé Trimestriel
            MonthlyPaidRevenueChart::class, // Graphique des revenus mensuels payés
            AnnualGrowthChart::class,     // Graphique de croissance annuelle
        ];
    }
}
