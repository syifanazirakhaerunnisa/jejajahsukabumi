-- ============================================================
-- DATABASE: jelajah_sukabumi
-- Sistem Informasi Direktori Wisata dan Destinasi Lokal
-- Kota Sukabumi, Jawa Barat
-- Proyek Perangkat Lunak - Sistem Informasi
-- ============================================================

CREATE DATABASE IF NOT EXISTS `jelajah_sukabumi` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `jelajah_sukabumi`;

-- --------------------------------------------------------
-- Struktur Tabel `users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `destinasi`;
DROP TABLE IF EXISTS `kategori`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(50) NOT NULL DEFAULT 'admin',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur Tabel `kategori`
-- --------------------------------------------------------
CREATE TABLE `kategori` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_kategori` VARCHAR(100) NOT NULL,
  `deskripsi` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur Tabel `destinasi`
-- --------------------------------------------------------
CREATE TABLE `destinasi` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kategori_id` INT NOT NULL,
  `nama_destinasi` VARCHAR(150) NOT NULL,
  `alamat` TEXT NOT NULL,
  `deskripsi` TEXT NOT NULL,
  `jam_operasional` VARCHAR(100) DEFAULT NULL,
  `kisaran_biaya` VARCHAR(100) DEFAULT NULL,
  `latitude` DECIMAL(10, 8) DEFAULT NULL,
  `longitude` DECIMAL(11, 8) DEFAULT NULL,
  `foto` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('publish', 'draft') NOT NULL DEFAULT 'publish',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_destinasi_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Data Awal: `users`
-- Default Admin: admin@jelajahsukabumi.id / admin123
-- --------------------------------------------------------
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Administrator JelajahSukabumi', 'admin@jelajahsukabumi.id', '$2y$10$YkdcjGroOCdsrA2n1hg7te0p72WfOLPvUSmhRA96yv/uMrxyHG/R.', 'admin', NOW());

-- --------------------------------------------------------
-- Data Awal: `kategori`
-- --------------------------------------------------------
INSERT INTO `kategori` (`id`, `nama_kategori`, `deskripsi`, `created_at`) VALUES
(1, 'Wisata dan Rekreasi', 'Destinasi wisata rekreasi air, wahana bermain keluarga, dan tempat liburan rekreasi di Kota Sukabumi.', NOW()),
(2, 'Kuliner Lokal', 'Kekayaan cita rasa kuliner legendaris, camilan khas mochi, dan tempat makan otentik Kota Sukabumi.', NOW()),
(3, 'Sejarah dan Heritage', 'Bangunan cagar budaya, museum bersejarah, stasiun peninggalan kolonial, dan landmark religi di Kota Sukabumi.', NOW()),
(4, 'Ruang Publik', 'Alun-alun terbuka, lapangan olahraga, taman kota, dan area rekreasi santai warga di pusat Kota Sukabumi.', NOW());

-- --------------------------------------------------------
-- Data Awal: `destinasi` (10 Destinasi di Kota Sukabumi)
-- --------------------------------------------------------
INSERT INTO `destinasi` (`id`, `kategori_id`, `nama_destinasi`, `alamat`, `deskripsi`, `jam_operasional`, `kisaran_biaya`, `latitude`, `longitude`, `foto`, `status`, `created_at`) VALUES
(1, 4, 'Alun-Alun Kota Sukabumi', 'Jl. Alun-Alun Utara, Kel. Gunungparang, Kec. Cikole, Kota Sukabumi, Jawa Barat 43111', 'Alun-Alun Kota Sukabumi merupakan pusat ruang terbuka publik ramah keluarga di jantung Kota Sukabumi yang terintegrasi secara langsung dengan Masjid Agung Kota Sukabumi. Tempat ini memiliki hamparan rumput sintetis yang bersih, area bermain anak ramah difabel, bangku taman yang asri, serta menjadi pusat interaksi warga yang nyaman di pagi dan sore hari.', '24 Jam (Terbuka Umum)', 'Gratis', -6.92182000, 106.92723000, 'alun_alun.jpg', 'publish', NOW()),

(2, 3, 'Masjid Agung Kota Sukabumi', 'Jl. Alun-Alun Barat No. 2, Kel. Gunungparang, Kec. Cikole, Kota Sukabumi, Jawa Barat 43111', 'Masjid Agung Kota Sukabumi adalah ikon spiritual dan cagar budaya religi bersejarah yang telah berdiri sejak abad ke-19 (sekitar tahun 1890-an). Memiliki arsitektur megah berornamen islami modern dengan kubah bernuansa emas dan latar belakang megah Gunung Gede Pangrango. Selain sebagai sarana ibadah utama masyarakat, masjid ini menjadi landmark kebanggaan Kota Santri.', '04.00 - 21.00 WIB', 'Gratis (Infaq Sukarela)', -6.92194000, 106.92645000, 'masjid_agung.jpg', 'publish', NOW()),

(3, 3, 'Gedung Juang 45 Kota Sukabumi', 'Jl. Veteran II No. 2, Kel. Selabatu, Kec. Cikole, Kota Sukabumi, Jawa Barat 43114', 'Gedung Juang 45 merupakan bangunan cagar budaya bernilai sejarah tinggi yang telah direvitalisasi menjadi Museum Sejarah Digital Interaktif pertama di Sukabumi. Di tempat ini, pengunjung dapat mempelajari rekam jejak perjuangan para pejuang Sukabumi pada masa pergerakan nasional dan kemerdekaan dengan bantuan instalasi audiovisual modern dan diorama interaktif.', 'Selasa - Minggu (08.00 - 16.00 WIB)', 'Gratis / Donasi Sukarela (Data Demonstrasi)', -6.91782000, 106.92951000, 'gedung_juang.jpg', 'publish', NOW()),

(4, 3, 'Stasiun Sukabumi', 'Jl. Stasiun Barat No. 2, Kel. Gunungparang, Kec. Cikole, Kota Sukabumi, Jawa Barat 43111', 'Stasiun Sukabumi merupakan stasiun kereta api kelas II bersejarah peninggalan masa kolonial Belanda (Staatsspoorwegen) yang dibuka pertama kali pada tahun 1882. Bangunan utamanya mempertahankan arsitektur art deco era kolonial dengan atap tinggi dan ornamen otentik. Saat ini melayani perjalanan KA Pangrango rute Sukabumi - Bogor dan KA Siliwangi rute Sukabumi - Cipatat.', '05.00 - 21.00 WIB', 'Menyesuaikan Tiket Kereta Api', -6.92558000, 106.92671000, 'stasiun_sukabumi.jpg', 'publish', NOW()),

(5, 2, 'Pusat Mochi Kaswari Lampion Sukabumi', 'Gg. Kaswari No. 19, Kel. Selabatu, Kec. Cikole, Kota Sukabumi, Jawa Barat 43114', 'Mochi Kaswari Lampion adalah ikon oleh-oleh kuliner paling legendaris dan paling dicari di Kota Sukabumi sejak tahun 1983. Kue mochi khas Sukabumi ini dibuat dari adonan tepung ketan kenyal berbalut tepung tapioka sangrai dengan beragam varian isi, mulai dari kacang hijau tradisional, wijen, kacang merah, keju, cokelat, hingga durian dalam kemasan keranjang bambu khas.', '07.00 - 20.30 WIB', 'Rp 40.000 - Rp 60.000 / keranjang', -6.91689000, 106.93245000, 'mochi_kaswari.jpg', 'publish', NOW()),

(6, 2, 'Bubur Ayam Bunut Siliwangi', 'Jl. Siliwangi No. 93, Kel. Cikole, Kec. Cikole, Kota Sukabumi, Jawa Barat 43113', 'Bubur Ayam Bunut Siliwangi adalah destinasi sarapan dan kuliner legendaris yang sudah memanjakan lidah warga Sukabumi dan pelancong sejak tahun 1975. Menyajikan bubur gurih kental beraroma kaldu istimewa dengan taburan suwiran ayam kampung melimpah, kacang kedelai goreng, seledri, kerupuk, serta pelengkap andalan berupa risol empuk dan ati ampela.', '06.00 - 22.00 WIB', 'Rp 20.000 - Rp 35.000 / porsi', -6.91350000, 106.93320000, 'bubur_bunut.jpg', 'publish', NOW()),

(7, 4, 'Lapang Merdeka Kota Sukabumi', 'Jl. Perintis Kemerdekaan, Kel. Gunungparang, Kec. Cikole, Kota Sukabumi, Jawa Barat 43111', 'Lapang Merdeka Kota Sukabumi adalah ruang publik multifungsi yang berlokasi tepat di seberang Alun-Alun Sukabumi. Dilengkapi dengan lintasan lari (jogging track) standar sintetis berwarna biru cerah, lapangan upacara dan olahraga serbaguna, arena skatepark, dan deretan pepohonan trembesi rindang yang menjadi sarana olahraga dan rekreasi favorit keluarga.', '24 Jam (Terbuka Umum)', 'Gratis', -6.92283000, 106.92881000, 'lapang_merdeka.jpg', 'publish', NOW()),

(8, 1, 'Pemandian Air Panas Cikundul', 'Jl. Proklamasi No. 242, Kel. Cikundul, Kec. Lembursitu, Kota Sukabumi, Jawa Barat 43168', 'Pemandian Air Panas Cikundul adalah objek wisata pemandian air panas alami berhawa asri yang berada di wilayah selatan Kota Sukabumi di tepi Sungai Cimandiri. Menyediakan kolam renang air panas alami terbuka untuk umum, kamar rendam air panas privat beratap, serta fasilitas terapi relaksasi air belerang alami yang menyehatkan tubuh.', '08.00 - 22.00 WIB', 'Rp 10.000 - Rp 30.000 / orang', -6.96645000, 106.91892000, 'cikundul_hotspring.jpg', 'publish', NOW()),

(9, 1, 'Santa Sea Waterpark Sukabumi', 'Jl. Lio Santa, Kel. Gedongpanjang, Kec. Citamiang, Kota Sukabumi, Jawa Barat 43144', 'Santa Sea Waterpark adalah taman wisata air tematik keluarga terlengkap di Kota Sukabumi. Mengusung konsep petualangan bajak laut (Captain Bart) yang seru, tempat ini menyajikan aneka wahana air seperti Kolam Ombak, Water Slides berkecepatan tinggi, Kolam Arus Amazon, kolam air hangat (Japanese Hot Spring), serta wahana mandi busa yang disukai anak-anak.', 'Senin - Minggu (09.00 - 17.00 WIB)', 'Rp 35.000 - Rp 50.000 / orang', -6.94520000, 106.92480000, 'santa_sea.jpg', 'publish', NOW()),

(10, 2, 'Restoran & Roti Legendaris Odeon', 'Jl. Pajagalan No. 48, Kel. Nyomplong, Kec. Warudoyong, Kota Sukabumi, Jawa Barat 43131', 'Terletak di kawasan pecinan historis Odeon Sukabumi, tempat ini menyajikan kelezatan mie khas Sukabumi legendaris, pangsit kuah lembut, siomay, dan roti jadul hangat yang dibuat secara tradisional sejak pertengahan abad ke-20. Nuansa interior bergaya vintage memberikan pengalaman kuliner nostalgia yang khas dan otentik di Kota Sukabumi.', '08.00 - 20.00 WIB', 'Rp 25.000 - Rp 55.000', -6.92520000, 106.92340000, 'kuliner_odeon.jpg', 'publish', NOW());
