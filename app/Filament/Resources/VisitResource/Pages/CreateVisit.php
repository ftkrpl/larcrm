<?php

namespace App\Filament\Resources\VisitResource\Pages;

use App\Filament\Resources\VisitResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVisit extends CreateRecord
{
    protected static string $resource = VisitResource::class;

    // JURUS REDIRECT SETELAH CREATE
    protected function getRedirectUrl(): string
    {
        // Mengarahkan user langsung ke halaman 'view' dari data yang baru dibuat
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
