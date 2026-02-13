<?php

namespace App\Filament\Resources;

use App\Models\Visit;
use App\Models\Item;
use App\Models\Customer;
use App\Filament\Resources\VisitResource\Pages;
use App\Filament\Resources\VisitResource\RelationManagers;
use Filament\Resources\Resource;

// Tambahkan ini di deretan use paling atas
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

// --- URUSAN FORM & TABLE ---
use Filament\Forms\Form;
//use Filament\Forms\Get;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\{TextInput, Textarea, DatePicker, Select};
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Actions\Action; // Tombol Pusat (Penting untuk v4)

// --- URUSAN INFOLIST / VIEW (PINTU UTAMA SCHEMAS) ---
use Filament\Schemas\Schema;
use Filament\Schemas\Components\{Section, Grid, Actions};
//use Filament\Schemas\Components\Utilities\Placeholder;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\{RepeatableEntry, TextEntry};

class VisitResource extends Resource
{
    protected static ?string $model = Visit::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Kunjungan Sales';

    /**
     * FORM KUNJUNGAN UTAMA
     */
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('kodesales')
                ->label('Kode Sales')
                ->default('SDA03')
                ->disabled()
                ->dehydrated(),

            Select::make('kodecust')
                ->label('Kode Cust (NOO)')
                ->disabled(fn (Get $get) => filled($get('customerid')))
                ->required()
                ->relationship('customer', 'customerid')
                ->searchable()
                ->getSearchResultsUsing(fn (string $search): array => 
                    Customer::where('source', 'NOO')
                        ->where(function ($query) use ($search) {
                            $query->where('customername', 'like', "%{$search}%")
                                  ->orWhere('customerid', 'like', "%{$search}%");
                        })
                        ->limit(10)
                        ->get()
                        ->mapWithKeys(fn ($item) => [
                            $item->customerid => "{$item->kodecust} - {$item->customername}"
                        ])
                        ->toArray()
                )
                ->getOptionLabelUsing(fn ($value) => (string) $value)
                ->reactive() 
                ->afterStateUpdated(function ($state, $set) {
                    $customer = Customer::where('customerid', $state)->first();
                    if ($customer) {
                        $set('namacust', $customer->customername); 
                        $set('namapic', $customer->admar); 
                    }
                })
                ->nullable()
                ->live(),

            Select::make('kodeacum')
                ->label('Kode Acumatica')
                ->disabled(fn (Get $get) => filled($get('kodecust')))
                ->required()
                ->relationship('customer', 'customerid')
                ->searchable()
                ->getSearchResultsUsing(fn (string $search): array => 
                    Customer::where('source', 'Acumatica')
                        ->where(function ($query) use ($search) {
                            $query->where('customername', 'like', "%{$search}%")
                                  ->orWhere('customerid', 'like', "%{$search}%");
                        })
                        ->limit(10)
                        ->get()
                        ->mapWithKeys(fn ($item) => [
                            $item->customerid => "{$item->customerid} - {$item->customername}"
                        ])
                        ->toArray()
                )
                ->getOptionLabelUsing(fn ($value) => (string) $value)
                ->reactive() 
                ->afterStateUpdated(function ($state, $set) {
                    $customer = Customer::where('customerid', $state)->first();
                    if ($customer) {
                        $set('namacust', $customer->customername); 
                        $set('namapic', $customer->admar); 
                    }
                })
                ->nullable()
                ->live(),

            TextInput::make('namacust')
                ->label('Nama Customer')
                ->disabled(fn (Get $get) => filled($get('kodeacum')) || filled($get('kodecust')))
                ->required(fn (Get $get) => blank($get('kodeacum')) && blank($get('kodecust')))
                ->extraInputAttributes(fn (Get $get) => [
                    'style' => filled($get('kodeacum')) 
                        ? 'color: #1e40af; font-weight: bold; font-style: italic;' 
                        : 'color: black;',
                ])
                ->dehydrated()
                ->placeholder('Ketik nama jika customer baru...')
                ->hint(fn (Get $get) => filled($get('kodeacum')) ? 'Data dari Master' : null)
                ->hintIcon(fn (Get $get) => filled($get('kodeacum')) ? 'heroicon-m-check-badge' : null)
                ->hintColor('success'),

