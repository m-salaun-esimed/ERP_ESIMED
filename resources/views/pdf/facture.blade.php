<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $facture->invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
    </style>
</head>
<body>
    <h1>Facture #{{ $facture->invoice_number }}</h1>

    <p><strong>Date d’émission :</strong> {{ $facture->issue_date }}</p>
    <p><strong>Date d'échéance :</strong> {{ $facture->due_date }}</p>

    <p><strong>Projet :</strong> {{ $facture->quote && $facture->quote->project ? $facture->quote->project->name : 'N/A' }}</p>
    <table style="width: 100%; margin-top: 20px; border: none;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <strong>Émetteur :</strong><br>
                {{ $user->name ?? 'N/A' }}<br>
                {{ $user->address ?? 'Adresse non disponible' }}<br>
                {{ $user->email ?? 'Email non disponible' }}<br>
                {{ $user->phone_number ?? 'Numéro de téléphone non disponible' }}
            </td>
            <td style="width: 50%; text-align: right; vertical-align: top;">
                <strong>Destinateur :</strong><br>
                {{ $facture->quote?->project?->customer?->name ?? 'N/A' }}<br>
                {{ $facture->quote?->project?->customer?->address ?? 'Adresse non disponible' }}<br>
                {{ $facture->quote?->project?->customer?->email ?? 'Email non disponible' }}<br>
                {{ $facture->quote?->project?->customer?->phone_number ?? 'Numéro de téléphone non disponible' }}
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($facture->invoiceLines as $line)
                <tr>
                    <td>{{ $line->description }}</td>
                    <td>{{ $line->quantity }}</td>
                    <td>{{ number_format($line->unit_price, 2) }} €</td>
                    <td>{{ number_format($line->line_total, 2) }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @php
        $totalHT = $facture->total_cost;
        $tva = $totalHT * 0.20;
        $totalTTC = $totalHT + $tva;
    @endphp

    <p style="text-align:right; margin-top: 20px;">
        <strong>Total HT : {{ number_format($totalHT, 2) }} €</strong>
    </p>
    <p style="text-align:right; margin-top: 5px;">
        <strong>TVA 20% : {{ number_format($tva, 2) }} €</strong>
    </p>
    <p style="text-align:right; margin-top: 5px;">
        <strong>TOTAL TTC : {{ number_format($totalTTC, 2) }} €</strong>
    </p>
    <p style="text-align:right; margin-top: 20px;">
        <strong>Règlement : {{ $facture->payment_type  }} </strong>
    </p>
</body>
</html>
