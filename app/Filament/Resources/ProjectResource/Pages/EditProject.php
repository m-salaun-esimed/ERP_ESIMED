<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Project;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
           Actions\DeleteAction::make()
            ->visible(fn (Project $record) =>
                $record->quotes()->count() === 0 &&
                $record->invoices()->count() === 0
            ),
        ];
    }
}
