<?php
/**
 * Halaman Beranda - JelajahSukabumi
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Beranda';
$metaDescription = 'Temukan Cerita di Setiap Sudut Sukabumi. Jelajahi tempat rekreasi, kuliner lokal legendaris, jejak sejarah, dan ruang publik di Kota Sukabumi.';

// Ambil Kategori dan jumlah destinasi per kategori
$kategoriList = [];
try {
    $stmtKat = $pdo->query("
        SELECT k.*, COUNT(d.id) AS total_destinasi 
        FROM kategori k
        LEFT JOIN destinasi d ON k.id = d.kategori_id AND d.status = 'publish'
        GROUP BY k.id
        ORDER BY k.id ASC
    ");
    $kategoriList = $stmtKat->fetchAll();
} catch (PDOException $e) {
    $kategoriList = [];
}

// Ambil 6 Destinasi Pilihan Teratas (status = publish)
$destinasiPilihan = [];
try {
    $stmtDest = $pdo->prepare("
        SELECT d.*, k.nama_kategori 
        FROM destinasi d
        JOIN kategori k ON d.kategori_id = k.id
        WHERE d.status = 'publish'
        ORDER BY d.id ASC
        LIMIT 6
    ");
    $stmtDest->execute();
    $destinasiPilihan = $stmtDest->fetchAll();
} catch (PDOException $e) {
    $destinasiPilihan = [];
}

// Total Statistik Destinasi
$totalDestinasi = count($destinasiPilihan);
try {
    $totalDestinasi = (int)$pdo->query("SELECT COUNT(*) FROM destinasi WHERE status = 'publish'")->fetchColumn();
} catch (PDOException $e) {}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Flash message jika ada -->
<div class="container mt-3">
    <?php display_flash(); ?>
</div>

<!-- ============================================================
     B. HERO SECTION
     ============================================================ -->
<section class="hero-section">
    <div class="hero-overlay"></div>
    <div class="container hero-content">
        <div class="row align-items-center">
            <div class="col-lg-9 col-xl-8">
                <!-- Badge Wilayah -->
                <div class="hero-badge">
                    <i class="bi bi-geo-alt-fill text-warning"></i>
                    Direktori Resmi Destinasi Lokal &bull; Kota Sukabumi
                </div>

                <!-- Judul Hero -->
                <h1 class="hero-title font-display">
                    Temukan Cerita di Setiap Sudut Sukabumi
                </h1>

                <!-- Deskripsi Hero -->
                <p class="hero-desc">
                    Jelajahi tempat menarik, kuliner lokal, ruang publik, dan jejak sejarah yang menjadikan Kota Sukabumi istimewa.
                </p>

                <!-- Search Box Hero -->
                <form action="<?= base_url('katalog.php') ?>" method="GET" class="hero-search-card" id="heroSearchForm">
                    <i class="bi bi-search fs-5 text-muted ms-2"></i>
                    <input 
                        type="text" 
                        name="q" 
                        class="hero-search-input" 
                        placeholder="Cari destinasi yang ingin kamu kunjungi..."
                        aria-label="Cari destinasi"
                        required
                        id="heroSearchInput"
                    >
                    <button type="submit" class="btn btn-primary-custom" id="btnHeroSearch">
                        <i class="bi bi-compass"></i> Mulai Jelajah
                    </button>
                </form>

                <!-- Quick tags / Rekomendasi cepat -->
                <div class="d-flex flex-wrap align-items-center gap-2 mt-4 text-white-50 small">
                    <span>Populer:</span>
                    <a href="<?= base_url('katalog.php?q=Alun-Alun') ?>" class="badge rounded-pill text-decoration-none bg-white text-dark py-2 px-3 fw-medium">Alun-Alun Sukabumi</a>
                    <a href="<?= base_url('katalog.php?q=Mochi') ?>" class="badge rounded-pill text-decoration-none bg-white text-dark py-2 px-3 fw-medium">Mochi Lampion</a>
                    <a href="<?= base_url('katalog.php?q=Gedung+Juang') ?>" class="badge rounded-pill text-decoration-none bg-white text-dark py-2 px-3 fw-medium">Gedung Juang 45</a>
                    <a href="<?= base_url('katalog.php?q=Bubur+Bunut') ?>" class="badge rounded-pill text-decoration-none bg-white text-dark py-2 px-3 fw-medium">Bubur Bunut</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     C. KATEGORI DESTINASI
     ============================================================ -->
<section class="py-5" id="kategoriSection">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-badge">Eksplorasi Tematik</span>
            <h2 class="section-title">Kategori Destinasi Kota Sukabumi</h2>
            <p class="section-subtitle">Pilih ragam pengalaman yang ingin Anda jelajahi, dari wisata air rekreasi hingga kuliner legendaris turun temurun.</p>
        </div>

        <div class="row g-4">
            <?php foreach ($kategoriList as $kat): ?>
                <div class="col-lg-3 col-md-6">
                    <a href="<?= base_url('katalog.php?kategori=' . $kat['id']) ?>" class="category-card" id="cardKategori<?= $kat['id'] ?>">
                        <div class="category-icon-box">
                            <i class="bi <?= get_kategori_icon($kat['nama_kategori']) ?>"></i>
                        </div>
                        <h3 class="category-title"><?= htmlspecialchars($kat['nama_kategori']) ?></h3>
                        <p class="category-desc"><?= htmlspecialchars(excerpt($kat['deskripsi'], 75)) ?></p>
                        <span class="badge bg-cream text-primary-custom mt-3 px-3 py-2 rounded-pill fw-semibold border">
                            <?= $kat['total_destinasi'] ?> Destinasi
                        </span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     D. DESTINASI PILIHAN (MAX 6 KARTU DARI DATABASE)
     ============================================================ -->
<section class="py-5 bg-white" id="destinasiPilihanSection">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-5">
            <div>
                <span class="section-badge">Rekomendasi Utama</span>
                <h2 class="section-title mb-0">Destinasi Pilihan di Kota Sukabumi</h2>
                <p class="text-muted-custom mt-2 mb-0">Tempat-tempat ikonik yang wajib dikunjungi saat Anda singgah di Kota Santri.</p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="<?= base_url('katalog.php') ?>" class="btn btn-outline-custom">
                    Lihat Semua Destinasi (<?= $totalDestinasi ?>) <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="row g-4">
            <?php if (!empty($destinasiPilihan)): ?>
                <?php foreach ($destinasiPilihan as $item): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="destinasi-card">
                            <div class="destinasi-img-wrap">
                                <span class="destinasi-category-badge">
                                    <i class="bi <?= get_kategori_icon($item['nama_kategori']) ?> me-1"></i>
                                    <?= htmlspecialchars($item['nama_kategori']) ?>
                                </span>
                                <img 
                                    src="<?= destinasi_img($item['foto']) ?>" 
                                    alt="<?= htmlspecialchars($item['nama_destinasi']) ?>" 
                                    class="destinasi-img"
                                    loading="lazy"
                                >
                            </div>
                            <div class="destinasi-card-body">
                                <h3 class="destinasi-name">
                                    <a href="<?= base_url('detail.php?id=' . $item['id']) ?>">
                                        <?= htmlspecialchars($item['nama_destinasi']) ?>
                                    </a>
                                </h3>
                                <div class="destinasi-location">
                                    <i class="bi bi-geo-alt-fill text-brown-custom"></i>
                                    <span><?= htmlspecialchars(excerpt($item['alamat'], 45)) ?></span>
                                </div>
                                <p class="destinasi-desc">
                                    <?= htmlspecialchars(excerpt($item['deskripsi'], 110)) ?>
                                </p>
                                <div class="destinasi-card-footer">
                                    <div class="destinasi-price-tag">
                                        <i class="bi bi-tag-fill"></i>
                                        <span><?= !empty($item['kisaran_biaya']) ? htmlspecialchars($item['kisaran_biaya']) : 'Gratis / Terjangkau' ?></span>
                                    </div>
                                    <a href="<?= base_url('detail.php?id=' . $item['id']) ?>" class="btn btn-sm btn-primary-custom py-1 px-3">
                                        Lihat Detail <i class="bi bi-chevron-right small"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center py-4 rounded-4">
                        <i class="bi bi-info-circle fs-3 d-block mb-2"></i>
                        Belum ada destinasi yang dipublikasikan saat ini.
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tombol Lihat Semua di Mobile / Bawah Grid -->
        <div class="text-center mt-5">
            <a href="<?= base_url('katalog.php') ?>" class="btn btn-primary-custom px-4 py-2" id="btnLihatSemuaDestinasi">
                <i class="bi bi-grid-3x3-gap me-2"></i> Lihat Semua Destinasi (<?= $totalDestinasi ?> Tempat)
            </a>
        </div>
    </div>
</section>

<!-- ============================================================
     E. TENTANG JELAJAH SUKABUMI
     ============================================================ -->
<section class="py-5" id="tentangSection">
    <div class="container">
        <div class="about-box">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="section-badge">Mengenal Kota Sukabumi</span>
                    <h2 class="section-title font-display mb-3">
                        Direktori Digital untuk Menemukan Pesona Lokal Sukabumi
                    </h2>
                    <p class="text-muted-custom" style="font-size: 1.05rem; line-height: 1.7;">
                        <strong>JelajahSukabumi</strong> merupakan Sistem Informasi Direktori Wisata dan Destinasi Lokal Berbasis Web yang dirancang khusus untuk memetakan serta mempromosikan tempat-tempat menarik di wilayah administratif <strong>Kota Sukabumi</strong>.
                    </p>
                    <p class="text-muted-custom" style="line-height: 1.7;">
                        Mulai dari ruang terbuka hijau ramah keluarga, cagar budaya saksi sejarah kemerdekaan dan kolonial, hingga ragam kuliner khas legendaris seperti Mochi Kaswari dan Bubur Ayam Bunut. Kami hadir untuk memudahkan masyarakat lokal dan wisatawan menemukan informasi yang akurat, terstruktur, dan terpercaya tanpa perantara transaksi komersial.
                    </p>

                    <div class="d-flex flex-wrap gap-3 mt-4">
                        <div class="badge-tag-kota">
                            <i class="bi bi-check-circle-fill text-success"></i> 100% Khusus Kota Sukabumi
                        </div>
                        <div class="badge-tag-kota">
                            <i class="bi bi-check-circle-fill text-success"></i> Informasi Terverifikasi
                        </div>
                        <div class="badge-tag-kota">
                            <i class="bi bi-check-circle-fill text-success"></i> Proyek Mahasiswa Sistem Informasi
                        </div>
                    </div>

                    <div class="mt-4 pt-2">
                        <a href="<?= base_url('tentang.php') ?>" class="btn btn-outline-custom">
                            Baca Selengkapnya Tentang Kami <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="position-relative">
                        <img 
                            src="<?= base_url('assets/images/hero_sukabumi.jpg') ?>" 
                            alt="Kota Sukabumi" 
                            class="img-fluid rounded-4 shadow-lg"
                            style="width: 100%; height: 380px; object-fit: cover;"
                        >
                        <div class="position-absolute bottom-0 start-0 m-3 p-3 bg-white rounded-3 shadow-sm border" style="max-width: 280px;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary-subtle text-primary-custom rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="bi bi-building-check fs-4"></i>
                                </div>
                                <div>
                                    <h4 class="h6 mb-0 text-primary-custom fw-bold">Kota Sukabumi</h4>
                                    <small class="text-muted">7 Kecamatan &bull; Jawa Barat</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