            TextInput::make('namapic')->label('Nama PIC'),
            TextInput::make('jabatanpic')->label('Jabatan PIC'),
            DatePicker::make('visit_date')
                ->required()
                ->displayFormat('d-m-Y')
                ->default(now()),
            Textarea::make('notes')->columnSpanFull(),
        ]);
    }

    /**
     * TABLE LIST KUNJUNGAN
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    TextColumn::make('namacust')
                        ->weight('bold')
                        ->size('lg')
                        ->searchable(),
                    TextColumn::make('kodesales')
                        ->formatStateUsing(fn ($state) => "Sales: " . $state)
                        ->color('gray'),
                    TextColumn::make('namapic')
                        ->label('Nama PIC')
                        ->formatStateUsing(function ($state, $r) {
                            $nama = $state ?? 'Tanpa Nama';
                            //$jabatan = $r->jabatanpic ? " ({$r->jabatanpic})" : "";
                            $jabatan = ($r?->jabatanpic) ? " ({$r->jabatanpic})" : "";
                            return "PIC: " . $nama . $jabatan;
                        })
                        ->icon('heroicon-m-user'),
                    TextColumn::make('visit_date')
                        ->date('d M Y')
                        ->color('primary')
                        ->icon('heroicon-m-calendar'),
                ]),
            ])
            ->contentGrid(['default' => 1, 'md' => 2, 'xl' => 3])
            ->actions([
                // Ganti \Filament\Tables\Actions\... menjadi \Filament\Actions\...
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(), // Kalau mau nambah tombol hapus sekalian
            ]);
    }

    /**
     * INFOLIST DETAIL KUNJUNGAN
     */
    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            // 1. Header Detail PIC
            Section::make(fn ($record) => "PIC: " . ($record->namapic ?? 'Tanpa Nama'))
                ->description('Klik untuk detail lebih lanjut')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Grid::make(2)->schema([
                        Placeholder::make('kodesales')
                            ->content(fn ($r) => $r->kodesales ?? '-'),
                        Placeholder::make('kodecust')
                            ->content(fn ($r) => $r->kodecust ?? '-'),
                        Placeholder::make('notes')
                            ->content(fn ($r) => $r->notes ?? '-')
                            ->columnSpanFull(),
                    ]),
                ]),

            // 2. Tombol Tambah Aktivitas
            Actions::make([
                Action::make('add_activity')
                    ->label('Tambah Aktivitas Baru')
                    ->icon('heroicon-m-plus-circle')
                    ->color('primary')
                    ->button()
                    ->slideOver()
                    ->form(static::getVisitActivityForm())
                    ->action(function ($record, array $data, $livewire) {
                        $record->activities()->create($data);
                        \Filament\Notifications\Notification::make()->title('Sukses')->success()->send();
                        $livewire->js('window.location.reload()');
                    }),
            ]),

            // 3. Daftar Aktivitas
            RepeatableEntry::make('activities')
                ->label('Daftar Aktivitaz')
                ->schema([
                    TextEntry::make('jenis')->badge()->color('info'),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'Deal' => 'success',
                            'Failed' => 'danger',
                            default => 'warning',
                        }),
                    
                    // Tombol Edit/Hapus diletakkan di dalam schema (v4 style)
                    Actions::make([
                        Action::make('edit_activity')
                            ->label('Edit')
                            ->icon('heroicon-m-pencil-square')
                            ->color('warning')
                            ->slideOver()
                            ->form(static::getVisitActivityForm())
                            ->fillForm(fn ($record) => $record->toArray())
                            ->action(function ($record, array $data, $livewire) {
                                $record->update($data);
                                \Filament\Notifications\Notification::make()->title('Updated')->success()->send();
                                $livewire->js('window.location.reload()');
                            }),

                        Action::make('delete_activity')
                            ->label('Hapus')
                            ->icon('heroicon-m-trash')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->action(function ($record, $livewire) {
                                $record->delete();
                                $livewire->js('window.location.reload()');
                            }),
                    ])->alignRight(),
                ])
                ->grid(['default' => 1, 'md' => 2]),
        ]);
    }

    /**
     * FORM HELPER UNTUK AKTIVITAS
     */
    public static function getVisitActivityForm(): array
    {
        return [
            \Filament\Forms\Components\Select::make('jenis')
                ->options(config('plenum.jenis_visit'))
                ->required()
                ->live(),

            \Filament\Forms\Components\Select::make('kode_barang')
                ->label('Cari Barang / Stock Item')
                ->required()
                ->searchable()
                ->getSearchResultsUsing(fn (string $search): array => 
                    Item::where('description', 'like', "%{$search}%")
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
                    $item = Item::where('inventoryid', $state)->first();
                    if ($item) {
                        $set('nama_barang', $item->description);
                        $set('kelompok_barang', $item->kelompok);
                    }
                })
                ->live()
                ->hidden(fn (Get $get) => $get('jenis') === 'Regular Visit'),

            \Filament\Forms\Components\TextInput::make('nama_barang')
                ->label('Nama Barang')
                ->dehydrated() 
                ->readOnly()
                ->hidden(fn (Get $get) => $get('jenis') === 'Regular Visit'),

            \Filament\Forms\Components\Select::make('kelompok_barang')
                ->label('Kelompok')
                ->options(fn () => Item::query()->distinct()->whereNotNull('kelompok')->pluck('kelompok', 'kelompok'))
                ->searchable()
                ->disabled(fn (Get $get) => filled($get('kode_barang')))
                ->dehydrated()
                ->hidden(fn (Get $get) => $get('jenis') === 'Regular Visit'),

            \Filament\Forms\Components\Select::make('status')
                ->options(config('plenum.status_visit'))
                ->required()
                ->hidden(fn (Get $get) => $get('jenis') === 'Regular Visit'),

            \Filament\Forms\Components\Select::make('potential')
                ->options(config('plenum.potential_visit'))
                ->hidden(fn (Get $get) => $get('jenis') === 'Regular Visit'),

            \Filament\Forms\Components\Select::make('sample')
                ->options(config('plenum.sample_visit'))
                ->hidden(fn (Get $get) => $get('jenis') === 'Regular Visit'),

            \Filament\Forms\Components\Textarea::make('result')
                ->label('Hasil/Catatan')
                ->required(),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVisits::route('/'),
            'create' => Pages\CreateVisit::route('/create'),
            'view' => Pages\ViewVisit::route('/{record}'),
            'edit' => Pages\EditVisit::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [RelationManagers\ActivitiesRelationManager::class];
    }
}