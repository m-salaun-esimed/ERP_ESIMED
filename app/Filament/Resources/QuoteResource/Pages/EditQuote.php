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
            Actions\DeleteAction::make()
                ->action(function () {
                    try {
                        $this->record->delete();

                        Notification::make()
                            ->title('Devis supprimé')
                            ->body('Le devis a été supprimé avec succès.')
                            ->success()
                            ->icon('heroicon-o-document-minus') // icône adaptée
                            ->send();
                        return redirect(static::getResource()::getUrl('index'));
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Suppression impossible')
                            ->body('Le devis ne peut pas être supprimé car il est lié à d’autres éléments.')
                            ->danger()
                            ->icon('heroicon-o-exclamation-triangle') // plus impactant que exclamation-circle
                            ->send();
                        $this->halt();
                    }
                }),
        ];
    }

    protected function beforeSave(): void
    {
        $statusId = $this->form->getState()['status_id'] ?? null;
        $hasLines = $this->record->quoteLines()->count() > 0;

        if (($statusId == 1 || $statusId == 2) && !$hasLines) {
           Notification::make()
            ->title('Statut invalide')
            ->body('Impossible de passer ce devis à "accepté" ou "envoyé" sans au moins une ligne.')
            ->danger()
            ->icon('heroicon-o-exclamation-circle') 
            ->send();

            $this->halt();
        }
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if ($this->record->status_id === 2) {
            Notification::make()
                ->title('Devis déjà accepté')
                ->body('Vous ne pouvez plus le modifier.')
                ->danger()
                ->icon('heroicon-o-lock-closed') // très clair pour un verrou
                ->send();

            $this->redirect(QuoteResource::getUrl('view', ['record' => $this->record]));
        }

        return $data;
    }
}
