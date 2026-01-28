<?php

namespace App\Filament\Resources\CustomerResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('branch')
                    ->badge(),
                TextColumn::make('customerid')
                    ->searchable(),
                TextColumn::make('extrefnbr')
                    ->searchable(),
                TextColumn::make('customername')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('addressline1')
                    ->searchable(),
                TextColumn::make('addressline2')
                    ->searchable(),
                TextColumn::make('priceclass')
                    ->badge(),
                TextColumn::make('tipe1')
                    ->searchable(),
                TextColumn::make('tipe2')
                    ->searchable(),
                TextColumn::make('tipe3')
                    ->searchable(),
                TextColumn::make('admar')
                    ->searchable(),
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
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
