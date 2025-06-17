<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use App\Models\ProjectStatus;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Filament\Resources\ProjectResource\Pages\ViewProject;


class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
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
                    ->label('Total Factures Payées (€)')
                    ->getStateUsing(fn (Project $record) => $record->total_paid_invoices_amount)

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
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('View')
                            ->icon('heroicon-o-eye')
                            ->url(fn ($record) => ViewProject::getUrl(['record' => $record->getKey()])),
            ])
            ->recordUrl(null)
            ->bulkActions([
            ]);
    }
}
