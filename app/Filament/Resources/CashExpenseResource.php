<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashExpenseResource\Pages;
use App\Models\CashExpense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class CashExpenseResource extends Resource
{
    protected static ?string $model = CashExpense::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-down';

    protected static ?string $navigationLabel = 'Pengeluaran Kas';

    protected static ?string $navigationGroup = 'Keuangan Kelas';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('description')
                ->label('Keterangan')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('amount')
                ->label('Jumlah (Rp)')
                ->numeric()
                ->minValue(0)
                ->required(),
            Forms\Components\DatePicker::make('date')
                ->label('Tanggal')
                ->default(Carbon::today())
                ->required(),
            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'pending' => 'Menunggu',
                    'confirmed' => 'Dikonfirmasi',
                ])
                ->default('confirmed')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('description')
                ->label('Keterangan')
                ->searchable()
                ->wrap(),
            Tables\Columns\TextColumn::make('amount')
                ->label('Jumlah')
                ->money('IDR', true)
                ->sortable(),
            Tables\Columns\TextColumn::make('date')
                ->label('Tanggal')
                ->date()
                ->sortable(),
            Tables\Columns\BadgeColumn::make('status')
                ->label('Status')
                ->colors([
                    'warning' => 'pending',
                    'danger' => 'pending',
                    'success' => 'confirmed',
                ])
                ->formatStateUsing(fn (string $state): string => $state === 'pending' ? 'Menunggu' : 'Dikonfirmasi'),
            Tables\Columns\TextColumn::make('recorder.name')
                ->label('Dicatat oleh')
                ->getStateUsing(fn (CashExpense $record): string => $record->recorder?->name ?? 'Tidak diketahui')
                ->toggleable(isToggledHiddenByDefault: true),
        ])
            ->defaultSort('date', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('confirm')
                    ->label('Konfirmasi')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->visible(fn (CashExpense $record): bool => $record->status !== 'confirmed')
                    ->action(function (CashExpense $record): void {
                        $record->update([
                            'status' => 'confirmed',
                            'date' => $record->date ?? Carbon::today()->toDateString(),
                        ]);

                        Notification::make()
                            ->title('Pengeluaran kas dikonfirmasi')
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
            'index' => Pages\ListCashExpenses::route('/'),
            'create' => Pages\CreateCashExpense::route('/create'),
            'edit' => Pages\EditCashExpense::route('/{record}/edit'),
        ];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['recorded_by'] = Auth::id();
        $data['date'] = $data['date'] ?? Carbon::today()->toDateString();

        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        $data['date'] = $data['date'] ?? Carbon::today()->toDateString();

        return $data;
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->hasAnyRole(['admin', 'super_admin']) ?? false;
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit($record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete($record): bool
    {
        return static::canViewAny();
    }

    public static function canDeleteAny(): bool
    {
        return static::canViewAny();
    }
}

