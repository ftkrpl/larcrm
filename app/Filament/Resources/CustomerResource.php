<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Schemas\Schema;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Master Customer';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customerid')->label('ID')->searchable()->sortable(),
                TextColumn::make('customername')->label('Nama Customer')->searchable(),
                TextColumn::make('branch')->badge()->color('gray'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Active' => 'success',
                        'On Hold' => 'warning',
                        'Inactive' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('priceclass')->label('Class'),
                TextColumn::make('admar')->label('Admar'),
            ])
            ->filters([
                SelectFilter::make('branch')
                    ->options([
                        'Sidoarjo' => 'Sidoarjo',
                        'Jakarta' => 'Jakarta',
                        // ... tambahkan lainnya
                    ]),
                SelectFilter::make('status'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
        ];
    }
}