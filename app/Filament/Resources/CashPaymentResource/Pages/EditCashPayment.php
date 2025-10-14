<?php

namespace App\Filament\Resources\CashPaymentResource\Pages;

use App\Filament\Resources\CashPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCashPayment extends EditRecord
{
    protected static string $resource = CashPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
