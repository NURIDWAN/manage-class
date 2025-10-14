<?php

namespace App\Filament\Resources\CashPaymentResource\Pages;

use App\Filament\Resources\CashPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCashPayments extends ListRecords
{
    protected static string $resource = CashPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
