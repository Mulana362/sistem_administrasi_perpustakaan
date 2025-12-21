# Perpustakaan SMPN 1 Bandung

Aplikasi manajemen perpustakaan berbasis **Laravel**.

## Fitur (opsional)
- (Isi fitur utama aplikasi kamu di sini, contoh:)
- Manajemen buku, anggota, peminjaman, pengembalian
- Laporan dan rekap data
- Login & role (admin/petugas) *(jika ada)*

## Persyaratan
Pastikan sudah terpasang:
- PHP (sesuai versi Laravel yang digunakan)
- Composer
- Node.js + NPM
- Database MySQL/MariaDB (XAMPP boleh)

> Jika kamu pakai XAMPP: aktifkan **Apache** dan **MySQL**.

---

## Instalasi

### 1) Clone repository
```bash
git clone https://github.com/Mulana362/Perpustakaan_Smpn_1_Bandung.git
cd Perpustakaan_Smpn_1_Bandung

Setup Database
Buat database baru di phpMyAdmin, misalnya:
perpustakaan

Lalu atur bagian ini di file .env:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=perpustakaan
DB_USERNAME=root
DB_PASSWORD=

Migrasi database:
php artisan migrate

Jika project kamu punya seeder, jalankan:
php artisan db:seed
atau:
php artisan migrate --seed

Install dependency frontend (Vite)
npm install
npm run build


Untuk mode development (auto reload):
npm run dev

8) Jalankan aplikasi
php artisan serve

Buka:
http://127.0.0.1:8000
