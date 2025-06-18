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
                ->body('You cannot create a quote that is already accepted or sent. Please add lines first.')
                ->danger()
                ->send();

            $this->halt();
        }
    }
}
