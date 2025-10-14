<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashAgreementResource\Pages;
use App\Models\CashAgreement;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class CashAgreementResource extends Resource
{
    protected static ?string $model = CashAgreement::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Administrasi Kelas';

    protected static ?string $navigationLabel = 'Perjanjian Kas Digital';

    protected static ?string $recordTitleAttribute = 'agreement_number';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->relationship('user', 'name')
                        ->label('Anggota')
                        ->required()
                        ->searchable(),
                    Forms\Components\TextInput::make('agreement_number')
                        ->label('Nomor Perjanjian')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(100),
                    Forms\Components\DatePicker::make('agreement_date')
                        ->label('Tanggal Perjanjian')
                        ->default(now())
                        ->required(),
                    Forms\Components\TextInput::make('amount')
                        ->label('Jumlah Pembayaran (Rp)')
                        ->numeric()
                        ->minValue(0)
                        ->required(),
                    Forms\Components\DatePicker::make('due_date')
                        ->label('Jatuh Tempo')
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'pending' => 'Menunggu Tanda Tangan',
                            'signed' => 'Sudah Ditandatangani',
                            'completed' => 'Selesai',
                        ])
                        ->default('draft')
                        ->required(),
                ]),
            Forms\Components\Textarea::make('notes')
                ->label('Catatan')
                ->columnSpanFull()
                ->rows(4),
            Forms\Components\FileUpload::make('signature_path')
                ->label('Tanda Tangan Digital')
                ->disk('public')
                ->directory('agreements/signatures')
                ->visibility('public')
                ->image()
                ->imageEditor()
                ->openable()
                ->downloadable()
                ->helperText('Unggah tanda tangan digital dalam format gambar.')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('agreement_number')
                ->label('Nomor')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('user.name')
                ->label('Anggota')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('amount')
                ->label('Jumlah')
                ->money('IDR', true),
            Tables\Columns\TextColumn::make('agreement_date')
                ->label('Tanggal')
                ->date(),
            Tables\Columns\TextColumn::make('due_date')
                ->label('Jatuh Tempo')
                ->date(),
            Tables\Columns\BadgeColumn::make('status')
                ->label('Status')
                ->colors([
                    'gray' => 'draft',
                    'warning' => 'pending',
                    'primary' => 'signed',
                    'success' => 'completed',
                ])
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'draft' => 'Draft',
                    'pending' => 'Menunggu Tanda Tangan',
                    'signed' => 'Sudah Ditandatangani',
                    'completed' => 'Selesai',
                    default => ucfirst($state),
                }),
            Tables\Columns\IconColumn::make('signature_path')
                ->label('Tanda Tangan')
                ->boolean()
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->sortable(),
            Tables\Columns\TextColumn::make('updated_at')
                ->label('Diperbarui')
                ->since()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
            ->defaultSort('agreement_date', 'desc')
            ->actions([
                Tables\Actions\Action::make('downloadPdf')
                    ->label('Unduh PDF')
                    ->icon('heroicon-o-arrow-down-on-square-stack')
                    ->action(function (CashAgreement $record) {
                        $pdf = Pdf::loadView('pdf.cash-agreement', [
                            'agreement' => $record,
                        ])->setPaper('a4');

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'perjanjian-kas-' . $record->agreement_number . '.pdf');
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCashAgreements::route('/'),
            'create' => Pages\CreateCashAgreement::route('/create'),
            'edit' => Pages\EditCashAgreement::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return static::hasManagementAccess();
    }

    public static function canCreate(): bool
    {
        return static::hasManagementAccess();
    }

    public static function canEdit($record): bool
    {
        return static::hasManagementAccess();
    }

    public static function canDelete($record): bool
    {
        return static::hasManagementAccess();
    }

    public static function canDeleteAny(): bool
    {
        return static::hasManagementAccess();
    }

    protected static function hasManagementAccess(): bool
    {
        return Auth::user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }
}
