<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('importUsers')
                ->label('Import CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->form([
                    Forms\Components\FileUpload::make('csv')
                        ->label('File CSV')
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv'])
                        ->required()
                        ->disk('local')
                        ->directory('imports/users')
                        ->visibility('private')
                        ->maxSize(2048)
                        ->helperText('Gunakan format header: no, nama, nim, email.'),
                ])
                ->action(function (array $data) {
                    $relativePath = $data['csv'] ?? null;

                    if (! $relativePath) {
                        Notification::make()
                            ->title('File tidak ditemukan')
                            ->danger()
                            ->send();

                        return;
                    }

                    $path = Storage::disk('local')->path($relativePath);

                    if (! is_file($path)) {
                        Notification::make()
                            ->title('File tidak dapat diakses')
                            ->danger()
                            ->send();

                        return;
                    }

                    $handle = fopen($path, 'r');

                    if ($handle === false) {
                        Notification::make()
                            ->title('Gagal membaca file CSV')
                            ->danger()
                            ->send();

                        return;
                    }

                    $created = 0;
                    $updated = 0;
                    $rowNumber = 0;
                    $headers = [];

                    try {
                        while (($row = fgetcsv($handle, 0, ',')) !== false) {
                            $rowNumber++;

                            // Skip empty rows
                            if (count(array_filter($row, fn ($value) => $value !== null && $value !== '')) === 0) {
                                continue;
                            }

                            if (empty($headers)) {
                                $headers = collect($row)
                                    ->map(fn ($header) => Str::slug((string) $header, '_'))
                                    ->toArray();
                                continue;
                            }

                            $rowData = array_pad($row, count($headers), null);
                            $assoc = @array_combine($headers, $rowData);

                            if (! is_array($assoc)) {
                                continue;
                            }

                            $nim = trim($assoc['nim'] ?? '');
                            $name = trim($assoc['nama'] ?? $assoc['name'] ?? '');
                            $email = trim($assoc['email'] ?? '');

                            if ($nim === '' || $name === '') {
                                continue;
                            }

                            $attributes = [
                                'name' => $name,
                                'email' => $email !== '' ? $email : null,
                                'role' => 'user',
                            ];

                            $user = User::query()->where('nim', $nim)->first();

                            if ($user) {
                                $user->fill($attributes);
                                $user->save();
                                $updated++;
                            } else {
                                $attributes['nim'] = $nim;
                                $attributes['password'] = Hash::make(Str::random(32));
                                User::create($attributes);
                                $created++;
                            }
                        }
                    } finally {
                        fclose($handle);
                        Storage::disk('local')->delete($relativePath);
                    }

                    Notification::make()
                        ->title('Import pengguna selesai')
                        ->success()
                        ->body("Ditambahkan: {$created} | Diperbarui: {$updated}")
                        ->send();
                })
                ->modalHeading('Import Pengguna dari CSV')
                ->modalButton('Import'),
            Actions\CreateAction::make(),
        ];
    }
}
