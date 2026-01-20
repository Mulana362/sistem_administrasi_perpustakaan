# Sistem Administrasi Perpustakaan (Laravel + MySQL)

Aplikasi web **administrasi perpustakaan** untuk mengelola data buku & anggota, serta transaksi **peminjaman/pengembalian** lengkap dengan **denda** dan pencarian data.

## Fitur
- Halaman utama
- Login Admin
- Katalog Buku + pencarian
- Manajemen Data Buku (CRUD)
- Manajemen Anggota (CRUD)
- Transaksi Peminjaman
- Transaksi Pengembalian + perhitungan denda
- Riwayat/rekap transaksi peminjaman & pengembalian

## Tech Stack
- Laravel (PHP)
- MySQL / MariaDB
- HTML/CSS/Bootstrap
- Git & GitHub

## Screenshot
### 1) Halaman utama
<img width="1918" height="884" alt="Halaman Utama" src="https://github.com/user-attachments/assets/6fc09fc0-c0ae-4e1b-aae5-af715f7ab4e6" />

### 2) Halaman Login
<img width="1908" height="891" alt="Halaman Login" src="https://github.com/user-attachments/assets/2d654f38-d296-4fe8-813d-cef7d5e3a1d2" />

### 3) Dashboard
<img width="1907" height="899" alt="Dashboard" src="https://github.com/user-attachments/assets/9b0beaf4-0282-4382-9dc8-e18cb0f281aa" />

### 4) Data / Katalog Buku
<img width="1910" height="895" alt="Data Buku" src="https://github.com/user-attachments/assets/a8dfd29b-073a-4f45-a852-7a3e0e9e9d73" />

### 5) Transaksi (Peminjaman / Pengembalian)
<img width="1905" height="872" alt="Transaksi" src="https://github.com/user-attachments/assets/44e6d822-85e0-477a-ae7d-3442071232a3" />

## Cara Menjalankan (Local)
1. Clone
```bash
git clone https://github.com/Mulana362/sistem_administrasi_perpustakaan.git
cd sistem_administrasi_perpustakaan
```
2. Install & setup
```bash
composer install
cp .env.example .env
php artisan key:generate
```
3. Jalankan
```bash
php artisan migrate --seed
php artisan serve
```
## Akun Demo
> Password tidak ditampilkan. (Role yang tersedia: Admin)

- Admin: admin@gmail.com

## Catatan
Project ini dibuat sebagai latihan/penerapan sistem informasi perpustakaan menggunakan Laravel + MySQL.  
AI digunakan untuk brainstorming/debugging, sedangkan implementasi & pengujian dilakukan sendiri.
