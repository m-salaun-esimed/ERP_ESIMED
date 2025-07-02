<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Models\Invoice; // Import du modèle Eloquent Invoice
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected Invoice $oldRecord;

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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->oldRecord = $this->getRecord()->replicate();

        return $data;
    }
    
    protected function afterSave(): void
    {
        $newStatus = (int) $this->record->invoice_status_id;
        $oldStatus = (int) $this->oldRecord->invoice_status_id;

        if ($newStatus === 3 && $oldStatus !== 3) {
            $recipient = $this->record->quote->project->customer->email ?? null;

            if ($recipient) {
                \Notification::route('mail', $recipient)
                    ->notify(new \App\Notifications\InvoicePaidNotification($this->record));
            }
        }
    }
}
