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

        if (($statusId == 1 || $statusId == 2) && !$hasLines) {
            Notification::make()
                ->title('Erreur')
                ->body('Vous ne pouvez pas passer ce devis en statut "accepté" ou "envoyé" sans avoir au moins une ligne.')
                ->danger()
                ->send();

            $this->halt(); // ⛔️ stoppe la sauvegarde sans erreur
        }
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if ($this->record->status_id === 2) {
            Notification::make()
                ->title('Cette facture est déjà payée')
                ->body('Vous ne pouvez plus la modifier.')
                ->danger()
                ->send();

            $this->redirect(QuoteResource::getUrl('view', ['record' => $this->record]));
        }

        return $data;
    }
}
