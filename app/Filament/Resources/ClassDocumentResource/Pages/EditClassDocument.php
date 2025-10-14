<?php

namespace App\Filament\Resources\ClassDocumentResource\Pages;

use App\Filament\Resources\ClassDocumentResource;
use Filament\Resources\Pages\EditRecord;

class EditClassDocument extends EditRecord
{
    protected static string $resource = ClassDocumentResource::class;

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Dokumen berhasil diperbarui';
    }
}
