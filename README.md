# Backend Infranexia

Backend Infranexia adalah REST API berbasis Laravel untuk mendukung sistem informasi operasional jaringan fiber optik. Backend ini menangani data aset jaringan, gangguan, tugas pekerjaan lapangan, laporan operator, data pengguna, metadata sistem, dan log aktivitas.

Project ini dirancang sebagai fondasi backend untuk aplikasi monitoring operasional jaringan. Sistem ini dapat digunakan oleh admin dan operator lapangan untuk mencatat, memantau, dan mengelola aktivitas jaringan secara lebih terstruktur.

## Daftar Isi

* [Tentang Project](#tentang-project)
* [Fitur Utama](#fitur-utama)
* [Tech Stack](#tech-stack)
* [Requirement](#requirement)
* [Struktur Folder](#struktur-folder)
* [Instalasi Local](#instalasi-local)
* [Konfigurasi Environment](#konfigurasi-environment)
* [Menjalankan Project](#menjalankan-project)
* [Database dan Seeder](#database-dan-seeder)
* [Akun Demo](#akun-demo)
* [API Endpoint](#api-endpoint)
* [Contoh Request Login](#contoh-request-login)
* [Testing](#testing)
* [Deployment](#deployment)
* [Docker](#docker)
* [Catatan Pengembangan](#catatan-pengembangan)
* [Author](#author)

## Tentang Project

Backend Infranexia menyediakan API untuk aplikasi operasional jaringan fiber optik. Sistem ini membantu proses pencatatan aset, pelaporan gangguan, pengelolaan tugas lapangan, dan validasi laporan operator.

Backend ini cocok digunakan untuk:

* Sistem monitoring aset jaringan fiber optik.
* Dashboard gangguan jaringan.
* Pencatatan laporan operator lapangan.
* Manajemen tugas teknisi atau operator.
* Integrasi frontend berbasis React, Vue, Next.js, atau aplikasi mobile.
* Pengembangan sistem berbasis GIS dan data operasional.

## Fitur Utama

### 1. Authentication

* Login menggunakan email dan password.
* Validasi akun aktif.
* Response login berisi data user dan token.
* Mendukung role user seperti admin dan operator.

### 2. User Management

* Mengelola data pengguna.
* Menyimpan nama, email, role, nomor telepon, region, dan status akun.
* Mendukung admin dan operator lapangan.

### 3. Asset Management

* Mengelola data aset jaringan.
* Mendukung jenis aset seperti ODP, ODC, Tiang, Kabel Feeder, Kabel Distribusi, Closure, dan STO.
* Menyimpan lokasi aset dengan latitude dan longitude.
* Menyimpan status aset seperti active, monitoring, damaged, dan inactive.

### 4. Disturbance Management

* Mengelola data gangguan jaringan.
* Menyimpan kode gangguan, jenis gangguan, tingkat severity, status, lokasi, deskripsi, waktu laporan, dan waktu penyelesaian.
* Mendukung penugasan gangguan ke operator.

### 5. Field Task Management

* Mengelola tugas pekerjaan lapangan.
* Menyimpan kode tugas, aset terkait, operator, prioritas, status, lokasi, dan due date.
* Cocok untuk pekerjaan inspeksi, validasi, perbaikan, dan tindak lanjut gangguan.

### 6. Field Report

* Mengelola laporan hasil pekerjaan lapangan.
* Menyimpan kondisi sebelum pekerjaan, tindakan yang dilakukan, kondisi setelah pekerjaan, lampiran, status laporan, catatan admin, waktu submit, dan waktu approval.

### 7. Activity Log

* Mencatat aktivitas penting dalam sistem.
* Menyimpan user, action, module, dan timestamp.
* Berguna untuk audit sederhana dan monitoring aktivitas pengguna.

### 8. Metadata API

* Menyediakan data referensi untuk frontend.
* Berisi daftar region, tipe aset, tipe gangguan, label prioritas, dan label status.

### 9. Health Check API

* Endpoint sederhana untuk mengecek status backend.
* Berguna untuk testing koneksi frontend dan deployment.

## Tech Stack

| Kategori            | Teknologi    |
| ------------------- | ------------ |
| Backend Framework   | Laravel      |
| Bahasa              | PHP 8.3      |
| Database            | PostgreSQL   |
| ORM                 | Eloquent ORM |
| Package Manager PHP | Composer     |
| Frontend Build Tool | Vite         |
| CSS Utility         | Tailwind CSS |
| JavaScript Runtime  | Node.js      |
| Testing             | PHPUnit      |
| Container           | Docker       |

## Requirement

Pastikan perangkat sudah memiliki:

* PHP 8.3 atau lebih baru.
* Composer.
* Node.js 20 atau lebih baru.
* npm.
* PostgreSQL.
* Git.

Cek versi dengan perintah berikut:

```bash
php -v
composer -V
node -v
npm -v
psql --version
git --version
```

## Struktur Folder

Struktur utama project:

```text
backendinfranexia/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Api/
│   │           ├── ActivityLogController.php
│   │           ├── AssetController.php
│   │           ├── AuthController.php
│   │           ├── CrudController.php
│   │           ├── DisturbanceController.php
│   │           ├── FieldReportController.php
│   │           ├── MetaController.php
│   │           ├── PruningTaskController.php
│   │           └── UserController.php
│   ├── Models/
│   │   ├── ActivityLog.php
│   │   ├── Asset.php
│   │   ├── Disturbance.php
│   │   ├── FieldReport.php
│   │   ├── PruningTask.php
│   │   └── User.php
│   └── Providers/
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   │   └── 2026_06_14_000000_create_operational_tables.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── public/
├── resources/
├── routes/
│   ├── api.php
│   ├── console.php
│   └── web.php
├── scripts/
├── storage/
├── tests/
├── .env.example
├── Dockerfile
├── composer.json
├── package.json
├── phpunit.xml
└── vite.config.js
```

## Instalasi Local

Clone repository:

```bash
git clone https://github.com/rizkiwahyuu/backendinfranexia.git
cd backendinfranexia
```

Install dependency Laravel:

```bash
composer install
```

Install dependency Node.js:

```bash
npm install
```

Copy file environment:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Buat database PostgreSQL:

```sql
CREATE DATABASE laravel;
```

Sesuaikan konfigurasi database di file `.env`.

Jalankan migration:

```bash
php artisan migrate
```

Jalankan seeder:

```bash
php artisan db:seed
```

Atau jalankan migration dan seeder sekaligus:

```bash
php artisan migrate:fresh --seed
```

Build asset frontend:

```bash
npm run build
```

## Konfigurasi Environment

Contoh konfigurasi `.env` untuk PostgreSQL:

```env
APP_NAME="Backend Infranexia"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=postgres
DB_PASSWORD=password_database_kamu
DB_SSLMODE=prefer

SESSION_DRIVER=database
SESSION_LIFETIME=120

QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

VITE_APP_NAME="${APP_NAME}"
```

Catatan:

* Ganti `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` sesuai konfigurasi PostgreSQL lokal.
* Jangan commit file `.env` ke GitHub.
* Gunakan `.env.example` sebagai template konfigurasi.

## Menjalankan Project

Jalankan Laravel development server:

```bash
php artisan serve
```

Backend akan berjalan di:

```text
http://localhost:8000
```

Untuk menjalankan Vite:

```bash
npm run dev
```

Project juga menyediakan script development dari Composer:

```bash
composer run dev
```

Script tersebut menjalankan server Laravel, queue listener, log viewer, dan Vite secara bersamaan.

## Database dan Seeder

Project ini memiliki beberapa tabel operasional utama:

| Tabel         | Fungsi                                                 |
| ------------- | ------------------------------------------------------ |
| users         | Menyimpan data pengguna, role, region, dan status akun |
| assets        | Menyimpan data aset jaringan                           |
| disturbances  | Menyimpan data gangguan jaringan                       |
| pruning_tasks | Menyimpan data tugas pekerjaan lapangan                |
| field_reports | Menyimpan laporan hasil pekerjaan operator             |
| activity_logs | Menyimpan log aktivitas sistem                         |

Seeder menyediakan data awal untuk:

* Admin.
* Operator.
* Aset jaringan.
* Gangguan.
* Tugas pekerjaan.
* Laporan lapangan.
* Log aktivitas.

Jalankan seeder dengan:

```bash
php artisan db:seed
```

Reset database dan isi ulang data dummy:

```bash
php artisan migrate:fresh --seed
```

## Akun Demo

Setelah menjalankan seeder, gunakan akun berikut untuk login saat development.

### Admin

```text
Email    : admin@sigaptif.id
Password : admin123
Role     : admin
```

### Operator

```text
Email    : budi@sigaptif.id
Password : operator123
Role     : operator
```

Catatan keamanan:

* Akun ini hanya untuk development.
* Ganti akun demo sebelum production.
* Jangan gunakan password contoh pada server production.

## API Endpoint

Base URL local:

```text
http://localhost:8000/api
```

### Health Check

| Method | Endpoint  | Deskripsi               |
| ------ | --------- | ----------------------- |
| GET    | `/health` | Mengecek status backend |

Response:

```json
{
  "status": "ok"
}
```

### Metadata

| Method | Endpoint | Deskripsi                       |
| ------ | -------- | ------------------------------- |
| GET    | `/meta`  | Mengambil data referensi sistem |

Data metadata berisi:

* Region.
* Asset types.
* Disturbance types.
* Priority labels.
* Status labels.

### Authentication

| Method | Endpoint | Deskripsi  |
| ------ | -------- | ---------- |
| POST   | `/login` | Login user |

### Users

| Method    | Endpoint      | Deskripsi             |
| --------- | ------------- | --------------------- |
| GET       | `/users`      | Mengambil semua user  |
| POST      | `/users`      | Membuat user baru     |
| GET       | `/users/{id}` | Mengambil detail user |
| PUT/PATCH | `/users/{id}` | Mengubah data user    |
| DELETE    | `/users/{id}` | Menghapus user        |

### Assets

| Method    | Endpoint       | Deskripsi             |
| --------- | -------------- | --------------------- |
| GET       | `/assets`      | Mengambil semua aset  |
| POST      | `/assets`      | Membuat aset baru     |
| GET       | `/assets/{id}` | Mengambil detail aset |
| PUT/PATCH | `/assets/{id}` | Mengubah data aset    |
| DELETE    | `/assets/{id}` | Menghapus aset        |

### Disturbances

| Method    | Endpoint             | Deskripsi                 |
| --------- | -------------------- | ------------------------- |
| GET       | `/disturbances`      | Mengambil semua gangguan  |
| POST      | `/disturbances`      | Membuat gangguan baru     |
| GET       | `/disturbances/{id}` | Mengambil detail gangguan |
| PUT/PATCH | `/disturbances/{id}` | Mengubah data gangguan    |
| DELETE    | `/disturbances/{id}` | Menghapus gangguan        |

### Field Tasks

| Method    | Endpoint              | Deskripsi                       |
| --------- | --------------------- | ------------------------------- |
| GET       | `/pruning-tasks`      | Mengambil semua tugas lapangan  |
| POST      | `/pruning-tasks`      | Membuat tugas lapangan baru     |
| GET       | `/pruning-tasks/{id}` | Mengambil detail tugas lapangan |
| PUT/PATCH | `/pruning-tasks/{id}` | Mengubah data tugas lapangan    |
| DELETE    | `/pruning-tasks/{id}` | Menghapus tugas lapangan        |

### Field Reports

| Method    | Endpoint              | Deskripsi                        |
| --------- | --------------------- | -------------------------------- |
| GET       | `/field-reports`      | Mengambil semua laporan lapangan |
| POST      | `/field-reports`      | Membuat laporan lapangan baru    |
| GET       | `/field-reports/{id}` | Mengambil detail laporan         |
| PUT/PATCH | `/field-reports/{id}` | Mengubah data laporan            |
| DELETE    | `/field-reports/{id}` | Menghapus laporan                |

### Activity Logs

| Method | Endpoint              | Deskripsi                      |
| ------ | --------------------- | ------------------------------ |
| GET    | `/activity-logs`      | Mengambil semua log aktivitas  |
| POST   | `/activity-logs`      | Membuat log aktivitas baru     |
| GET    | `/activity-logs/{id}` | Mengambil detail log aktivitas |

## Contoh Request Login

Endpoint:

```text
POST /api/login
```

Body:

```json
{
  "email": "admin@sigaptif.id",
  "password": "admin123"
}
```

Contoh response:

```json
{
  "user": {
    "id": 1,
    "name": "Admin Utama",
    "email": "admin@sigaptif.id",
    "role": "admin",
    "is_active": true
  },
  "token": "base64-token"
}
```

## Contoh Request Membuat Asset

Endpoint:

```text
POST /api/assets
```

Body:

```json
{
  "asset_code": "ODP-SBY-001",
  "asset_name": "ODP Surabaya Barat 001",
  "asset_type": "ODP",
  "region_id": 3,
  "latitude": -7.2562,
  "longitude": 112.6629,
  "address": "Jl. Manukan Tama",
  "status": "active",
  "installation_date": "2024-01-10",
  "notes": "Aset contoh",
  "created_by": 1
}
```

## Testing

Jalankan test Laravel:

```bash
php artisan test
```

Atau gunakan script Composer:

```bash
composer test
```

## Deployment

Langkah umum deployment ke server production:

1. Clone repository ke server.

```bash
git clone https://github.com/rizkiwahyuu/backendinfranexia.git
cd backendinfranexia
```

2. Install dependency production.

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

3. Buat file `.env`.

```bash
cp .env.example .env
```

4. Sesuaikan konfigurasi production.

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-backend-kamu.com

DB_CONNECTION=pgsql
DB_HOST=host_database
DB_PORT=5432
DB_DATABASE=nama_database
DB_USERNAME=user_database
DB_PASSWORD=password_database
```

5. Generate key jika belum ada.

```bash
php artisan key:generate
```

6. Jalankan migration production.

```bash
php artisan migrate --force
```

7. Optimasi Laravel.

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

8. Pastikan permission folder benar.

```bash
chmod -R 775 storage bootstrap/cache
```

9. Arahkan document root web server ke folder:

```text
public/
```

10. Jalankan queue worker jika fitur queue digunakan.

```bash
php artisan queue:work
```

Rekomendasi konfigurasi web server:

* Nginx atau Apache.
* PHP-FPM untuk production.
* PostgreSQL sebagai database utama.
* SSL aktif menggunakan HTTPS.
* Backup database berkala.
* Jangan aktifkan `APP_DEBUG=true` di production.

## Docker

Project ini memiliki `Dockerfile`.

Build image:

```bash
docker build -t backendinfranexia .
```

Jalankan container:

```bash
docker run -p 8000:8000 --env-file .env backendinfranexia
```

Backend akan berjalan di:

```text
http://localhost:8000
```

Catatan:

* Pastikan database PostgreSQL dapat diakses dari container.
* Jika PostgreSQL berjalan di host lokal, sesuaikan `DB_HOST`.
* Untuk development berbasis container penuh, tambahkan `docker-compose.yml`.

## Catatan Pengembangan

Beberapa hal yang dapat dikembangkan selanjutnya:

* Menambahkan Laravel Sanctum untuk autentikasi token yang lebih aman.
* Menambahkan middleware role untuk admin dan operator.
* Menambahkan pagination pada endpoint list.
* Menambahkan filter berdasarkan region, status, tanggal, dan severity.
* Menambahkan upload file untuk lampiran laporan lapangan.
* Menambahkan validasi API yang lebih detail.
* Menambahkan dokumentasi OpenAPI atau Swagger.
* Menambahkan dashboard analytics untuk gangguan dan performa jaringan.
* Menambahkan audit log otomatis pada setiap perubahan data.
* Menambahkan test untuk setiap endpoint API.

## Author

Rizki Wahyu Widodo

* GitHub: rizkiwahyuu
* Project: Backend Infranexia
* Fokus: Sistem Informasi Operasional Jaringan Fiber Optik

## License

Project ini menggunakan struktur Laravel. Sesuaikan bagian lisensi dengan kebutuhan repository.
