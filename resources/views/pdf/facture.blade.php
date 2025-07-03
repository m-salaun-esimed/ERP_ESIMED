<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $facture->invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
        .header-table td { border: none; }
        .totaux { text-align: right; margin-top: 10px; }
    </style>
</head>
<body>
    <h1>Facture n°{{ $facture->invoice_number }}</h1>

    <p><strong>Date d’émission :</strong> {{ $facture->issue_date }}</p>
    <p><strong>Date d'échéance :</strong> {{ $facture->due_date }}</p>
    <p><strong>Projet :</strong> {{ $facture->quote?->project?->name ?? 'N/A' }}</p>

    <table class="header-table">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <strong>Émetteur :</strong><br>
                {{ $user->name ?? 'N/A' }}<br>
                @php
                    $addressLine = trim(
                        implode(', ', array_filter([
                            $user->street ?? null,
                            ($user->postal_code ?? '') . ' ' . ($user->city ?? ''),
                            $user->country ?? null
                        ]))
                    );
                @endphp
                {{ $addressLine ?: 'Adresse non disponible' }}<br>
                {{ $user->email ?? 'Email non disponible' }}<br>
                {{ $user->phone_number ?? 'Numéro non disponible' }}
            </td>
            <td style="width: 50%; text-align: right; vertical-align: top;">
                <strong>Destinataire :</strong><br>
                @php
                    $customer = $facture->quote?->project?->customer;
                    $customerAddressLine = trim(
                        implode(', ', array_filter([
                            $customer?->street ?? null,
                            ($customer?->postal_code ?? '') . ' ' . ($customer?->city ?? ''),
                            $customer?->country ?? null
                        ]))
                    );
                @endphp
                {{ $customer?->name ?? 'N/A' }}<br>
                {{ $customerAddressLine ?: 'Adresse non disponible' }}<br>
                {{ $customer?->email ?? 'Email non disponible' }}<br>
                {{ $customer?->phone_number ?? 'Numéro non disponible' }}
            </td>

        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Quantité</th>
                <th>Prix unitaire (€)</th>
                <th>Total (€)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($facture->invoiceLines as $line)
                <tr>
                    <td>{{ $line->description }}</td>
                    <td>{{ $line->quantity }}</td>
                    <td>{{ number_format($line->unit_price, 2, ',', ' ') }}</td>
                    <td>{{ number_format($line->line_total, 2, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @php
        $totalHT = $facture->total_cost;
        $tva = $totalHT * 0.20;
        $totalTTC = $totalHT + $tva;
    @endphp

    <p class="totaux"><strong>Total HT :</strong> {{ number_format($totalHT, 2, ',', ' ') }} €</p>
    <p class="totaux"><strong>TVA (20%) :</strong> {{ number_format($tva, 2, ',', ' ') }} €</p>
    <p class="totaux"><strong>Total TTC :</strong> {{ number_format($totalTTC, 2, ',', ' ') }} €</p>

    <p class="totaux" style="margin-top: 20px;"><strong>Mode de règlement :</strong> {{ $facture->payment_type ?? 'Non spécifié' }}</p>

    {{-- Optionnel : à afficher uniquement si c'est une proforma --}}
    @if($facture->is_proforma ?? false)
        <p style="margin-top: 40px; font-style: italic; color: #777;">
            Ce document est une facture proforma, non valable pour la comptabilité. Aucun paiement ne doit être effectué sur cette base.
        </p>
    @endif
</body>
</html>
