<?php

namespace App\Filament\Resources\CustomerResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('branch')
                    ->options([
            'Sidoarjo' => 'Sidoarjo',
            'Jakarta' => 'Jakarta',
            'Bandung' => 'Bandung',
            'Semarang' => 'Semarang',
            'Yogyakarta' => 'Yogyakarta',
            'Malang' => 'Malang',
            'Denpasar' => 'Denpasar',
            'Makassar' => 'Makassar',
            'Jember' => 'Jember',
            'Gift Shop' => 'Gift shop',
            'Holding' => 'Holding',
        ])
                    ->required(),
                TextInput::make('customerid')
                    ->required(),
                TextInput::make('extrefnbr')
                    ->default(null),
                TextInput::make('customername')
                    ->default(null),
                Select::make('status')
                    ->options([
            'Active' => 'Active',
            'On Hold' => 'On hold',
            'Credit Hold' => 'Credit hold',
            'On-Time' => 'On  time',
            'Inactive' => 'Inactive',
        ])
                    ->default('Active')
                    ->required(),
                TextInput::make('addressline1')
                    ->default(null),
                TextInput::make('addressline2')
                    ->default(null),
                Select::make('priceclass')
                    ->options([
            'Dist' => 'Dist',
            'Ecom' => 'Ecom',
            'FS' => 'F s',
            'Grosir' => 'Grosir',
            'GT' => 'G t',
            'MT' => 'M t',
            'NKA' => 'N k a',
            'Perwakilan' => 'Perwakilan',
        ])
                    ->default(null),
                TextInput::make('tipe1')
                    ->default(null),
                TextInput::make('tipe2')
                    ->default(null),
                TextInput::make('tipe3')
                    ->default(null),
                TextInput::make('admar')
                    ->default(null),
            ]);
    }
}
