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
                            ->title('Customer deleted')
                            ->success()
                            ->send();

                        return redirect(static::getResource()::getUrl('index'));
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error during deletion')
                            ->body('The customer cannot be deleted because there are related data linked to it.')
                            ->danger()
                            ->send();

                        $this->halt();
                    }
                }),
        ];
    }


}
