<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
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
                )
                ->action(function () {
                    try {
                        $this->record->delete();

                        Notification::make()
                            ->title('Projet supprimé')
                            ->body('Le projet a été supprimé avec succès.')
                            ->success()
                            ->icon('heroicon-o-archive-box-x-mark') // 🔁 adapté pour un projet
                            ->send();

                        return redirect(static::getResource()::getUrl('index'));
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Suppression impossible')
                            ->body('Le projet ne peut pas être supprimé car il a un ou plusieurs devis affiliés.')
                            ->danger()
                            ->icon('heroicon-o-exclamation-circle') // ⚠️ cohérent avec une erreur
                            ->send();

                        $this->halt();
                    }
                }),
        ];
    }
}
