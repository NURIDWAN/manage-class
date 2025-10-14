<?php

namespace App\Filament\Resources\ClassDocumentResource\Pages;

use App\Filament\Resources\ClassDocumentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClassDocument extends CreateRecord
{
    protected static string $resource = ClassDocumentResource::class;

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Dokumen berhasil diunggah';
    }
}
