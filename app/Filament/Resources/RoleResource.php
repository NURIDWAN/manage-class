<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Manajemen Akses';

    protected static ?string $navigationLabel = 'Peran';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Detail Peran')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Peran')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    Forms\Components\TextInput::make('guard_name')
                        ->label('Guard')
                        ->default(config('auth.defaults.guard', 'web'))
                        ->required()
                        ->maxLength(255),
                ]),
            Forms\Components\Section::make('Hak Akses')
                ->schema([
                    Forms\Components\Select::make('permissions')
                        ->label('Izin')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->relationship('permissions', 'name')
                        ->helperText('Pilih izin yang dimiliki oleh peran ini.')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')
                ->label('Nama')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('guard_name')
                ->label('Guard')
                ->badge()
                ->color('warning'),
            Tables\Columns\TextColumn::make('permissions_count')
                ->counts('permissions')
                ->label('Jumlah Izin')
                ->badge()
                ->suffix(' izin'),
            Tables\Columns\TextColumn::make('updated_at')
                ->label('Terakhir Diubah')
                ->since(),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
