<?php

namespace App\Filament\Resources;

use App\Models\Visit;
use App\Filament\Resources\VisitResource\Pages;
use App\Filament\Resources\VisitResource\RelationManagers;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Forms\Components\{TextInput, Textarea, DatePicker, Select, Placeholder};
use Filament\Schemas\Components\{Section, Grid, Actions};
use Filament\Actions\Action;
use Filament\Schemas\Components\Utilities\Get;

class VisitResource extends Resource
{
    protected static ?string $model = Visit::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-clipboard-document-check'; //jika pakai ?string ternyata error
    protected static ?string $navigationLabel = 'Kunjungan Sales';

    public static function form(Schema $form): Schema
    {
        return $form->components([
            TextInput::make('kodesales')
                ->label('Kode Sales')
                ->default('SDA03') // ->default(fn () => auth()->user()->kode_sales_user)
                ->disabled()
                ->dehydrated(),
            TextInput::make('kodecust')
                ->label('Kode Cust')
                ->reactive()
                ->disabled(fn (Get $get) => filled($get('kodeacum')))
                ->dehydrated()
                ->extraAttributes(fn (Get $get) => [
                    'class' => filled($get('kodeacum')) 
                        ? 'bg-gray-200 dark:bg-gray-800 cursor-not-allowed rounded-lg' 
                        : '',
                    'style' => filled($get('kodeacum'))
                        ? 'pointer-events: all !important;' // Memaksa mouse bisa berinteraksi untuk memicu kursor
                        : '',
                ])
                ->live(),
            
            //TextInput::make('kodeacum')->label('Kode Acumatica')->placeholder('C0101398'),
            Select::make('kodeacum')
                ->label('Kode Acumatica')
                    ->required()
                    ->relationship('customer', 'customerid')
                    //->relationship('customer')
                    ->searchable()
                    // 1. Saat Mengetik: Muncul ID - NAMA
                    ->getSearchResultsUsing(fn (string $search): array => 
                        \App\Models\Customer::where('customername', 'like', "%{$search}%")
                            ->orWhere('customerid', 'like', "%{$search}%")
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
                        $customer = \App\Models\Customer::where('customerid', $state)->first();
                        if ($customer) {
                            $set('namacust', $customer->customername); 
                            $set('namapic', $customer->admar); 
                        }
                    })
                    ->nullable()
                    ->live(),
            //TextInput::make('namacust')->label('Nama Cust')->required(),
            TextInput::make('namacust')
                ->label('Nama Customer')
                ->disabled(fn (Get $get) => filled($get('kodeacum')) || filled($get('kodecust')))
                ->required(fn (Get $get) => blank($get('kodeacum')) && blank($get('kodecust')))
                ->extraInputAttributes(fn (Get $get) => [
                    'style' => filled($get('kodeacum')) 
                    ? 'color: #1e40af; font-weight: bold; font-style: italic;' // Biru Tua, Bold, Italic
                    : 'color: black;',
                ])
                ->dehydrated()
                // Tambahkan ini agar tidak monoton:
                ->placeholder('Ketik nama jika customer baru...')
                ->hint(fn (Get $get) => filled($get('kodeacum')) ? 'Data dari Master' : null)
                ->hintIcon(fn (Get $get) => filled($get('kodeacum')) ? 'heroicon-m-check-badge' : null)
                ->hintColor('success'), // Warna hijau biar kelihatan "Resmi"
            TextInput::make('namapic')->label('Nama PIC'),
            TextInput::make('jabatanpic')->label('Jabatan PIC'),
            DatePicker::make('visit_date')
                ->required()
                ->displayFormat('d-m-Y')
                ->default(now()->format('d-m-Y')),
            Textarea::make('notes')->columnSpanFull(),
        ]);
    }

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
                            $jabatanText = $r?->jabatanpic ?? '';
                            $jabatan = ($jabatanText !== '') ? " ({$jabatanText})" : "";
                            return "PIC: " . $nama . $jabatan;;
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
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
            ]);
    }

    public static function infolist(Schema $infolist): Schema
    {
        return $infolist->components([
            Section::make(fn ($record) => "PIC: " . ($record->namapic ?? 'Tanpa Nama'))
                ->description('Klik untuk detail lebih lanjut')
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
                ])
                ->collapsible()
                ->collapsed(),

            Actions::make([
                Action::make('add_activity')
                    ->label('Tambah Aktivitas Baru')
                    ->icon('heroicon-m-plus-circle')
                    ->color('primary')
                    ->button()
                    ->extraAttributes(['class' => 'my-4'])
                    ->slideOver()
                    ->form([
                        \Filament\Forms\Components\Select::make('jenis')
                            ->options(['Regular Visit' => 'Regular Visit', 'New Customer' => 'New Customer'])
                            ->required(),
                        TextInput::make('kode_barang'),
                        \Filament\Forms\Components\Select::make('status')
                            ->options(['Deal' => 'Deal', 'On Progress' => 'On Progress'])
                            ->required(),
                        Textarea::make('result')->required(),
                    ])
                    ->action(function ($record, array $data, $livewire) {
                        $record->activities()->create($data);
                        \Filament\Notifications\Notification::make()->title('Sukses')->success()->send();
                        $livewire->js('window.location.reload()');
                    }),
            ]),
        ]);
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