<?php

namespace App\Filament\Resources\VisitResource\Pages;

use App\Filament\Resources\VisitResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use App\Filament\Resources\ActivityResource; // IMPORT RESOURCE ACTIVITY

class ViewVisit extends ViewRecord
{
    protected static string $resource = VisitResource::class;

    // Menjadikan Nama Customer dan Tanggal sebagai Judul Halaman
    public function getTitle(): string 
    {
        return $this->record->namacust;
    }

    // Menghapus Subtitle jika tidak diperlukan (Opsional)
    public function getSubheading(): ?string
    {
        return \Carbon\Carbon::parse($this->record->visit_date)->format('d M Y');
    }
    protected function getHeaderActions(): array
    {
        
        return [

        ];
    }
    /*
    // Tambahkan ini jika belum ada
    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
    */
}