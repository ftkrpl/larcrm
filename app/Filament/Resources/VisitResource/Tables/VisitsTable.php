<?php

namespace App\Filament\Resources\VisitResource\Tables; // Samakan dengan nama foldernya

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VisitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kodesales')
                    ->searchable(),
                TextColumn::make('kodecust')
                    ->searchable(),
                TextColumn::make('kodeacum')
                    ->searchable(),
                TextColumn::make('namacust')
                    ->searchable(),
                TextColumn::make('namapic')
                    ->searchable(),
                TextColumn::make('jabatanpic')
                    ->searchable(),
                TextColumn::make('status'),
                TextColumn::make('visit_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
