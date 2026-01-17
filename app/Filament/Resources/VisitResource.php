<?php

namespace App\Filament\Resources;


use App\Models\Visit;
use Filament\Resources\Resource;
use App\Filament\Resources\VisitResource\Pages;
use Filament\Tables\Columns\Layout\Stack; // Pastikan import ini ada di atas

// DAFTAR USE HASIL SCAN (PASTI ADA)
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use App\Filament\Resources\VisitResource\RelationManagers;


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
                \Filament\Forms\Components\TextInput::make('kodecust')
                    ->label('Kode Cust'),
                \Filament\Forms\Components\TextInput::make('namacust')
                    ->label('Nama Cust')
                    ->required(),
                \Filament\Forms\Components\TextInput::make('kodeacum')
                    ->label('Kode Acumatica') // Judul yang muncul di atas kotak input
                    ->placeholder('Contoh: C0101398'), // Opsional: Teks bantuan di dalam kotak
                \Filament\Forms\Components\TextInput::make('namapic')
                    ->label('Nama PIC'),
                \Filament\Forms\Components\TextInput::make('jabatanpic')
                    ->label('Jabatan PIC'),
                \Filament\Forms\Components\DatePicker::make('visit_date')->required(),
                \Filament\Forms\Components\Textarea::make('notes')->columnSpanFull(),
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
                \Filament\Actions\ViewAction::make(),
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
            'view' => Pages\ViewVisit::route('/{record}'), // TAMBAHKAN INI
            'edit' => Pages\EditVisit::route('/{record}/edit'),
        ];
    }
    public static function getRelations(): array
    {
        return [
            // Pastikan alamatnya lengkap dan folder RelationManagers ada 's'-nya
            RelationManagers\ActivitiesRelationManager::class,
        ];
    }
    /*
    public static function infolist(\Filament\Schemas\Schema $infolist): \Filament\Schemas\Schema
    {
        return $infolist
            ->components([
                \Filament\Forms\Components\Placeholder::make('namacust')
                    ->label('Customer')
                    ->content(fn ($record): string => $record->namacust ?? '-'),
                
                \Filament\Forms\Components\Placeholder::make('visit_date')
                    ->label('Tanggal Kunjungan')
                    ->content(fn ($record): string => $record->visit_date ?? '-'),

                \Filament\Forms\Components\Placeholder::make('notes')
                    ->label('Catatan')
                    ->content(fn ($record): string => $record->notes ?? '-'),
            ]);
    }
    */
public static function infolist(\Filament\Schemas\Schema $infolist): \Filament\Schemas\Schema
{
    return $infolist
        ->components([
            // 1. SECTION DETAIL PIC (Readonly & Collapsible)
            \Filament\Schemas\Components\Section::make(function ($record) {
                $pic = $record->namapic ?? 'Tanpa Nama';
                $jabatan = $record->jabatanpic ? " ({$record->jabatanpic})" : "";
                return "PIC: " . $pic . $jabatan;
            })
            ->description('Klik untuk detail lebih lanjut')
            ->schema([
                \Filament\Schemas\Components\Grid::make(2)
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('kodesales')
                            ->label('Kode Sales')
                            ->content(fn ($record) => $record->kodesales ?? '-'),
                        
                        \Filament\Forms\Components\Placeholder::make('kodecust')
                            ->label('Kode Cust')
                            ->content(fn ($record) => $record->kodecust ?? '-'),
                        
                        \Filament\Forms\Components\Placeholder::make('notes')
                            ->label('Catatan')
                            ->content(fn ($record) => $record->notes ?? '-')
                            ->columnSpanFull(),
                    ]),
            ])
            ->collapsible()
            ->collapsed(), // Biarkan detail ini tertutup secara default

            // 2. TOMBOL AKSI (DI LUAR SECTION - SELALU MUNCUL)
            \Filament\Schemas\Components\Actions::make([
                \Filament\Actions\Action::make('add_activity')
                    ->label('Tambah Aktivitas Baru')
                    ->icon('heroicon-m-plus-circle')
                    ->color('primary')
                    ->button() // Membuatnya terlihat seperti tombol solid
                    ->extraAttributes(['class' => 'my-4']) // Memberi jarak atas-bawah
                    ->slideOver()
                    ->form([
                        \Filament\Forms\Components\Select::make('jenis')
                            ->options([
                                'Regular Visit' => 'Regular Visit',
                                'New Customer' => 'New Customer',
                                'New Product Development' => 'New Product Development',
                                'Existing Product Offering' => 'Existing Product Offering',
                                'Competitor Info' => 'Competitor Info',
                            ])->required(),
                        \Filament\Forms\Components\TextInput::make('kode_barang'),
                        \Filament\Forms\Components\Select::make('status')
                            ->options(['Failed' => 'Failed', 'Deal' => 'Deal', 'On Progress' => 'On Progress'])
                            ->required(),
                        \Filament\Forms\Components\Textarea::make('result')->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->activities()->create($data);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Aktivitas Berhasil Disimpan')
                            ->success()
                            ->send();
                        // TAMBAHKAN INI: Beritahu Livewire untuk refresh komponen di halaman tersebut
                        return redirect(request()->header('Referer'));
                    }),
            ]),
        ]);
}  
}