<?php

namespace App\Filament\Resources\CashAgreementResource\Pages;

use App\Filament\Resources\CashAgreementResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCashAgreement extends CreateRecord
{
    protected static string $resource = CashAgreementResource::class;

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Perjanjian kas berhasil dibuat';
    }
}
