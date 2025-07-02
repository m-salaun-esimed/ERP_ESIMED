    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Relance de facture</title>
    </head>
    <body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px;">
        <div style="background-color: #ffffff; padding: 30px; border-radius: 8px; max-width: 600px; margin: auto;">
            <h2 style="color: #333;">Relance de facture</h2>
            
            <p>Bonjour {{ $invoice->quote->project->customer->name ?? 'client' }},</p>

            <p>Nous vous écrivons pour vous rappeler que la facture suivante est en attente de paiement :</p>

            <ul>
                <li><strong>Référence :</strong> {{ $invoice->invoice_number }}</li>
                <li><strong>Montant :</strong> {{ number_format($invoice->total_cost, 2) }} €</li>
                <li><strong>Date d’échéance :</strong> {{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') }}</li>
            </ul>

            <p>Merci de procéder au règlement dès que possible.</p>

            <p>Si le paiement a déjà été effectué, veuillez ignorer ce message.</p>

            <p style="margin-top: 30px;">Cordialement</p>
        </div>
    </body>
    </html>
