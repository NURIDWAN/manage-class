<?php

namespace App\Filament\Resources\ClassScheduleResource\Pages;

use App\Filament\Resources\ClassScheduleResource;
use Filament\Resources\Pages\EditRecord;

class EditClassSchedule extends EditRecord
{
    protected static string $resource = ClassScheduleResource::class;

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Jadwal kuliah berhasil diperbarui';
    }
}
