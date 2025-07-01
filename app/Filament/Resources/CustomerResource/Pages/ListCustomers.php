<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getDeletedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Client supprimé')
            ->body('Le client a été supprimé avec succès.')
            ->success()
            ->icon('heroicon-o-user-minus');
    }
}
