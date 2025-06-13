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

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                TextInput::make('contact_name')
                    ->required(),
                TextInput::make('address')
                    ->required(),
                TextInput::make('phone_number')
                    ->tel()
                    ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->required(),
                TextInput::make('city')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('name')->searchable(),
            TextColumn::make('contact_name')->searchable(),
            TextColumn::make('phone_number')->searchable(),
            TextColumn::make('email')->searchable(),
            TextColumn::make('address')->searchable(),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),

            DeleteAction::make()
                ->action(function ($record) {
                    try {
                        $record->delete();
                        Notification::make()
                            ->title('Client supprimé')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Suppression impossible')
                            ->body($e->getMessage()) // ou message custom
                            ->danger()
                            ->send();
                    }
                }),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make()
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            try {
                                $record->delete();
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->title('Suppression impossible')
                                    ->body("Le client {$record->name} possède des projets associés.")
                                    ->danger()
                                    ->send();
                                return;
                            }
                        }

                        Notification::make()
                            ->title('Clients supprimés')
                            ->success()
                            ->send();
                    }),
            ]),
        ]);
}

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
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
