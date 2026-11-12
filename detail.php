<?php
/**
 * Halaman Detail Destinasi - JelajahSukabumi
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$destinasiId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($destinasiId <= 0) {
    set_flash('danger', 'Destinasi yang Anda cari tidak valid.');
    header('Location: ' . base_url('katalog.php'));
    exit;
}

// Ambil data destinasi
$destinasi = null;
try {
    $stmt = $pdo->prepare("
        SELECT d.*, k.nama_kategori 
        FROM destinasi d
        JOIN kategori k ON d.kategori_id = k.id
        WHERE d.id = :id
    ");
    $stmt->execute([':id' => $destinasiId]);
    $destinasi = $stmt->fetch();
} catch (PDOException $e) {
    $destinasi = null;
}

// Cek keberadaan dan status destinasi
$isLoggedIn = !empty($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
if (!$destinasi || ($destinasi['status'] !== 'publish' && !$isLoggedIn)) {
    set_flash('warning', 'Destinasi tidak ditemukan atau belum dipublikasikan.');
    header('Location: ' . base_url('katalog.php'));
    exit;
}

$pageTitle = $destinasi['nama_destinasi'];
$metaDescription = excerpt($destinasi['deskripsi'], 150);

// Ambil rekomendasi destinasi terkait dalam kategori yang sama (selain destinasi ini)
$rekomendasiList = [];
try {
    $stmtRek = $pdo->prepare("
        SELECT d.*, k.nama_kategori 
        FROM destinasi d
        JOIN kategori k ON d.kategori_id = k.id
        WHERE d.kategori_id = :kategori_id 
          AND d.id != :current_id 
          AND d.status = 'publish'
        ORDER BY RAND()
        LIMIT 3
    ");
    $stmtRek->execute([
        ':kategori_id' => $destinasi['kategori_id'],
        ':current_id'  => $destinasi['id']
    ]);
    $rekomendasiList = $stmtRek->fetchAll();
} catch (PDOException $e) {
    $rekomendasiList = [];
}

// Buat URL Google Maps
$googleMapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($destinasi['nama_destinasi'] . ' ' . $destinasi['alamat']);
if (!empty($destinasi['latitude']) && !empty($destinasi['longitude'])) {
    $googleMapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . $destinasi['latitude'] . ',' . $destinasi['longitude'];
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumb -->
<div class="bg-white border-bottom py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="<?= base_url('index.php') ?>" class="text-decoration-none text-muted">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('katalog.php') ?>" class="text-decoration-none text-muted">Katalog Destinasi</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('katalog.php?kategori=' . $destinasi['kategori_id']) ?>" class="text-decoration-none text-muted"><?= htmlspecialchars($destinasi['nama_kategori']) ?></a></li>
                <li class="breadcrumb-item active text-primary-custom fw-semibold" aria-current="page"><?= htmlspecialchars($destinasi['nama_destinasi']) ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        <!-- Kolom Konten Utama -->
        <div class="col-lg-8">
            <!-- Foto Utama Berukuran Besar -->
            <div class="detail-hero-img-wrap">
                <img 
                    src="<?= destinasi_img($destinasi['foto']) ?>" 
                    alt="<?= htmlspecialchars($destinasi['nama_destinasi']) ?>" 
                    class="detail-hero-img"
                >
                <div class="position-absolute top-0 start-0 m-3 d-flex gap-2">
                    <span class="badge" style="background: rgba(35, 75, 61, 0.9); font-size: 0.9rem; padding: 8px 16px; border-radius: 9999px;">
                        <i class="bi <?= get_kategori_icon($destinasi['nama_kategori']) ?> me-1"></i>
                        <?= htmlspecialchars($destinasi['nama_kategori']) ?>
                    </span>
                    <?php if ($destinasi['status'] === 'draft'): ?>
                        <span class="badge bg-warning text-dark py-2 px-3 rounded-pill fw-bold">
                            <i class="bi bi-eye-slash-fill me-1"></i> Mode Draf (Hanya Admin)
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Judul & Lokasi -->
            <div class="bg-white rounded-4 p-4 p-md-5 border shadow-sm mb-4">
                <h1 class="display-6 fw-bold text-primary-custom mb-3 font-display">
                    <?= htmlspecialchars($destinasi['nama_destinasi']) ?>
                </h1>

                <div class="d-flex align-items-start gap-2 text-muted mb-4 pb-3 border-bottom">
                    <i class="bi bi-geo-alt-fill text-brown-custom fs-5 mt-1 flex-shrink-0"></i>
                    <span class="fs-6"><?= htmlspecialchars($destinasi['alamat']) ?></span>
                </div>

                <!-- Deskripsi Lengkap -->
                <div class="destination-description mb-4">
                    <h5 class="fw-bold text-primary-custom mb-3">Deskripsi Lengkap</h5>
                    <div class="text-secondary" style="font-size: 1.05rem; line-height: 1.8;">
                        <?= nl2br(htmlspecialchars($destinasi['deskripsi'])) ?>
                    </div>
                </div>

                <!-- Catatan Verifikasi Data & Akademik -->
                <div class="p-3 rounded-3" style="background-color: var(--js-cream); border-left: 4px solid var(--js-brown);">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-patch-check-fill text-brown-custom"></i>
                        <strong class="text-primary-custom" style="font-size: 0.9rem;">Informasi Direktori Terverifikasi</strong>
                    </div>
                    <p class="mb-0 small text-muted">
                        Data tempat ini berlokasi di wilayah administratif <strong>Kota Sukabumi</strong>. Jam operasional dan kisaran biaya dapat berubah sewaktu-waktu sesuai kebijakan pengelola setempat.
                    </p>
                </div>
            </div>

            <!-- Tombol Navigasi Bawah -->
            <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between mb-5">
                <a href="<?= base_url('katalog.php') ?>" class="btn btn-outline-custom">
                    <i class="bi bi-arrow-left"></i> Kembali ke Katalog
                </a>
                <a href="<?= $googleMapsUrl ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary-custom" id="btnGoogleMapsMain">
                    <i class="bi bi-map-fill"></i> Buka Rute di Google Maps <i class="bi bi-box-arrow-up-right small ms-1"></i>
                </a>
            </div>
        </div>

        <!-- Kolom Sidebar Kanan (Informasi Penting & Quick Specs) -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 100px;">
                <!-- Card Spesifikasi & Jam Operasional -->
                <div class="detail-info-card mb-4">
                    <h5 class="fw-bold text-primary-custom mb-4 pb-2 border-bottom">
                        <i class="bi bi-info-circle-fill text-brown-custom me-2"></i>Informasi Destinasi
                    </h5>

                    <!-- Jam Operasional -->
                    <div class="spec-item">
                        <div class="spec-icon">
                            <i class="bi bi-clock-fill"></i>
                        </div>
                        <div>
                            <div class="spec-label">Jam Operasional</div>
                            <div class="spec-value">
                                <?= !empty($destinasi['jam_operasional']) ? htmlspecialchars($destinasi['jam_operasional']) : 'Informasi belum tersedia' ?>
                            </div>
                        </div>
                    </div>

                    <!-- Kisaran Biaya / Tiket -->
                    <div class="spec-item">
                        <div class="spec-icon">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <div>
                            <div class="spec-label">Kisaran Biaya / Tiket</div>
                            <div class="spec-value">
                                <?= !empty($destinasi['kisaran_biaya']) ? htmlspecialchars($destinasi['kisaran_biaya']) : 'Informasi belum tersedia' ?>
                            </div>
                        </div>
                    </div>

                    <!-- Kategori -->
                    <div class="spec-item">
                        <div class="spec-icon">
                            <i class="bi bi-folder-fill"></i>
                        </div>
                        <div>
                            <div class="spec-label">Kategori Wisata</div>
                            <div class="spec-value">
                                <?= htmlspecialchars($destinasi['nama_kategori']) ?>
                            </div>
                        </div>
                    </div>

                    <!-- Koordinat Lokasi -->
                    <div class="spec-item">
                        <div class="spec-icon">
                            <i class="bi bi-pin-map-fill"></i>
                        </div>
                        <div>
                            <div class="spec-label">Titik Koordinat</div>
                            <div class="spec-value small font-monospace">
                                <?php if (!empty($destinasi['latitude']) && !empty($destinasi['longitude'])): ?>
                                    <?= htmlspecialchars($destinasi['latitude']) ?>, <?= htmlspecialchars($destinasi['longitude']) ?>
                                <?php else: ?>
                                    <span class="text-muted fw-normal">Informasi belum tersedia</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Google Maps Aksi Cepat -->
                    <div class="d-grid mt-4">
                        <a href="<?= $googleMapsUrl ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary-custom py-2" id="btnSidebarMaps">
                            <i class="bi bi-geo-alt-fill"></i> Lihat Lokasi di Google Maps
                        </a>
                    </div>
                </div>

                <!-- Bagian Bantuan / Laporkan Data -->
                <div class="card border-0 bg-white shadow-sm p-4 rounded-4 mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary-subtle text-primary-custom rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-shield-check fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-primary-custom">Non-Komersial</h6>
                            <p class="small text-muted mb-0">Direktori ini gratis dan tidak memungut biaya dari pengunjung maupun pengelola.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
         REKOMENDASI DESTINASI TERKAIT (KATEGORI YANG SAMA)
         ============================================================ -->
    <?php if (!empty($rekomendasiList)): ?>
        <div class="mt-5 pt-4 border-top">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="section-badge">Jelajahi Serupa</span>
                    <h3 class="fw-bold text-primary-custom mb-0">Rekomendasi Terkait di Kota Sukabumi</h3>
                </div>
                <a href="<?= base_url('katalog.php?kategori=' . $destinasi['kategori_id']) ?>" class="btn btn-sm btn-outline-custom">
                    Lihat Kategori Ini <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="row g-4">
                <?php foreach ($rekomendasiList as $rek): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="destinasi-card">
                            <div class="destinasi-img-wrap">
                                <span class="destinasi-category-badge">
                                    <i class="bi <?= get_kategori_icon($rek['nama_kategori']) ?> me-1"></i>
                                    <?= htmlspecialchars($rek['nama_kategori']) ?>
                                </span>
                                <img 
                                    src="<?= destinasi_img($rek['foto']) ?>" 
                                    alt="<?= htmlspecialchars($rek['nama_destinasi']) ?>" 
                                    class="destinasi-img"
                                    loading="lazy"
                                >
                            </div>
                            <div class="destinasi-card-body">
                                <h4 class="destinasi-name h5">
                                    <a href="<?= base_url('detail.php?id=' . $rek['id']) ?>">
                                        <?= htmlspecialchars($rek['nama_destinasi']) ?>
                                    </a>
                                </h4>
                                <div class="destinasi-location">
                                    <i class="bi bi-geo-alt-fill text-brown-custom"></i>
                                    <span><?= htmlspecialchars(excerpt($rek['alamat'], 45)) ?></span>
                                </div>
                                <div class="destinasi-card-footer">
                                    <div class="destinasi-price-tag">
                                        <i class="bi bi-tag-fill"></i>
                                        <span><?= !empty($rek['kisaran_biaya']) ? htmlspecialchars($rek['kisaran_biaya']) : 'Tersedia' ?></span>
                                    </div>
                                    <a href="<?= base_url('detail.php?id=' . $rek['id']) ?>" class="btn btn-sm btn-outline-custom py-1 px-3">
                                        Detail <i class="bi bi-chevron-right small"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
