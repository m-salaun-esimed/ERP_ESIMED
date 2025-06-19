<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\QueryException;
use App\Filament\Resources\InvoiceResource\RelationManagers\InvoicesRelationManager;
use App\Filament\Resources\CustomerResource\Pages\ViewCustomer;
use App\Filament\Resources\CustomerResource\RelationManagers\ProjectsRelationManager;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Clients';
    protected static ?string $label = 'Clients';
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nom')
                    ->required(),
                TextInput::make('contact_name')
                    ->label('Nom du contact')
                    ->required(),
                TextInput::make('address')
                    ->label('Adresse')
                    ->required(),
                TextInput::make('phone_number')
                    ->label('Numéro de téléphone')
                    ->tel()
                    ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                    ->required(),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),
                TextInput::make('city')
                    ->label('Ville')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nom')->searchable(),
                TextColumn::make('contact_name')->label('Nom du contact')->searchable(),
                TextColumn::make('phone_number')->label('Téléphone')->searchable(),
                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('address')->label('Adresse')->searchable(),
            ])
            ->actions([
                Tables\Actions\Action::make('View')
                    ->label('Voir')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => ViewCustomer::getUrl(['record' => $record->getKey()])),
                Tables\Actions\EditAction::make()
                    ->label('Modifier'),

                DeleteAction::make()
                    ->label('Supprimer')
                    ->action(function ($record) {
                        try {
                            $record->delete();
                            Notification::make()
                                ->title('Client supprimé')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Échec de la suppression')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('supprimer_clients')
                    ->label('Supprimer les clients')
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            try {
                                $record->delete();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title("Erreur lors de la suppression")
                                    ->body("Impossible de supprimer le client \"{$record->name}\" : {$e->getMessage()}")
                                    ->danger()
                                    ->send();

                                return;
                            }
                        }

                        Notification::make()
                            ->title("Clients supprimés")
                            ->success()
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion()
                    ->requiresConfirmation()
                    ->color('danger')
                    ->icon('heroicon-o-trash'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ProjectsRelationManager::class,
            InvoicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
            'view' => Pages\ViewCustomer::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }

    public static function getNavigationSort(): int
    {
        return 1;
    }
}
