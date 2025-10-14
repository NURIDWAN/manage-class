<?php

namespace App\Filament\Resources\CashAgreementResource\Pages;

use App\Filament\Resources\CashAgreementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCashAgreements extends ListRecords
{
    protected static string $resource = CashAgreementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Buat Perjanjian'),
        ];
    }
}
