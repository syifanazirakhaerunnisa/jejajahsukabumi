# JelajahSukabumi - Sistem Informasi Direktori Wisata & Destinasi Lokal Kota Sukabumi

> **Proyek Mata Kuliah:** Proyek Perangkat Lunak  
> **Program Studi:** Sistem Informasi  
> **Target Wilayah:** Kota Sukabumi, Jawa Barat  
> **Tipe Aplikasi:** Direktori Digital Publik Non-Komersial & Panel Admin CRUD  

---

## 1. Deskripsi Proyek

**JelajahSukabumi** adalah sistem informasi direktori wisata dan destinasi lokal berbasis web yang dirancang untuk memetakan dan menyajikan informasi seputar tempat wisata, cagar budaya sejarah, kuliner khas, dan ruang terbuka publik ramah keluarga di wilayah administratif **Kota Sukabumi** (Kecamatan Cikole, Citamiang, Lembursitu, Warudoyong, Baros, Gunungpuyuh, dan Cibeureum).

Website ini bersifat direktori murni (bukan marketplace) tanpa proses transaksi komersial, ditujukan untuk mempermudah masyarakat lokal, pelajar, dan wisatawan menemukan informasi yang valid, terstruktur, dan terpercaya.

---

## 2. Fitur Utama

### A. Fitur Publik (Pengunjung)
1. **Beranda Interaktif:**
   - Hero section visual panorama Kota Sukabumi berlatar Gunung Gede Pangrango.
   - Kolom pencarian cepat terintegrasi langsung dengan katalog destinasi.
   - 4 Kategori tematik interaktif (Wisata & Rekreasi, Kuliner Lokal, Sejarah & Heritage, Ruang Publik) dengan jumlah destinasi real-time.
   - Kartu 6 destinasi pilihan teratas dari database.
   - Section profil dan identitas lokal Kota Sukabumi.
2. **Katalog Destinasi:**
   - Filter multi-kriteria: Kata kunci (nama/alamat/deskripsi), Kategori, Wilayah/Kecamatan, dan Pengurutan (Terbaru, Nama A-Z, Nama Z-A, Terlama).
   - Pagination responsif (6 item per halaman).
   - *Empty state* ramah pengguna jika pencarian tidak ditemukan.
   - Hanya menampilkan destinasi berstatus *publish*.
3. **Halaman Detail Destinasi:**
   - Foto utama beresolusi tinggi.
   - Badge kategori tematik.
   - Alamat lengkap dan deskripsi naratif.
   - Jam operasional dan kisaran biaya (ditandai jelas jika belum diverifikasi).
   - Titik koordinat GPS dan tombol rute interaktif menuju **Google Maps**.
   - Rekomendasi 3 destinasi terkait dalam kategori yang sama.
4. **Halaman Tentang Kami:**
   - Latar belakang akademik, visi, misi, dan nilai non-komersial platform.

### B. Fitur Panel Administrator (`/admin/`)
1. **Autentikasi & Keamanan:**
   - Login terproteksi password hashing (`password_verify()` dan `PASSWORD_BCRYPT`).
   - Proteksi sesi PHP di setiap controller admin.
   - Proteksi CSRF Token pada seluruh formulir manipulasi data (POST).
   - Validasi ketat format & ukuran file gambar (Maks. 3 MB; format JPG, PNG, WebP).
2. **Dashboard Statistik:**
   - 4 Kartu metrik: Total Destinasi, Total Kategori, Total Publish, dan Total Draft.
   - Grafik interaktif (*Bar Chart*) distribusi destinasi per kategori menggunakan Chart.js.
   - Tabel ringkasan 5 destinasi terbaru dengan tombol aksi cepat.
3. **CRUD Data Destinasi:**
   - Tambah destinasi baru lengkap dengan upload foto & pratinjau (*live preview*).
   - Edit data destinasi yang sudah ada (dengan opsi mempertahankan atau mengganti foto lama).
   - Hapus destinasi dengan konfirmasi keamanan via metode POST (otomatis membersihkan file upload terkait).
   - Dukungan status publikasi (*Publish* atau *Draft*).
4. **CRUD Kategori Wisata:**
   - Tambah kategori baru.
   - Edit nama dan deskripsi kategori.
   - Hapus kategori dengan **proteksi integritas relasional** (kategori tidak dapat dihapus jika masih digunakan oleh destinasi aktif).
5. **Laporan & Cetak:**
   - Rekap data destinasi dalam tabel terstruktur.
   - Filter laporan berdasarkan kategori dan status publikasi.
   - Tombol cetak menggunakan print dialog browser dengan template KOP resmi akademik untuk pengujian/evaluasi dosen.

