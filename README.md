# Manajemen Kelas — Dokumentasi

Repositori ini berisi aplikasi manajemen kelas berbasis Laravel dengan integrasi Filament Admin untuk mengelola pembayaran kas, pengumuman, jadwal, dan pengguna. Dokumentasi berikut menjelaskan langkah instalasi, konfigurasi, dan fitur utama.

## Persyaratan Sistem

- PHP >= 8.2 dengan ekstensi: `ctype`, `fileinfo`, `mbstring`, `openssl`, `pdo`, `tokenizer`
- Composer
- Node.js & npm (atau bun/PNPM sesuai preferensi)
- MySQL/MariaDB atau database lain yang didukung Laravel
- Git

## Instalasi Lokal

```bash
git clone https://github.com/your-org/class-manager.git
cd class-manager
cp .env.example .env
composer install
npm install
php artisan key:generate
```

Sesuaikan `.env` dengan kredensial database serta pengaturan aplikasi:

```env
APP_NAME="Manajemen Kelas"
APP_URL=http://class-manager.test
DB_DATABASE=class_manager
DB_USERNAME=root
DB_PASSWORD=secret

CLASS_APP_NAME="Manajemen Kelas"
CLASS_WEEKLY_CASH_AMOUNT=10000
CLASS_GITHUB_URL=https://github.com/your-org/class-manager
CLASS_FOOTER_TEXT="Dikelola oleh Komunitas Kelas Kompak"
```

### Migrasi & Seed

```bash
php artisan migrate
php artisan db:seed
```

- Seeder akan membuat akun demo (superadmin, admin, user) serta pengaturan default.

### Build Frontend & Jalankan Server

```bash
npm run dev    # atau npm run build untuk production
php artisan serve
```

## Fitur Utama

### 1. Pengaturan Aplikasi
- Kelola nama aplikasi, nominal kas mingguan, link GitHub, dan teks footer melalui menu **Pengaturan Aplikasi** di Filament.
- Nilai ini digunakan di navbar, footer, halaman login, dan logika kas.

### 2. Manajemen Kas
- Input pembayaran kas (tunai/transfer), konfirmasi status, dan monitoring kas masuk/keluar.
- Laporan kas menyediakan grafik bulanan/mingguan, daftar kas masuk/keluar, serta unduh PDF.
- Nominal target (kas mingguan) mengikuti pengaturan dinamis.

### 3. Pengiriman CSV Pengguna
- Import melalui Filament → Pengguna → *Import CSV* dengan format kolom: `no, nama, nim, email`.
- Sistem menambah atau memperbarui pengguna berdasarkan NIM.

### 4. Dashboard User & Admin
- Ringkasan kas pribadi dan kelas, agenda, pengumuman terkini.
- Banner pengumuman (marquee) ditampilkan bila ada berita terbaru.

### 5. Jadwal Kuliah
- Tampilan jadwal per hari dengan filter hari dan tabel ringkasan mingguan.

## Deploy ke Production

1. **Dependensi & Build**
   ```bash
   composer install --no-dev --optimize-autoloader
   npm ci && npm run build
   php artisan storage:link
   ```

2. **Migrasi & Seeder**
   ```bash
   php artisan migrate --force
   php artisan db:seed --class=SettingSeeder --force
   ```

3. **Optimisasi**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

4. **Queue & Scheduler** (opsional)
   - Jalankan `php artisan queue:work --daemon` bila memakai queue.
   - Tambahkan cron `* * * * * php /path/artisan schedule:run >> /dev/null 2>&1` untuk scheduler.

5. **Web Server**
   - Arahkan root server ke direktori `public/`.
   - Aktifkan HTTPS dan pastikan `APP_URL` menggunakan protokol yang benar.

## Akun Demo (Seeder)

| Role         | Email                  | Password |
|--------------|------------------------|----------|
| Super Admin  | `superadmin@example.com` | `password` |
| Admin        | `admin@example.com`      | `password` |
| User Demo    | `user@example.com`       | `password` |

## Struktur Direktori Penting

- `app/Filament/Resources/` – resource Filament untuk kas, pengaturan, pengguna, dll.
- `app/Support/Settings.php` – helper mengambil nilai setting.
- `resources/views/layouts/dashboard.blade.php` – layout utama user dengan navbar, banner, footer.
- `database/seeders/` – seeder role, user, dan pengaturan.

## Best Practice Pengembangan

- Gunakan branch feature → pull request ke `main`.
- Jalankan `php artisan test` sebelum merge/deploy.
- Format kode mengikuti standar Laravel; gunakan Pint (`./vendor/bin/pint`) bila perlu.

## Lisensi

Proyek ini mengikuti lisensi MIT (sesuaikan jika berbeda).

---

Untuk pertanyaan atau kontribusi, silakan ajukan issue/PR di repositori GitHub proyek ini.

