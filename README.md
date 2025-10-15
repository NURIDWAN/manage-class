# Class Manager v2

Aplikasi manajemen kelas berbasis Laravel 11 dan Filament 3 untuk memonitor kas, mengelola agenda, membagikan dokumen, serta mengatur anggota kelas dalam satu dashboard modern.

## Isi
- Fitur Utama
- Teknologi
- Prasyarat
- Instalasi & Konfigurasi
- Menjalankan Aplikasi
- Data Awal & Akun Demo
- Struktur Proyek
- Perintah Penting
- Testing & Kualitas
- Deployment Singkat
- Kontribusi & Lisensi

## Fitur Utama
- **Dashboard anggota**: ringkasan kas pribadi, progres target bulanan, agenda terdekat, dan pengumuman terbaru.
- **Manajemen kas kelas**: pencatatan kas masuk/keluar, konfirmasi pembayaran, pelacakan sisa tagihan, arsip transaksi, serta unduh bukti pembayaran.
- **Laporan PDF**: ekspor laporan kas bulanan lengkap dengan perhitungan target per anggota.
- **Pengaturan aplikasi**: melalui Filament dapat mengubah nama aplikasi, nominal kas mingguan, tautan GitHub, dan teks footer yang digunakan di seluruh UI.
- **Dokumen & arsip**: unggah dokumen kelas, arsip poster pembayaran, serta kelola jadwal kuliah, event, dan pengumuman.
- **Hak akses granular**: integrasi Filament Shield untuk role `super_admin`, `admin`, dan `user`.

## Teknologi
- Laravel 11, PHP 8.2+
- Filament 3 + Filament Shield
- Tailwind CSS, Vite, dan Alpine/Blade components
- DomPDF untuk generate laporan PDF
- Database: MySQL/MariaDB, PostgreSQL, atau SQLite

## Prasyarat
- PHP 8.2 beserta ekstensi: `ctype`, `fileinfo`, `mbstring`, `openssl`, `pdo`, `tokenizer`
- Composer
- Node.js 18+ dan npm (atau PNPM/Bun sesuai preferensi)
- Database server (MySQL/MariaDB/SQLite/PostgreSQL)
- Git

## Instalasi & Konfigurasi
```bash
git clone https://github.com/your-org/class-managerv2.git
cd class-managerv2
cp .env.example .env
composer install
npm install        # atau pnpm install / bun install
php artisan key:generate
```

Konfigurasikan `.env` untuk koneksi database, mail, dan opsi aplikasi:

```env
APP_NAME="Class Manager"
APP_URL=http://class-manager.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=class_manager
DB_USERNAME=root
DB_PASSWORD=secret

CLASS_APP_NAME="Class Manager"
CLASS_WEEKLY_CASH_AMOUNT=10000   # nominal kas per minggu
CLASS_GITHUB_URL=https://github.com/your-org/class-managerv2
CLASS_FOOTER_TEXT="Dikelola bersama oleh Komunitas Kelas"
```

## Menjalankan Aplikasi
- Migrasi & seed awal:
  ```bash
  php artisan migrate --seed
  php artisan storage:link
  ```
- Jalankan backend & frontend:
  ```bash
  php artisan serve
  npm run dev          # Vite dev server
  ```
- Alternatif all-in-one (server + queue + Vite + log tail):
  ```bash
  composer dev
  ```

## Data Awal & Akun Demo
Seeder menyiapkan role, pengaturan, serta akun berikut:

| Role        | Email                    | Password  |
|-------------|--------------------------|-----------|
| Super Admin | superadmin@example.com   | password  |
| Admin       | admin@example.com        | password  |
| User Demo   | user@example.com         | password  |

Setelah login, lengkapi profil (NIM, kelas, nomor HP) untuk mengakses modul kas dan laporan.

## Struktur Proyek
- `app/Filament/Resources/` – modul administratif untuk kas, pengeluaran, jadwal, dokumen, dan manajemen pengguna.
- `app/Support/` – helper konfigurasi (`Settings`, `DashboardDataBuilder`) untuk menyiapkan statistik dan data laporan.
- `resources/views/` – tampilan dashboard anggota (`dashboard/`), halaman jadwal publik, dan template PDF.
- `database/migrations/` – skema kas, dana kelas, pengumuman, event, dan role-permission.
- `database/seeders/` – data default pengaturan, akun demo, agenda, dan kas awal.

## Perintah Penting
- `php artisan migrate:fresh --seed` – reset database dengan data awal.
- `php artisan make:filament-resource ...` – membuat resource baru di panel admin.
- `npm run build` – build aset produksi via Vite.
- `php artisan queue:work` / `schedule:work` – jalankan queue dan scheduler bila fitur tersebut diaktifkan.

## Testing & Kualitas
- Jalankan test:
  ```bash
  php artisan test
  ```
- Format kode (opsional):
  ```bash
  ./vendor/bin/pint
  ```

## Deployment Singkat
1. `composer install --no-dev --optimize-autoloader`
2. `npm ci && npm run build`
3. `php artisan migrate --force`
4. `php artisan config:cache route:cache view:cache`
5. Pastikan root web server diarahkan ke direktori `public/` dan `APP_URL` menggunakan HTTPS di environment produksi.

## Kontribusi & Lisensi
- Gunakan alur feature branch → pull request ke `main`.
- Buka issue untuk bug/pertanyaan, sertakan langkah reproduksi.
- Proyek berada di bawah lisensi MIT. Sesuaikan jika kebijakan internal berbeda.

Terima kasih sudah menggunakan Class Manager v2! Untuk ide fitur atau integrasi baru, silakan ajukan melalui issue/PR.
