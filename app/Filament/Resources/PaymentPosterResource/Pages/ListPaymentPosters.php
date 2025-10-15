<?php

namespace App\Filament\Resources\PaymentPosterResource\Pages;

use App\Filament\Resources\PaymentPosterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentPosters extends ListRecords
{
    protected static string $resource = PaymentPosterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
