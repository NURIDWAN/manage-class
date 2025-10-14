<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashPaymentResource\Pages;
use App\Models\CashPayment;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Resources\Resource;
use Illuminate\Support\Carbon;

class CashPaymentResource extends Resource
{
    protected static ?string $model = CashPayment::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Pembayaran Kas';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')
                ->required()
                ->searchable()
                ->label('Nama Mahasiswa'),
            Forms\Components\TextInput::make('amount')
                ->label('Jumlah (Rp)')
                ->default(10000)
                ->numeric()
                ->disabled()
                ->dehydrated(true)
                
                ->helperText('Iuran kas mingguan akan otomatis bernilai Rp 10.000.'),
            Forms\Components\Placeholder::make('auto_info')
                ->label('Konfirmasi Otomatis')
                ->content('Tanggal pembayaran dan status akan otomatis diisi sebagai hari ini dan terkonsfirmasi.'),
            Forms\Components\Hidden::make('date')
                ->default(fn (): string => Carbon::today()->toDateString()),
            Forms\Components\Hidden::make('status')
                ->default('confirmed'),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('user.name')->label('Mahasiswa'),
            Tables\Columns\TextColumn::make('amount')->money('IDR', true)->label('Jumlah'),
            Tables\Columns\TextColumn::make('date')->date(),
            Tables\Columns\BadgeColumn::make('status')
                ->colors([
                    'warning' => 'pending',
                    'success' => 'confirmed',
                ]),
        ])
            ->defaultSort('date', 'desc')
            ->actions([
                Tables\Actions\Action::make('confirm')
                    ->label('Konfirmasi')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->visible(fn (CashPayment $record): bool => $record->status !== 'confirmed')
                    ->action(function (CashPayment $record): void {
                        $record->update([
                            'status' => 'confirmed',
                            'date' => $record->date ?? Carbon::today()->toDateString(),
                        ]);

                        Notification::make()
                            ->title('Pembayaran kas dikonfirmasi')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCashPayments::route('/'),
            'create' => Pages\CreateCashPayment::route('/create'),
            'edit' => Pages\EditCashPayment::route('/{record}/edit'),
        ];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['amount'] = $data['amount'] ?? 10000;
        $data['date'] = $data['date'] ?? Carbon::today()->toDateString();
        $data['status'] = $data['status'] ?? 'confirmed';

        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        $data['amount'] = $data['amount'] ?? 10000;
        $data['date'] = $data['date'] ?? Carbon::today()->toDateString();

        return $data;
    }
}
