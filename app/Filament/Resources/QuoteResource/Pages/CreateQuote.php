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

        if ($statusId == 2 || $statusId == 1) {
            Notification::make()
                ->title('Error')
                ->body('Vous ne pouvez pas créer un devis qui est déjà accepté ou envoyé. Veuillez d\'abord ajouter des lignes.')
                ->danger()
                ->send();

            $this->halt();
        }
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Devis créé')
            ->body('Le nouveau devis a bien été enregistré.')
            ->success()
            ->icon('heroicon-o-document-plus');
    }

}
