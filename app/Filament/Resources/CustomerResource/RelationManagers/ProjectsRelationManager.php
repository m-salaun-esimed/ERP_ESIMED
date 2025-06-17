<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use App\Models\Project;
use App\Models\ProjectStatus;
use App\Filament\Resources\ProjectResource\Pages\ViewProject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),

                Select::make('status_project_id')
                    ->label('Status')
                    ->options(function () {
                        return ProjectStatus::pluck('name', 'id')->toArray();
                    })
                    ->searchable(false)
                    ->required()
                    ->reactive(),

                Select::make('customer_id')
                    ->hidden()
                    ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->id),
                DatePicker::make('date_started')
                    ->label('Date de début')
                    ->required(fn (Get $get) => $get('status') !== 'prospect'),

                DatePicker::make('date_end')
                    ->label('Date de fin')
                    ->required(fn (Get $get) => in_array($get('status'), ['terminé', 'annulé'])),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('statusProject.name')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'gray' => 'prospect',
                        'blue' => 'devis envoyé',
                        'yellow' => 'devis accepté',
                        'indigo' => 'démarré',
                        'green' => 'terminé',
                        'red' => 'annulé',
                    ])
                    ->searchable(),
                TextColumn::make('date_started')->date('d/m/Y à H:i'),
                TextColumn::make('date_end')->date('d/m/Y à H:i'),
                TextColumn::make('total_paid_invoices_amount')
                    ->getStateUsing(fn (Project $record) => $record->total_paid_invoices_amount)
                    ->label('Total invoices payed (€)')
                    ->money('EUR', locale: 'fr_FR'),
            ])
            ->filters([
                SelectFilter::make('status_project_id')
                    ->label('Status')
                    ->options(function () {
                        return ProjectStatus::pluck('name', 'id')->toArray();
                    })
                    ->default(function () {
                        return ProjectStatus::where('name', 'démarré')->value('id');
                    }),

                SelectFilter::make('customer_id')
                    ->label('Customers')
                    ->options(function () {
                        $user = Auth::user();

                        if (!$user) {
                            return [];
                        }

                        return $user->customers()->pluck('name', 'id')->toArray();
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => ViewProject::getUrl(['record' => $record->getKey()])),
            ])
            ->recordUrl(null)
            ->bulkActions([]);
    }
}