---

## 3. Teknologi yang Digunakan

- **Server:** XAMPP (Apache HTTP Server & MariaDB/MySQL)
- **Bahasa Pemrograman:** PHP Native 8.0+ (berorientasi PDO & prepared statements)
- **Basis Data:** MariaDB / MySQL (`jelajah_sukabumi`)
- **Frontend Framework:** Bootstrap 5.3.2 & Bootstrap Icons 1.11.3
- **Styling:** Vanilla CSS3 dengan Palet Lokal:
  - Hijau Tua: `#234B3D`
  - Krem: `#F7F3EA`
  - Putih: `#FFFFFF`
  - Cokelat: `#9A7556`
  - Abu-abu: `#6B7280`
- **Visualisasi Data:** Chart.js 4.4.1 (CDN)
- **Tipografi:** Plus Jakarta Sans & Playfair Display (Google Fonts)

---

## 4. Struktur Folder

```text
c:/xampp/htdocs/JelajahSukabumi/
│
├── admin/                     # Area Panel Administrator
│   ├── index.php              # Dashboard metrik & grafik Chart.js
│   ├── destinasi.php          # Daftar data destinasi & filter
│   ├── destinasi_tambah.php   # Form tambah destinasi & upload
│   ├── destinasi_edit.php     # Form edit destinasi & preview
│   ├── destinasi_hapus.php    # Handler hapus destinasi (POST only)
│   ├── kategori.php           # CRUD kategori & proteksi relasi
│   └── laporan.php            # Laporan rekap & print preview
│
├── assets/                    # Aset Statis
│   ├── css/
│   │   ├── style.css          # CSS utama frontend bertema lokal
│   │   └── admin.css          # CSS dashboard admin & print stylesheet
│   ├── js/
│   │   └── main.js            # JavaScript preview upload & animasi
│   └── images/
│       └── hero_sukabumi.jpg  # Banner panorama Kota Sukabumi
│
├── config/
│   └── database.php           # Koneksi PDO MySQL & error handler
│
├── includes/                  # Komponen Modular Reusable
│   ├── header.php             # Navbar & header publik
│   ├── footer.php             # Footer publik & copyright
│   ├── admin_header.php       # Topbar & nav admin
│   ├── admin_sidebar.php      # Sidebar hijau tua admin
│   ├── admin_footer.php       # Footer admin & hook JS
│   ├── functions.php          # Helper sanitasi, flash, upload, CSRF
│   └── auth.php               # Middleware proteksi sesi login admin
│
├── uploads/
│   └── destinasi/             # Direktori penyimpanan foto destinasi
│
├── index.php                  # Halaman Beranda Publik
├── katalog.php                # Halaman Katalog Direktori & Filter
├── detail.php                 # Halaman Detail Informasi Destinasi
├── tentang.php                # Halaman Profil & Visi Misi Website
├── login.php                  # Form Login Admin
├── logout.php                 # Logout & pembersihan sesi
├── database.sql               # Skema tabel & 10 data awal terverifikasi
└── README.md                  # Dokumentasi lengkap sistem
```

---

## 5. Panduan Instalasi dan Menjalankan Aplikasi

### Langkah 1: Persiapan XAMPP
1. Pastikan Anda telah menginstal aplikasi **XAMPP**.
2. Buka **XAMPP Control Panel**.
3. Klik tombol **Start** pada modul **Apache** dan **MySQL** hingga indikator berwarna hijau.

### Langkah 2: Lokasi Folder Proyek
Pastikan folder proyek ini berada di direktori `htdocs` XAMPP Anda:
```text
C:\xampp\htdocs\JelajahSukabumi
```

### Langkah 3: Impor Basis Data MySQL
Ada dua cara mudah untuk mengimpor file `database.sql`:

#### Cara A: Melalui phpMyAdmin (Grafis)
1. Buka browser dan akses: `http://localhost/phpmyadmin`
2. Klik tab **Databases / Basis data**, buat basis data baru bernama:
   ```sql
   jelajah_sukabumi
   ```
   (Pilih Collation: `utf8mb4_unicode_ci` lalu klik **Create**).
3. Klik nama basis data `jelajah_sukabumi` pada bilah kiri.
4. Klik tab **Import / Impor** pada menu atas.
5. Klik **Choose File / Telusuri file**, lalu pilih file:
   `C:\xampp\htdocs\JelajahSukabumi\database.sql`
