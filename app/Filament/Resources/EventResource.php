<?php

namespace App\Filament\Resources;

use App\Models\Event;
use App\Filament\Resources\EventResource\Pages\CreateEvent;
use App\Filament\Resources\EventResource\Pages\EditEvent;
use App\Filament\Resources\EventResource\Pages\ListEvents;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Kegiatan Kelas';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->label('Nama Kegiatan')->required(),
            Forms\Components\Textarea::make('description')->label('Deskripsi')->rows(4),
            Forms\Components\DatePicker::make('date')->label('Tanggal Kegiatan'),
            Forms\Components\Select::make('created_by')
                ->relationship('creator', 'name')
                ->label('Dibuat Oleh')
                ->default(fn () => Auth::id()),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('title')->label('Nama Kegiatan')->searchable(),
            Tables\Columns\TextColumn::make('date')->label('Tanggal')->date(),
            Tables\Columns\TextColumn::make('creator.name')->label('Pembuat'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvents::route('/'),
            'create' => CreateEvent::route('/create'),
            'edit' => EditEvent::route('/{record}/edit'),
        ];
    }
}
