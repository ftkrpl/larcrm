<?php

namespace App\Filament\Resources\VisitResource\RelationManagers;

use App\Filament\Resources\VisitResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Forms\Components\{TextInput, Select, Textarea};
use Filament\Actions\{CreateAction, EditAction, DeleteAction};

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';
    protected static ?string $title = 'Daftar Aktivitasss';
    protected static ?string $relatedResource = VisitResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('jenis')
                ->options([
                    'Regular Visit' => 'Regular Visit',
                    'New Customer' => 'New Customer',
                    'New Product Development' => 'New Product Development',
                    'Existing Product Offering' => 'Existing Product Offering',
                    'Competitor Info' => 'Competitor Info',
                ])->required(),
            //TextInput::make('kode_barang')->label('Kode Barang'),
            Select::make('kode_barang') // sesuaikan nama kolom di tabel visit_activities
                ->label('Kode Barang / Stock Item')
                ->required()
                ->searchable()
                ->getSearchResultsUsing(fn (string $search): array => 
                    \App\Models\Item::where('description', 'like', "%{$search}%") // Ganti \App\Models\Item sesuai model barang Anda
                        ->orWhere('inventoryid', 'like', "%{$search}%")
                        ->limit(10)
                        ->get()
                        ->mapWithKeys(fn ($item) => [
                            $item->inventoryid => "{$item->inventoryid} - {$item->description}"
                        ])
                        ->toArray()
                )
                ->getOptionLabelUsing(fn ($value) => (string) $value)
                ->reactive()
                ->afterStateUpdated(function ($state, $set) {
                    // Ambil data barang untuk mengisi nama barang secara otomatis
                    $item = \App\Models\Item::where('inventoryid', $state)->first();
                    if ($item) {
                        $set('nama_barang', $item->description);
                        $set('kelompok_barang', $item->kelompok);
                    }
                })
                ->live(),
            TextInput::make('nama_barang')->label('Nama Barang'),
            TextInput::make('kelompok_barang')->label('Kelompok'),
            Select::make('sample')->options(['Yes' => 'Yes', 'No' => 'No'])->required(),
            Select::make('status')->options(['Failed' => 'Failed', 'Deal' => 'Deal', 'On Progress' => 'On Progress'])->required(),
            Textarea::make('result')->label('Hasil')->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    TextColumn::make('jenis')->badge()->color('info'),
                    TextColumn::make('kode_barang')->formatStateUsing(fn($s) => "Barang: " . ($s ?? '-')),
                    TextColumn::make('status')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'Deal' => 'success',
                            'Failed' => 'danger',
                            default => 'warning',
                        }),
                    TextColumn::make('result')->limit(50)->color('gray'),
                ])
            ])
            ->recordAction('edit')
            ->contentGrid(['default' => 1, 'md' => 2])
            ->headerActions([
                CreateAction::make()->label('New Activityyy')->icon('heroicon-m-plus-circle')->slideOver(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    protected function canCreate(): bool { return true; }
    protected function canEdit(\Illuminate\Database\Eloquent\Model $record): bool { return true; }
    protected function canDelete(\Illuminate\Database\Eloquent\Model $record): bool { return true; }
    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        // Izinkan tampil di halaman View DAN halaman Edit
        return in_array($pageClass, [
            \App\Filament\Resources\VisitResource\Pages\ViewVisit::class,
            \App\Filament\Resources\VisitResource\Pages\EditVisit::class, // Tambahkan baris ini
        ]);
    }
}