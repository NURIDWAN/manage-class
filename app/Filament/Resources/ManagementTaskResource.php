<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ManagementTaskResource\Pages;
use App\Models\ManagementTask;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ManagementTaskResource extends Resource
{
    protected static ?string $model = ManagementTask::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Administrasi Kelas';

    protected static ?string $navigationLabel = 'Tugas Kepengurusan';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Nama Tugas')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('assigned_to')
                        ->label('Penanggung Jawab')
                        ->relationship('assignee', 'name')
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'pending' => 'Belum Dikerjakan',
                            'in_progress' => 'Sedang Berjalan',
                            'completed' => 'Selesai',
                        ])
                        ->default('pending')
                        ->required(),
                    Forms\Components\DatePicker::make('due_date')
                        ->label('Tanggal Selesai')
                        ->minDate(now())
                        ->native(false),
                ]),
            Forms\Components\Textarea::make('description')
                ->label('Deskripsi / Jobdesk')
                ->rows(5)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('title')
                ->label('Tugas')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('assignee.name')
                ->label('Penanggung Jawab')
                ->placeholder('-')
                ->sortable(),
            Tables\Columns\BadgeColumn::make('status')
                ->label('Status')
                ->colors([
                    'warning' => 'pending',
                    'primary' => 'in_progress',
                    'success' => 'completed',
                ])
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'pending' => 'Belum Dikerjakan',
                    'in_progress' => 'Sedang Berjalan',
                    'completed' => 'Selesai',
                    default => ucfirst($state),
                }),
            Tables\Columns\TextColumn::make('due_date')
                ->label('Tenggat')
                ->date()
                ->placeholder('-'),
            Tables\Columns\TextColumn::make('updated_at')
                ->label('Diperbarui')
                ->since()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
            ->defaultSort('status')
            ->actions([
                Tables\Actions\Action::make('markCompleted')
                    ->label('Tandai Selesai')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->visible(fn (ManagementTask $record) => $record->status !== 'completed')
                    ->action(function (ManagementTask $record) {
                        $record->update(['status' => 'completed']);

                        Notification::make()
                            ->title('Tugas ditandai selesai')
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListManagementTasks::route('/'),
            'create' => Pages\CreateManagementTask::route('/create'),
            'edit' => Pages\EditManagementTask::route('/{record}/edit'),
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
