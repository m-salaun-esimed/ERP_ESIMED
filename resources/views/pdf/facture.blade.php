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
    <p><strong>Projet :</strong> {{ $facture->quote && $facture->quote->project ? $facture->quote->project->name : 'N/A' }}</p>

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

    <p style="text-align:right; margin-top: 20px;">
        <strong>Total général : {{ number_format($facture->total_cost, 2) }} €</strong>
    </p>
</body>
</html>
