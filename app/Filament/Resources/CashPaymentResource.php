<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashPaymentResource\Pages;
use App\Models\CashPayment;
use App\Support\Settings;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Resources\Resource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

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
                ->default(fn () => Settings::weeklyCashAmount())
                ->numeric()
                ->minValue(0)
                ->helperText(fn (): string => 'Nominal default Rp ' . number_format(Settings::weeklyCashAmount(), 0, ',', '.') . ' dan bisa disesuaikan sesuai pembayaran.'),
            Forms\Components\Select::make('payment_method')
                ->label('Metode Pembayaran')
                ->options([
                    'cash' => 'Tunai',
                    'transfer' => 'Transfer',
                ])
                ->default('cash')
                ->required(),
            Forms\Components\FileUpload::make('proof_path')
                ->label('Bukti Pembayaran')
                ->disk('public')
                ->directory('cash-payment-proofs')
                ->helperText('Opsional. Unggah bukti pembayaran jika diperlukan.')
                ->downloadable()
                ->openable()
                ->imagePreviewHeight('150')
                ->preserveFilenames()
                ->acceptedFileTypes(['image/*', 'application/pdf'])
                ->maxSize(5120),
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
            Tables\Columns\BadgeColumn::make('payment_method')
                ->label('Metode')
                ->colors([
                    'primary' => 'cash',
                    'warning' => 'transfer',
                ])
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'cash' => 'Tunai',
                    'transfer' => 'Transfer',
                    default => ucfirst($state),
                }),
            Tables\Columns\BadgeColumn::make('status')
                ->colors([
                    'warning' => 'pending',
                    'success' => 'confirmed',
                ]),
            Tables\Columns\ImageColumn::make('proof_path')
                ->label('Bukti')
                ->disk('public')
                ->square()
                ->size(48)
                ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Actions\Action::make('view_proof')
                    ->label('Lihat Bukti')
                    ->icon('heroicon-o-eye')
                    ->visible(fn (CashPayment $record): bool => (bool) $record->proof_path)
                    ->url(fn (CashPayment $record): ?string => $record->proof_path ? Storage::disk('public')->url($record->proof_path) : null)
                    ->openUrlInNewTab(),
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
        $data['amount'] = $data['amount'] ?? Settings::weeklyCashAmount();
        $data['date'] = $data['date'] ?? Carbon::today()->toDateString();
        $data['status'] = $data['status'] ?? 'confirmed';
        $data['payment_method'] = $data['payment_method'] ?? 'cash';

        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        $data['amount'] = $data['amount'] ?? Settings::weeklyCashAmount();
        $data['date'] = $data['date'] ?? Carbon::today()->toDateString();
        $data['payment_method'] = $data['payment_method'] ?? 'cash';

        return $data;
    }
}
