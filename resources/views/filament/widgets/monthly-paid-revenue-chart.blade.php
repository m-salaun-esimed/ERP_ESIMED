<x-filament::widget>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <x-filament::card>
        <h2 class="text-lg font-bold mb-4">CA mensuel payé - Année {{ $year }}</h2>

        <canvas id="paidRevenueChart" width="400" height="200"></canvas>

        <script>
            document.addEventListener('livewire:load', function () {
                console.log('livewire:load fired');
                const ctx = document.getElementById('paidRevenueChart').getContext('2d');
                console.log(ctx);
                new Chart(ctx, {
                    // ... ton config
                });
            });
            document.addEventListener('livewire:load', function () {
                const ctx = document.getElementById('paidRevenueChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
                        datasets: [{
                            label: 'CA payé (€)',
                            data: @json($monthlyPaidRevenue),
                            backgroundColor: 'rgba(59, 130, 246, 0.7)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return value.toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' });
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.parsed.y.toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' });
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    </x-filament::card>
</x-filament::widget>
