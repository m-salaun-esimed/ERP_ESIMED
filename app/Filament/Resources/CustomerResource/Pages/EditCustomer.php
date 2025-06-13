<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make()
                ->action(function () {
                    try {
                        $this->record->delete();

                        Notification::make()
                            ->title('Client supprimé')
                            ->success()
                            ->send();

                        return redirect(static::getResource()::getUrl('index'));
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Suppression impossible')
                            ->body('Le client ne peut pas être supprimé car des données y sont liées.')
                            ->danger()
                            ->send();

                        $this->halt();
                    }
                }),
        ];
    }


}
