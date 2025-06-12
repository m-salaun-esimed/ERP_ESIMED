<?php

namespace App\Filament\Resources\QuoteLineResource\Pages;

use App\Filament\Resources\QuoteLineResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuoteLine extends EditRecord
{
    protected static string $resource = QuoteLineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
