@component('mail::message')
# Bonjour {{ $user->name }},

Voici la liste des factures en retard :

@foreach ($overdueInvoices as $item)
- Facture #{{ $item['invoice']->id }}  
  Client : {{ $item['customer']->name }}  
  Projet : {{ $item['project']->name ?? 'N/A' }}  
  Montant : {{ number_format($item['invoice']->total_cost, 2) }} €  
  Date d'échéance : {{ \Carbon\Carbon::parse($item['invoice']->due_date)->format('d/m/Y') }}

@endforeach

Merci de prendre les mesures nécessaires.

Cordialement,  
{{ config('app.name') }}
@endcomponent
