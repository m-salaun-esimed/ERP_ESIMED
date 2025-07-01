<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->action(function () {
                    try {
                        $this->record->delete();

                        Notification::make()
                            ->title('Facture supprimée')
                            ->body('La facture a été supprimée avec succès.')
                            ->success()
                            ->icon('heroicon-o-archive-box-x-mark')
                            ->send();

                        return redirect(static::getResource()::getUrl('index'));
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Suppression impossible')
                            ->body('La facture ne peut pas être supprimée car elle a des dépendances.')
                            ->danger()
                            ->icon('heroicon-o-exclamation-circle')
                            ->send();

                        $this->halt();
                    }
                }),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if ($this->record->invoice_status_id === 3) {
            Notification::make()
                ->title('Facture déjà réglée')
                ->body('Vous ne pouvez plus modifier cette facture.')
                ->danger()
                ->send();

            $this->redirect(InvoiceResource::getUrl('view', ['record' => $this->record]));
        }

        return $data;
    }
}
