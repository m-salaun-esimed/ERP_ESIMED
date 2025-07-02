<x-filament::widget>
    <x-filament::card>
        <div class="flex justify-between items-center mb-4">
            {{-- Bouton trimestre précédent --}}
            <x-filament::button wire:click="previousQuarter" class="mr-4 text-4xl" style="font-size: 2rem;">
                &larr;
            </x-filament::button>

            {{-- Titre du trimestre --}}
            <h2 class="text-lg font-bold text-center">
                T{{ $quarter }} {{ $year }} ({{ $start->format('d/m/Y') }} - {{ $end->format('d/m/Y') }})
            </h2>

            {{-- Bouton trimestre suivant --}}
            <x-filament::button wire:click="nextQuarter" class="ml-4 text-4xl" style="font-size: 2rem;">
                &rarr;
            </x-filament::button>
        </div>

        {{-- Statistiques --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="font-semibold">Revenu payé :</p>
                <p>{{ number_format($paidRevenue, 2, ',', ' ') }} €</p>
            </div>
            <div>
                <p class="font-semibold">Revenu estimé :</p>
                <p>{{ number_format($estimatedRevenue, 2, ',', ' ') }} €</p>
            </div>
            <div>
                <p class="font-semibold">Charges à payer :</p>
                <p>{{ number_format($chargesToPay, 2, ',', ' ') }} €</p>
            </div>
            <div>
                <p class="font-semibold">Charges estimées à payer :</p>
                <p>{{ number_format($estimatedChargesToPay, 2, ',', ' ') }} €</p>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
