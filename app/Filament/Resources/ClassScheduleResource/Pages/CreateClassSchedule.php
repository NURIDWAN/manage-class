<?php

namespace App\Filament\Resources\ClassScheduleResource\Pages;

use App\Filament\Resources\ClassScheduleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClassSchedule extends CreateRecord
{
    protected static string $resource = ClassScheduleResource::class;

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Jadwal kuliah berhasil dibuat';
    }
}
