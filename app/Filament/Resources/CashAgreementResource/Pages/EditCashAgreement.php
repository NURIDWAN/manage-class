<?php

namespace App\Filament\Resources\CashAgreementResource\Pages;

use App\Filament\Resources\CashAgreementResource;
use Filament\Resources\Pages\EditRecord;

class EditCashAgreement extends EditRecord
{
    protected static string $resource = CashAgreementResource::class;

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Perjanjian kas berhasil diperbarui';
    }
}
