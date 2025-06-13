<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditQuote extends EditRecord
{
    protected static string $resource = QuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        $statusId = $this->form->getState()['status_id'] ?? null;
        $hasLines = $this->record->quoteLines()->count() > 0;

        if ($statusId == 2 && !$hasLines) {
            Notification::make()
                ->title('Erreur')
                ->body('Vous ne pouvez pas passer ce devis en statut "accepté" sans avoir au moins une ligne.')
                ->danger()
                ->send();

            $this->halt(); // ⛔️ stoppe la sauvegarde sans erreur
        }
    }
}
