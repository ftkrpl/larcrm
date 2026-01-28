<?php

namespace App\Filament\Resources\CustomerResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CustomerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('branch')
                    ->badge(),
                TextEntry::make('customerid'),
                TextEntry::make('extrefnbr')
                    ->placeholder('-'),
                TextEntry::make('customername')
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('addressline1')
                    ->placeholder('-'),
                TextEntry::make('addressline2')
                    ->placeholder('-'),
                TextEntry::make('priceclass')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('tipe1')
                    ->placeholder('-'),
                TextEntry::make('tipe2')
                    ->placeholder('-'),
                TextEntry::make('tipe3')
                    ->placeholder('-'),
                TextEntry::make('admar')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
