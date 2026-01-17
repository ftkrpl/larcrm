<?php

namespace App\Filament\Resources\VisitResource\Schemas; // Pastikan 'VisitResource', bukan 'Visits'

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class VisitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kodesales')
                    ->required(),
                TextInput::make('kodecust')
                    ->required(),
                TextInput::make('kodeacum')
                    ->required(),
                TextInput::make('namacust')
                    ->required(),
                TextInput::make('namapic')
                    ->required(),
                TextInput::make('jabatanpic')
                    ->required(),
                Textarea::make('notes')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('status')
                    ->default(null),
                DatePicker::make('visit_date')
                    ->required(),
            ]);
    }
}
