<x-filament::widget>
    <x-filament::card>
        <div class="flex justify-between items-center mb-4">
            <x-filament::button wire:click="previousQuarter">
                Trimestre précédent
            </x-filament::button>
            <h2 class="text-lg font-bold">
                T{{ $quarter }} {{ $year }} ({{ $start->format('d/m/Y') }} - {{ $end->format('d/m/Y') }})
            </h2>
            <x-filament::button wire:click="nextQuarter">
                Trimestre suivant
            </x-filament::button>
        </div>

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
