<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateQuote extends CreateRecord
{
    protected static string $resource = QuoteResource::class;
    protected function beforeCreate(): void
    {
        $statusId = $this->form->getState()['status_id'] ?? null;

        if ($statusId == 2) {
            Notification::make()
                ->title('Erreur')
                ->body('Vous ne pouvez pas créer un devis déjà accepté. Ajoutez d’abord des lignes.')
                ->danger()
                ->send();

            $this->halt();
        }
    }
}
