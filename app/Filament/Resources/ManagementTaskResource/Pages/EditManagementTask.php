<?php

namespace App\Filament\Resources\ManagementTaskResource\Pages;

use App\Filament\Resources\ManagementTaskResource;
use Filament\Resources\Pages\EditRecord;

class EditManagementTask extends EditRecord
{
    protected static string $resource = ManagementTaskResource::class;

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Tugas kepengurusan berhasil diperbarui';
    }
}
