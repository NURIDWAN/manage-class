<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Pengaturan Sistem';

    protected static ?string $navigationLabel = 'Pengaturan Aplikasi';

    protected static ?string $recordTitleAttribute = 'key';

    public static function form(Form $form): Form
    {
        $options = [
            'app_name' => 'Nama Aplikasi',
            'weekly_cash_amount' => 'Nominal Kas Mingguan (Rp)',
            'github_url' => 'Link GitHub',
            'footer_text' => 'Teks Footer',
        ];

        return $form->schema([
            Forms\Components\Select::make('key')
                ->label('Jenis Pengaturan')
                ->options($options)
                ->required()
                ->searchable()
                ->disabled(fn (?Setting $record): bool => $record !== null)
                ->unique(ignoreRecord: true)
                ->helperText('Pilih pengaturan yang ingin diatur.'),
            Forms\Components\TextInput::make('value')
                ->label(fn (callable $get) => $options[$get('key') ?? 'app_name'] ?? 'Nilai')
                ->required()
                ->rules(function (callable $get) {
                    return $get('key') === 'weekly_cash_amount'
                        ? ['required', 'numeric', 'min:0']
                        : ['required', 'string'];
                })
                ->dehydrateStateUsing(function ($state, callable $get) {
                    $state = is_string($state) ? trim($state) : $state;

                    if ($get('key') === 'weekly_cash_amount') {
                        return (string) ((int) $state);
                    }

                    return (string) $state;
                })
                ->helperText(fn (callable $get) => match ($get('key')) {
                    'weekly_cash_amount' => 'Masukkan nominal kas mingguan dalam rupiah.',
                    'github_url' => 'Opsional. Masukkan URL repositori GitHub untuk ditampilkan di footer.',
                    'footer_text' => 'Keterangan singkat yang tampil pada footer.',
                    default => 'Nama aplikasi akan muncul di navbar dan halaman login.',
                })
                ->columnSpanFull(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        $labels = [
            'app_name' => 'Nama Aplikasi',
            'weekly_cash_amount' => 'Kas Mingguan',
            'github_url' => 'Link GitHub',
            'footer_text' => 'Teks Footer',
        ];

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Pengaturan')
                    ->formatStateUsing(fn (string $state): string => $labels[$state] ?? $state)
                    ->searchable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Nilai')
                    ->formatStateUsing(function ($state, Setting $record) {
                        return $record->key === 'weekly_cash_amount'
                            ? 'Rp ' . number_format((int) $state, 0, ',', '.')
                            : (string) $state;
                    })
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->since(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
