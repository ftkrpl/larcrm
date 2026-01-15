<?php

namespace App\Filament\Resources;

use App\Models\Visit;
use Filament\Resources\Resource;
use App\Filament\Resources\VisitResource\Pages;
use Filament\Tables\Columns\Layout\Stack; // Pastikan import ini ada di atas

class VisitResource extends Resource
{
    protected static ?string $model = Visit::class;

    // Kita pakai tipe data yang sudah disetujui sebelumnya
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Kunjungan Sales';

    // KUNCI: Gunakan \Filament\Schemas\Schema sesuai pesan error-nya
    public static function form(\Filament\Schemas\Schema $form): \Filament\Schemas\Schema
    {
        return $form
            ->components([
                \Filament\Forms\Components\TextInput::make('customer_name')->required(),
                \Filament\Forms\Components\DatePicker::make('visit_date')->required(),
                \Filament\Forms\Components\Textarea::make('notes')->required(),
            ]);
    }

    public static function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return $table
            ->columns([
                Stack::make([ // <--- SISIPKAN DI SINI
                    \Filament\Tables\Columns\TextColumn::make('namacust')
                        ->label('Nama Cust')
                        ->weight('bold')
                        ->size('lg') // Perbesar dikit buat judul card
                        ->searchable(),
                    
                    \Filament\Tables\Columns\TextColumn::make('kodesales')
                        ->label('Kode Sales')
                        ->formatStateUsing(fn ($state) => "Sales: " . $state)
                        ->color('gray'),

                    \Filament\Tables\Columns\TextColumn::make('namapic')
                        ->label('Nama PIC')
                        ->formatStateUsing(fn ($state, $record) => "PIC: " . $state . " (" . $record->jabatanpic . ")")
                        ->icon('heroicon-m-user'),

                    \Filament\Tables\Columns\TextColumn::make('visit_date')
                        ->label('Visit Date')
                        ->date('d M Y')
                        ->color('primary')
                        ->icon('heroicon-m-calendar'),
                    
                    \Filament\Tables\Columns\TextColumn::make('status')
                        ->label('Status Kunjungan')
                        ->badge()
                        ->wrap() // <--- Tambahkan ini
                        ->separator(',') // Memecah "A,B,C" menjadi badge terpisah
                        ->color('success') // Warna hijau agar segar dilihat bos
                        ->size('xs'), // Ukuran kecil agar hemat ruang di Card
                ]),
            ])
            ->contentGrid([
                'default' => 1,
                'md' => 2,
                'xl' => 3,
            ])
            // Opsional: Hilangkan garis antar baris agar lebih seperti Card
            ->striped(false)
            ->actions([
                // ALAMAT BARU: Filament\Actions\EditAction (Tanpa kata Tables)
                \Filament\Actions\EditAction::make(), 
            ])
            ->bulkActions([
                \Filament\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVisits::route('/'),
            'create' => Pages\CreateVisit::route('/create'),
            'edit' => Pages\EditVisit::route('/{record}/edit'),
        ];
    }
}