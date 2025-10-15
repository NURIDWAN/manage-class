<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentPosterResource\Pages;
use App\Models\PaymentPoster;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PaymentPosterResource extends Resource
{
    protected static ?string $model = PaymentPoster::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Pengaturan Sistem';

    protected static ?string $navigationLabel = 'Poster Pembayaran';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->label('Judul Poster')
                ->required()
                ->maxLength(255),
            Forms\Components\FileUpload::make('file_path')
                ->label('Berkas Poster')
                ->required(fn (?PaymentPoster $record) => ! $record?->file_path)
                ->disk('public')
                ->directory('cash-payment-posters')
                ->acceptedFileTypes(['image/*', 'application/pdf'])
                ->maxSize(5120)
                ->downloadable()
                ->openable()
                ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                    $extension = $file->getClientOriginalExtension();
                    $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                    $timestamp = now()->format('YmdHis');

                    return trim("poster-{$timestamp}-{$name}", '-') . ($extension ? ".{$extension}" : '');
                }),
            Forms\Components\Toggle::make('is_active')
                ->label('Aktif')
                ->default(true)
                ->helperText('Poster aktif akan ditampilkan kepada pengguna.'),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->since(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif')
                    ->placeholder('Semua'),
            ])
            ->actions([
                Tables\Actions\Action::make('preview')
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->visible(fn (PaymentPoster $record): bool => ! empty($record->file_path))
                    ->url(function (PaymentPoster $record): ?string {
                        if (! $record->file_path) {
                            return null;
                        }

                        $disk = Storage::disk('public');

                        return $disk->exists($record->file_path)
                            ? $disk->url($record->file_path)
                            : null;
                    })
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
            'index' => Pages\ListPaymentPosters::route('/'),
            'create' => Pages\CreatePaymentPoster::route('/create'),
            'edit' => Pages\EditPaymentPoster::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest();
    }
}
