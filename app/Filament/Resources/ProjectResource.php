<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Get;
use Filament\Tables\Filters\SelectFilter;
use App\Models\ProjectStatus;
use App\Filament\Resources\ProjectResource\Pages\ViewProject;
use App\Filament\Resources\ProjectResource\RelationManagers\QuotesRelationManager;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationLabel = 'Projets';

    protected static ?string $label = 'Projets';
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nom du projet')
                    ->required(),

                Select::make('status_project_id')
                    ->label('Statut')
                    ->options(function () {
                        return ProjectStatus::pluck('name', 'id')->toArray();
                    })
                    ->searchable(false)
                    ->required()
                    ->reactive(),

                Select::make('customer_id')
                    ->label('Client')
                    ->options(function () {
                        $user = Auth::user();

                        if (!$user) {
                            return [];
                        }

                        return $user->customers()->pluck('name', 'id')->toArray();
                    })
                    ->searchable()
                    ->required(),

                DatePicker::make('date_started')
                    ->label('Date de début')
                    ->required(fn (Get $get) => $get('status') !== 'prospect'),

                DatePicker::make('date_end')
                    ->label('Date de fin')
                    ->required(fn (Get $get) => in_array($get('status'), ['terminé', 'annulé'])),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom du projet')
                    ->searchable(),

                TextColumn::make('statusProject.name')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'prospect' => 'gray',
                        'en cours' => 'warning',
                        'terminé' => 'success',
                        'annulé' => 'danger',
                    })
                    ->searchable(),

                TextColumn::make('customer.name')
                    ->label('Client')
                    ->searchable(),

                TextColumn::make('date_started')
                    ->label('Date de début')
                    ->date('d/m/Y à H:i'),

                TextColumn::make('date_end')
                    ->label('Date de fin')
                    ->date('d/m/Y à H:i'),

                TextColumn::make('total_paid_invoices_amount')
                    ->label('Total factures payées (€)')
                    ->getStateUsing(fn (Project $record) => $record->total_paid_invoices_amount)
                    ->money('EUR', locale: 'fr_FR')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status_project_id')
                    ->label('Statut')
                    ->options(function () {
                        return ProjectStatus::pluck('name', 'id')->toArray();
                    })
                    ->default(function () {
                        return ProjectStatus::where('name', 'en cours')->value('id');
                    }),

                SelectFilter::make('customer_id')
                    ->label('Clients')
                    ->options(function () {
                        $user = Auth::user();

                        if (!$user) {
                            return [];
                        }

                        return $user->customers()->pluck('name', 'id')->toArray();
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('Voir')
                    ->label('Voir')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => ViewProject::getUrl(['record' => $record->getKey()])),
                Tables\Actions\EditAction::make()
                    ->label('Modifier'),
            ])
            ->recordUrl(null)
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            QuotesRelationManager::class,
            \App\Filament\Resources\InvoiceResource\RelationManagers\InvoicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
            'view' => ViewProject::route('/{record}/view'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return  parent::getEloquentQuery()
            ->whereHas('customer', function (Builder $query) {
                $query->where('user_id', Auth::id());
            });
    }

    public static function getNavigationSort(): int
    {
        return 2;
    }
}
