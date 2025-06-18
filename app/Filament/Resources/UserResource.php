<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use App\Filament\Resources\UserResource\Pages\EditProfile;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
        TextInput::make('name')->required(),
        TextInput::make('email')->email()->required(),
        TextInput::make('phone_number')
            ->tel()
            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
            ->required(),
        TextInput::make('address')->required(),
        TextInput::make('charge_rate')->required(),
        TextInput::make('birth_date')
            ->mask('99/99/9999')
            ->placeholder('MM/DD/YYYY'),
        TextInput::make('password')
            ->password()
            ->revealable()
            ->required(),
    ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('created_at')
                    ->date('d/m/Y à H:i'),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Créé après'),
                        DatePicker::make('created_until')
                            ->label('Créé avant'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([

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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-user'; 
    }

    public static function getNavigationSort(): int
    {
        return 10;
    }

    public static function getNavigationLabel(): string
    {
        return 'Users';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->admin === 1;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->admin;
    }

    public static function canCreate(): bool
    {
        return auth()->user()->admin;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->admin;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->admin;
    }
}
