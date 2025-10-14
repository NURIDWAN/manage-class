<?php

namespace App\Filament\Resources\ManagementTaskResource\Pages;

use App\Filament\Resources\ManagementTaskResource;
use Filament\Resources\Pages\CreateRecord;

class CreateManagementTask extends CreateRecord
{
    protected static string $resource = ManagementTaskResource::class;

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Tugas kepengurusan berhasil dibuat';
    }
}
