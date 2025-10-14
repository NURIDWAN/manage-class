<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassDocumentResource\Pages;
use App\Models\ClassDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;

class ClassDocumentResource extends Resource
{
    protected static ?string $model = ClassDocument::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationGroup = 'Administrasi Kelas';

    protected static ?string $navigationLabel = 'Arsip Dokumen';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->label('Judul Dokumen')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('category')
                ->label('Kategori')
                ->maxLength(100)
                ->helperText('Misal: Notulen, Daftar Hadir, Surat Izin.'),
            Forms\Components\Textarea::make('description')
                ->label('Deskripsi')
                ->rows(4),
            Forms\Components\FileUpload::make('file_path')
                ->label('Berkas')
                ->required()
                ->disk('public')
                ->directory('documents')
                ->visibility('public')
                ->preserveFilenames()
                ->helperText('Format yang didukung: PDF, DOCX, XLSX, gambar.')
                ->acceptedFileTypes([
                    'application/pdf',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-excel',
                    'image/jpeg',
                    'image/png',
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('title')
                ->label('Judul')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('category')
                ->label('Kategori')
                ->badge()
                ->color('primary')
                ->placeholder('-'),
            Tables\Columns\TextColumn::make('uploader.name')
                ->label('Diunggah Oleh')
                ->placeholder('-')
                ->sortable(),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Tanggal Unggah')
                ->dateTime('d M Y H:i')
                ->sortable(),
        ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Unduh')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (ClassDocument $record) {
                        if (! $record->file_path || ! Storage::disk('public')->exists($record->file_path)) {
                            Notification::make()
                                ->title('Berkas tidak ditemukan')
                                ->danger()
                                ->send();

                            return;
                        }

                        $stream = Storage::disk('public')->readStream($record->file_path);

                        if (! $stream) {
                            Notification::make()
                                ->title('Gagal mengunduh berkas')
                                ->danger()
                                ->send();

                            return;
                        }

                        return response()->streamDownload(function () use ($stream) {
                            fpassthru($stream);
                            fclose($stream);
                        }, $record->title . '.' . pathinfo($record->file_path, PATHINFO_EXTENSION));
                    })
                    ->visible(fn (ClassDocument $record) => filled($record->file_path)),
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
            'index' => Pages\ListClassDocuments::route('/'),
            'create' => Pages\CreateClassDocument::route('/create'),
            'edit' => Pages\EditClassDocument::route('/{record}/edit'),
        ];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uploaded_by'] = Auth::id();

        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        if (! array_key_exists('uploaded_by', $data) || ! $data['uploaded_by']) {
            $data['uploaded_by'] = Auth::id();
        }

        return $data;
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
