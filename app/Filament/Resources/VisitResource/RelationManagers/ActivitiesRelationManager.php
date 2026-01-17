<?php

namespace App\Filament\Resources\VisitResource\RelationManagers;

use App\Filament\Resources\VisitResource;

use Filament\Tables\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\Layout\Stack; // Pastikan import ini ada di atas
use Filament\Schemas\Schema; // ALAMAT BARU SESUAI ERROR

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';
    protected static ?string $title = 'Aktivitas';

    protected static ?string $relatedResource = VisitResource::class;

    // Gunakan \Filament\Schemas\Schema sesuai permintaan error-nya
    //public function form(\Filament\Schemas\Schema $form): \Filament\Schemas\Schema
    public function form(Schema $schema): Schema
    {
        return $schema
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
            //->contentGrid([
            //    'default' => 1,
            //    'md' => 2,
            //])
            ->headerActions([
                // WAJIB pakai Tables\Actions agar muncul di atas tabel relasi
                \Filament\Actions\CreateAction::make()
                    ->label('New Activity')
                    ->icon('heroicon-m-plus-circle')
                    ->modalHeading('Buat Aktivitas Kunjungan')
                    ->model(\App\Models\Activity::class) 
                    ->slideOver()
                    ->visible(true), // PAKSA VISIBLE
            ])
            ->actions([
                \Filament\Actions\EditAction::make()->visible(true),
                \Filament\Actions\DeleteAction::make()->visible(true),
            ]);
    }

    // --- KUNCI JAWABAN AGAR TOMBOL MUNCUL DI HALAMAN VIEW ---
    
    protected function canCreate(): bool
    {
        // Paksa agar selalu boleh membuat aktivitas baru di halaman apapun
        return true;
    }

    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        // Pastikan Relation Manager ini diizinkan tampil di halaman ViewRecord
        return true;
    }

    // Tambahkan ini juga agar tombol "Edit" dan "Delete" muncul di halaman View
    protected function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return true;
    }

    protected function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return true;
    }
}
