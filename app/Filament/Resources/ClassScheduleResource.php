<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassScheduleResource\Pages;
use App\Models\ClassSchedule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClassScheduleResource extends Resource
{
    protected static ?string $model = ClassSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Akademik';

    protected static ?string $navigationLabel = 'Jadwal Kuliah';

    protected static ?string $recordTitleAttribute = 'course_name';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Detail Jadwal')
                ->schema([
                    Forms\Components\TextInput::make('course_name')
                        ->label('Mata Kuliah')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('day_of_week')
                        ->label('Hari')
                        ->required()
                        ->options([
                            'monday' => 'Senin',
                            'tuesday' => 'Selasa',
                            'wednesday' => 'Rabu',
                            'thursday' => 'Kamis',
                            'friday' => 'Jumat',
                            'saturday' => 'Sabtu',
                            'sunday' => 'Minggu',
                        ])
                        ->searchable()
                        ->native(false),
                    Forms\Components\TimePicker::make('starts_at')
                        ->label('Jam Mulai')
                        ->required()
                        ->seconds(false),
                    Forms\Components\TimePicker::make('ends_at')
                        ->label('Jam Selesai')
                        ->required()
                        ->after('starts_at')
                        ->seconds(false),
                    Forms\Components\TextInput::make('room')
                        ->label('Ruangan')
                        ->maxLength(100),
                ])
                ->columns([
                    'default' => 1,
                    'md' => 2,
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('course_name')
                    ->label('Mata Kuliah')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('day_of_week')
                    ->label('Hari')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'monday' => 'Senin',
                        'tuesday' => 'Selasa',
                        'wednesday' => 'Rabu',
                        'thursday' => 'Kamis',
                        'friday' => 'Jumat',
                        'saturday' => 'Sabtu',
                        'sunday' => 'Minggu',
                        default => ucfirst((string) $state),
                    })
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('time_range')
                    ->label('Waktu')
                    ->getStateUsing(fn (ClassSchedule $record): string => sprintf(
                        '%s - %s',
                        $record->starts_at ? Carbon::parse($record->starts_at)->format('H:i') : '-',
                        $record->ends_at ? Carbon::parse($record->ends_at)->format('H:i') : '-',
                    )),
                Tables\Columns\TextColumn::make('room')
                    ->label('Ruangan')
                    ->badge()
                    ->color('primary')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('day_of_week')
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListClassSchedules::route('/'),
            'create' => Pages\CreateClassSchedule::route('/create'),
            'edit' => Pages\EditClassSchedule::route('/{record}/edit'),
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
