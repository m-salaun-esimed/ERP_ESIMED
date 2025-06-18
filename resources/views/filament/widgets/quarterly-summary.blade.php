<x-filament::widget>
    <x-filament::card>
        <div class="flex justify-between items-center mb-4">
            <x-filament::button wire:click="previousQuarter">
                Previous Quarter
            </x-filament::button>
            <h2 class="text-lg font-bold">
                Q{{ $quarter }} {{ $year }} ({{ $start->format('d/m/Y') }} - {{ $end->format('d/m/Y') }})
            </h2>
            <x-filament::button wire:click="nextQuarter">
                Next Quarter
            </x-filament::button>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="font-semibold">Paid Revenue:</p>
                <p>{{ number_format($paidRevenue, 2, ',', ' ') }} €</p>
            </div>
            <div>
                <p class="font-semibold">Estimated Revenue:</p>
                <p>{{ number_format($estimatedRevenue, 2, ',', ' ') }} €</p>
            </div>
            <div>
                <p class="font-semibold">Charges to Pay:</p>
                <p>{{ number_format($chargesToPay, 2, ',', ' ') }} €</p>
            </div>
            <div>
                <p class="font-semibold">Estimated Charges to Pay:</p>
                <p>{{ number_format($estimatedChargesToPay, 2, ',', ' ') }} €</p>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