6. Gulir ke bawah dan klik tombol **Import / Kirim**.

#### Cara B: Melalui Terminal / Command Prompt
```bash
cd C:\xampp\mysql\bin
mysql.exe -u root -p < C:\xampp\htdocs\JelajahSukabumi\database.sql
```
*(Tekan Enter saat diminta password jika menggunakan default XAMPP).*

### Langkah 4: Konfigurasi Database (Jika Diperlukan)
File konfigurasi terletak di `config/database.php`. Pengaturan bawaan XAMPP telah terpasang:
```php
$db_host = 'localhost';
$db_port = '3306';
$db_name = 'jelajah_sukabumi';
$db_user = 'root';
$db_pass = ''; // default XAMPP tanpa password
```

### Langkah 5: Mengakses Aplikasi di Browser
- **Halaman Pengunjung (Publik):**
  [http://localhost/JelajahSukabumi/](http://localhost/JelajahSukabumi/)
- **Halaman Katalog:**
  [http://localhost/JelajahSukabumi/katalog.php](http://localhost/JelajahSukabumi/katalog.php)
- **Halaman Login Admin:**
  [http://localhost/JelajahSukabumi/login.php](http://localhost/JelajahSukabumi/login.php)

---

## 6. Akun Administrator Default

Untuk keperluan pengujian, demonstrasi akademik, dan penilaian:
- **URL Login:** `http://localhost/JelajahSukabumi/login.php`
- **Email:** `admin@jelajahsukabumi.id`
- **Password:** `admin123`

*Catatan Keamanan:* Password dienkripsi menggunakan fungsi standar PHP `password_hash()` dengan algoritma BCRYPT.

---

## 7. Data Destinasi Terverifikasi Kota Sukabumi

Database awal telah dilengkapi 10 destinasi yang secara administratif 100% berada di Kota Sukabumi:

| No | Nama Destinasi | Kategori | Kecamatan | Kisaran Biaya |
|----|----------------|----------|-----------|---------------|
| 1 | **Alun-Alun Kota Sukabumi** | Ruang Publik | Cikole | Gratis |
| 2 | **Masjid Agung Kota Sukabumi** | Sejarah & Heritage | Cikole | Gratis |
| 3 | **Gedung Juang 45 Kota Sukabumi** | Sejarah & Heritage | Cikole | Gratis / Donasi |
| 4 | **Stasiun Sukabumi** | Sejarah & Heritage | Cikole | Sesuai Tiket KA |
| 5 | **Pusat Mochi Kaswari Lampion** | Kuliner Lokal | Cikole | Rp 40.000 - Rp 60.000 |
| 6 | **Bubur Ayam Bunut Siliwangi** | Kuliner Lokal | Cikole | Rp 20.000 - Rp 35.000 |
| 7 | **Lapang Merdeka Kota Sukabumi** | Ruang Publik | Cikole | Gratis |
| 8 | **Pemandian Air Panas Cikundul** | Wisata & Rekreasi | Lembursitu | Rp 10.000 - Rp 30.000 |
| 9 | **Santa Sea Waterpark Sukabumi** | Wisata & Rekreasi | Citamiang | Rp 35.000 - Rp 50.000 |
| 10 | **Restoran & Roti Legendaris Odeon** | Kuliner Lokal | Warudoyong | Rp 25.000 - Rp 55.000 |

---

## 8. Panduan Pengujian Fitur untuk Penilai / Penguji

1. **Pengujian Frontend Publik:**
   - Buka beranda, lakukan pencarian dengan kata kunci `Alun-Alun` atau `Mochi` pada hero section.
   - Uji filter kategori pada menu atau dropdown.
   - Masuk ke salah satu destinasi untuk melihat detail informasi, jam operasional, dan tautan rute Google Maps.
2. **Pengujian Panel Admin:**
   - Klik "Login Admin" di navbar atau footer, masukkan kredensial `admin@jelajahsukabumi.id` / `admin123`.
   - Lihat statistik pada dashboard serta grafik distribusi per kategori.
   - Coba tambah destinasi baru (dengan foto, alamat, dan status *Draft* atau *Publish*).
   - Pastikan destinasi berstatus *Draft* tidak tampil kepada publik sampai diubah menjadi *Publish*.
   - Masuk ke menu Kategori, coba hapus kategori yang sedang digunakan; sistem akan menampilkan peringatan keamanan dan menolak penghapusan.
   - Masuk ke menu Laporan, gunakan filter dan klik tombol "Cetak Laporan" untuk menguji layout print-ready.
