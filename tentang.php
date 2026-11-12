<?php
/**
 * Halaman Tentang Kami - JelajahSukabumi
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Tentang Kami';
$metaDescription = 'Tentang JelajahSukabumi - Sistem Informasi Direktori Wisata dan Destinasi Lokal Berbasis Web di Kota Sukabumi, Jawa Barat.';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Header Banner -->
<section class="py-5" style="background: linear-gradient(135deg, var(--js-primary), var(--js-primary-dark)); color: white;">
    <div class="container text-center py-4">
        <span class="badge mb-3 px-3 py-2" style="background: rgba(255,255,255,0.15); font-size: 0.85rem; letter-spacing: 0.05em;">
            <i class="bi bi-info-circle-fill text-warning me-1"></i> Profil Direktori Digital
        </span>
        <h1 class="display-5 fw-bold font-display text-white mb-3">Tentang JelajahSukabumi</h1>
        <p class="lead text-white-50 mx-auto" style="max-width: 680px;">
            Mengenal platform informasi destinasi lokal, sejarah, rekreasi, dan kuliner khas di wilayah Kota Sukabumi, Jawa Barat.
        </p>
    </div>
</section>

<div class="container py-5">
    <!-- Cerita & Latar Belakang -->
    <div class="row align-items-center g-5 mb-5">
        <div class="col-lg-6">
            <span class="section-badge">Latar Belakang Proyek</span>
            <h2 class="section-title font-display mb-3">
                Menghubungkan Masyarakat dengan Pesona Otentik Kota Sukabumi
            </h2>
            <p class="text-secondary" style="font-size: 1.05rem; line-height: 1.8;">
                <strong>JelajahSukabumi</strong> dikembangkan sebagai implementasi proyek akademik pada mata kuliah <strong>Proyek Perangkat Lunak</strong> bagi mahasiswa Program Studi <strong>Sistem Informasi</strong>. 
            </p>
            <p class="text-secondary" style="line-height: 1.8;">
                Kota Sukabumi memiliki kekayaan cagar budaya kolonial Belanda, tempat ibadah bersejarah, ruang terbuka publik yang baru direvitalisasi, hingga tradisi kuliner legendaris seperti kue mochi dan bubur ayam. Namun, banyak informasi destinasi masih tersebar secara sporadis dan terkadang membingungkan antara wilayah Kota dan Kabupaten.
            </p>
            <p class="text-secondary" style="line-height: 1.8;">
                Melalui sistem informasi direktori digital ini, kami berdedikasi menyajikan basis data terstruktur, akurat, dan mudah diakses bagi masyarakat lokal, pelajar, maupun wisatawan luar daerah.
            </p>
        </div>
        <div class="col-lg-6">
            <img 
                src="<?= base_url('assets/images/hero_sukabumi.jpg') ?>" 
                alt="Tentang JelajahSukabumi" 
                class="img-fluid rounded-4 shadow-md"
                style="width: 100%; height: 380px; object-fit: cover;"
            >
        </div>
    </div>

    <!-- Nilai Utama / Value Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 bg-white p-4 rounded-4 shadow-sm h-100">
                <div class="category-icon-box mb-3" style="width: 56px; height: 56px;">
                    <i class="bi bi-geo-alt-fill text-primary-custom"></i>
                </div>
                <h4 class="fw-bold text-primary-custom mb-2">Fokus Khusus Kota</h4>
                <p class="text-muted small mb-0">
                    Seluruh destinasi yang terdata berada dalam batas wilayah administratif 7 kecamatan Kota Sukabumi, memastikan informasi yang tepat sasaran dan akurat.
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 bg-white p-4 rounded-4 shadow-sm h-100">
                <div class="category-icon-box mb-3" style="width: 56px; height: 56px;">
                    <i class="bi bi-shield-lock-fill text-primary-custom"></i>
                </div>
                <h4 class="fw-bold text-primary-custom mb-2">Bukan Marketplace</h4>
                <p class="text-muted small mb-0">
                    Website ini beroperasi murni sebagai direktori informasi publik edukatif tanpa ada unsur transaksi jual-beli komersial atau pemungutan biaya.
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 bg-white p-4 rounded-4 shadow-sm h-100">
                <div class="category-icon-box mb-3" style="width: 56px; height: 56px;">
                    <i class="bi bi-laptop-fill text-primary-custom"></i>
                </div>
                <h4 class="fw-bold text-primary-custom mb-2">Arsitektur Berkelanjutan</h4>
                <p class="text-muted small mb-0">
                    Dibangun menggunakan standar PHP native berorientasi PDO dan basis data relasional MariaDB/MySQL, siap dikembangkan lebih lanjut oleh mahasiswa berikutnya.
                </p>
            </div>
        </div>
    </div>

    <!-- Visi & Misi Box -->
    <div class="about-box bg-cream-light mb-5">
        <div class="row g-4">
            <div class="col-md-6 border-end-md">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-primary-custom text-white p-2 rounded-circle"><i class="bi bi-eye-fill"></i></span>
                    <h3 class="fw-bold text-primary-custom mb-0">Visi Direktori</h3>
                </div>
                <p class="text-secondary" style="line-height: 1.8;">
                    Menjadi sistem direktori pariwisata dan destinasi lokal Kota Sukabumi yang paling terpercaya, informatif, dan mudah digunakan oleh masyarakat dan generasi muda berbasis teknologi web modern.
                </p>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-brown-custom text-white p-2 rounded-circle" style="background-color: var(--js-brown);"><i class="bi bi-bullseye"></i></span>
                    <h3 class="fw-bold text-primary-custom mb-0">Misi Utama</h3>
                </div>
                <ul class="text-secondary" style="line-height: 1.8; padding-left: 20px;">
                    <li>Mendokumentasikan destinasi sejarah, cagar budaya, rekreasi, dan kuliner khas Kota Sukabumi secara digital.</li>
                    <li>Menyediakan pencarian dan navigasi lokasi berbasis pemetaan titik koordinat Google Maps yang valid.</li>
                    <li>Memberikan wadah pengelolaan data yang terpusat melalui panel admin yang aman dan terstruktur.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Informasi Tim & Akademik -->
    <div class="card border-0 bg-white p-4 p-md-5 rounded-4 shadow-sm text-center">
        <div class="mx-auto mb-3" style="width: 64px; height: 64px; background: var(--js-primary-subtle); color: var(--js-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
            <i class="bi bi-mortarboard"></i>
        </div>
        <h3 class="fw-bold text-primary-custom mb-2 font-display">Identitas Proyek Akademik</h3>
        <p class="text-muted mx-auto mb-4" style="max-width: 600px;">
            Sistem Informasi JelajahSukabumi ini didokumentasikan dan dipresentasikan untuk pemenuhan tugas perkuliahan Program Studi <strong>Sistem Informasi</strong>.
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="<?= base_url('katalog.php') ?>" class="btn btn-primary-custom px-4 py-2">
                <i class="bi bi-compass me-1"></i> Mulai Jelajahi Destinasi
            </a>
            <a href="<?= base_url('login.php') ?>" class="btn btn-outline-custom px-4 py-2">
                <i class="bi bi-lock-fill me-1"></i> Masuk Panel Admin
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
