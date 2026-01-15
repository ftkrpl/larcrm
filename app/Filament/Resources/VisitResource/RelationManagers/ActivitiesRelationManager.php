<?php

namespace App\Filament\Resources\VisitResource\RelationManagers;

use App\Filament\Resources\VisitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\Layout\Stack; // Pastikan import ini ada di atas

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';
    protected static ?string $title = 'Aktivitas';

    protected static ?string $relatedResource = VisitResource::class;

    // Gunakan \Filament\Schemas\Schema sesuai permintaan error-nya
    public function form(\Filament\Schemas\Schema $form): \Filament\Schemas\Schema
    {
        return $form
            ->components([ // Gunakan components() bukan schema() jika pakai Schema class
                \Filament\Forms\Components\Select::make('jenis')
                    ->options([
                        'Regular Visit' => 'Regular Visit',
                        'New Customer' => 'New Customer',
                        'New Product Development' => 'New Product Development',
                        'Existing Product Offering' => 'Existing Product Offering',
                        'Competitor Info' => 'Competitor Info',
                    ])->required(),
                \Filament\Forms\Components\TextInput::make('kode_barang'),
                \Filament\Forms\Components\TextInput::make('kelompok_barang'),
                \Filament\Forms\Components\Select::make('sample')
                    ->options(['Yes' => 'Yes', 'No' => 'No'])->required(),
                \Filament\Forms\Components\Select::make('status')
                    ->options(['Failed' => 'Failed', 'Deal' => 'Deal', 'On Progress' => 'On Progress'])->required(),
                \Filament\Forms\Components\Textarea::make('result')->columnSpanFull(),
            ]);
    }

    // Gunakan \Filament\Tables\Table sesuai permintaan standar Filament
    public function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return $table
            ->columns([
            Stack::make([
                \Filament\Tables\Columns\TextColumn::make('jenis')->badge(),
                \Filament\Tables\Columns\TextColumn::make('kode_barang')->label('Barang'),
                \Filament\Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'Deal' => 'success',
                        'Failed' => 'danger',
                        default => 'warning',
                    }),
                \Filament\Tables\Columns\TextColumn::make('result')->label('Result'),
                ])
            ])
            ->contentGrid([
                'default' => 1,
                'md' => 2,
            ])
            ->headerActions([
                // GANTI DARI Tables\Actions\CreateAction MENJADI:
                \Filament\Actions\CreateAction::make()
                    ->label('New Activity') // <-- Ganti caption tombol di sini
                    ->icon('heroicon-m-plus-circle') // Opsional: tambah ikon biar makin cakep
                    ->modalHeading('Buat Aktivitas Kunjungan'), // Judul di kotak popup-nya
            ])
            ->actions([
                // GANTI SEMUA MENJADI:
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ]);
    }
}
