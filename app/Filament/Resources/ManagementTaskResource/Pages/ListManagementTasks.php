<?php

namespace App\Filament\Resources\ManagementTaskResource\Pages;

use App\Filament\Resources\ManagementTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListManagementTasks extends ListRecords
{
    protected static string $resource = ManagementTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah Tugas'),
        ];
    }
}
