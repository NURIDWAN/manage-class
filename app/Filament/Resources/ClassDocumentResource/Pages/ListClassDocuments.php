<?php

namespace App\Filament\Resources\ClassDocumentResource\Pages;

use App\Filament\Resources\ClassDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClassDocuments extends ListRecords
{
    protected static string $resource = ClassDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Unggah Dokumen'),
        ];
    }
}
