<?php
/**
 * Halaman Katalog Destinasi - JelajahSukabumi
 * Dilengkapi Live Search & Filter Instan berbasis AJAX
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Katalog Destinasi';
$metaDescription = 'Jelajahi seluruh direktori destinasi wisata, kuliner legendaris, sejarah cagar budaya, dan ruang publik di Kota Sukabumi.';

// Ambil semua kategori untuk filter dropdown
$kategoriList = [];
try {
    $kategoriList = $pdo->query("SELECT * FROM kategori ORDER BY id ASC")->fetchAll();
} catch (PDOException $e) {
    $kategoriList = [];
}

// Filter params
$searchKeyword = isset($_GET['q']) ? trim($_GET['q']) : '';
$selectedKategori = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;
$selectedWilayah = isset($_GET['wilayah']) ? trim($_GET['wilayah']) : '';
$selectedSort = isset($_GET['sort']) ? trim($_GET['sort']) : 'terbaru';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 6;
$offset = ($page - 1) * $perPage;

// Bangun query dinamis
$where = ["d.status = 'publish'"];
$params = [];

if (!empty($searchKeyword)) {
    $where[] = "(d.nama_destinasi LIKE :q1 OR d.deskripsi LIKE :q2 OR d.alamat LIKE :q3)";
    $params[':q1'] = '%' . $searchKeyword . '%';
    $params[':q2'] = '%' . $searchKeyword . '%';
    $params[':q3'] = '%' . $searchKeyword . '%';
}

if ($selectedKategori > 0) {
    $where[] = "d.kategori_id = :kategori";
    $params[':kategori'] = $selectedKategori;
}

if (!empty($selectedWilayah)) {
    $where[] = "d.alamat LIKE :wilayah";
    $params[':wilayah'] = '%' . $selectedWilayah . '%';
}

$whereClause = implode(' AND ', $where);

// Sorting
$orderBy = "d.id DESC";
if ($selectedSort === 'nama_asc') {
    $orderBy = "d.nama_destinasi ASC";
} elseif ($selectedSort === 'nama_desc') {
    $orderBy = "d.nama_destinasi DESC";
} elseif ($selectedSort === 'terlama') {
    $orderBy = "d.id ASC";
}

// Hitung total destinasi yang cocok
$totalItems = 0;
try {
    $countSql = "SELECT COUNT(*) FROM destinasi d WHERE {$whereClause}";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalItems = (int)$countStmt->fetchColumn();
} catch (PDOException $e) {
    $totalItems = 0;
}

$totalPages = max(1, ceil($totalItems / $perPage));
if ($page > $totalPages && $totalItems > 0) {
    $page = $totalPages;
    $offset = ($page - 1) * $perPage;
}

// Ambil destinasi per halaman
$destinasiList = [];
try {
    $sql = "
        SELECT d.*, k.nama_kategori 
        FROM destinasi d
        JOIN kategori k ON d.kategori_id = k.id
        WHERE {$whereClause}
        ORDER BY {$orderBy}
        LIMIT :limit OFFSET :offset
    ";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    $destinasiList = $stmt->fetchAll();
} catch (PDOException $e) {
    $destinasiList = [];
}

// List Kecamatan di Kota Sukabumi untuk filter wilayah
$kecamatanList = [
    'Cikole',
    'Citamiang',
    'Lembursitu',
    'Warudoyong',
    'Baros',
    'Gunungpuyuh',
    'Cibeureum'
];

$hasActiveFilter = (!empty($searchKeyword) || $selectedKategori > 0 || !empty($selectedWilayah) || $selectedSort !== 'terbaru');

/**
 * Render Kartu Destinasi (Grid)
 */
function render_katalog_cards($destinasiList) {
    ob_start();
    if (!empty($destinasiList)):
        foreach ($destinasiList as $item): ?>
            <div class="col-lg-4 col-md-6 destinasi-col">
                <div class="destinasi-card h-100">
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
                            <span><?= htmlspecialchars(excerpt($item['alamat'], 50)) ?></span>
                        </div>
                        <p class="destinasi-desc">
                            <?= htmlspecialchars(excerpt($item['deskripsi'], 120)) ?>
                        </p>
                        <div class="destinasi-card-footer">
                            <div class="destinasi-price-tag">
                                <i class="bi bi-tag-fill"></i>
                                <span><?= !empty($item['kisaran_biaya']) ? htmlspecialchars($item['kisaran_biaya']) : 'Gratis / Fleksibel' ?></span>
                            </div>
                            <a href="<?= base_url('detail.php?id=' . $item['id']) ?>" class="btn btn-sm btn-primary-custom py-1 px-3">
                                Lihat Detail <i class="bi bi-chevron-right small"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach;
    else: ?>
        <div class="col-12">
            <div class="empty-state bg-white rounded-4 shadow-sm border py-5 text-center">
                <div class="empty-state-icon mb-3">
                    <i class="bi bi-search text-muted fs-1"></i>
                </div>
                <h3 class="fw-bold text-primary-custom mb-2">Destinasi Tidak Ditemukan</h3>
                <p class="text-muted mx-auto mb-4" style="max-width: 480px;">
                    Maaf, tidak ada destinasi yang cocok dengan pencarian kata kunci atau filter yang Anda pilih. Silakan coba kata kunci lain atau reset filter.
                </p>
                <button type="button" class="btn btn-primary-custom px-4 py-2" id="btnResetEmptyState">
                    <i class="bi bi-arrow-clockwise me-1"></i> Tampilkan Semua Destinasi
                </button>
            </div>
        </div>
    <?php endif;
    return ob_get_clean();
}

/**
 * Render Navigasi Pagination
 */
function render_katalog_pagination($page, $totalPages, $queryParams) {
    if ($totalPages <= 1) return '';
    unset($queryParams['ajax']);
    ob_start(); ?>
    <nav class="mt-5" aria-label="Navigasi Halaman Katalog">
        <ul class="pagination pagination-custom justify-content-center">
            <!-- Tombol Previous -->
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <?php
                $prevParams = $queryParams;
                $prevParams['page'] = max(1, $page - 1);
                $prevUrl = base_url('katalog.php?' . http_build_query($prevParams));
                ?>
                <a class="page-link page-link-ajax" href="<?= $prevUrl ?>" data-page="<?= max(1, $page - 1) ?>" aria-label="Sebelumnya">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>

            <!-- Nomor Halaman -->
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php
                $pageParams = $queryParams;
                $pageParams['page'] = $i;
                $pageUrl = base_url('katalog.php?' . http_build_query($pageParams));
                ?>
                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                    <a class="page-link page-link-ajax" href="<?= $pageUrl ?>" data-page="<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <!-- Tombol Next -->
            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                <?php
                $nextParams = $queryParams;
                $nextParams['page'] = min($totalPages, $page + 1);
                $nextUrl = base_url('katalog.php?' . http_build_query($nextParams));
                ?>
                <a class="page-link page-link-ajax" href="<?= $nextUrl ?>" data-page="<?= min($totalPages, $page + 1) ?>" aria-label="Selanjutnya">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
    <?php
    return ob_get_clean();
}

/**
 * Render Info Jumlah Destinasi
 */
function render_katalog_count_info($totalItems, $hasActiveFilter) {
    ob_start(); ?>
    Ditemukan <strong class="text-primary-custom" id="textTotalItems"><?= $totalItems ?></strong> destinasi yang sesuai kriteria.
    <?php if ($hasActiveFilter): ?>
        <span class="badge bg-cream text-dark ms-2 border" id="badgeFilterAktif">Filter Aktif</span>
    <?php endif;
    return ob_get_clean();
}

// ============================================================
// RESPON AJAX JSON: JIKA PERMINTAAN DILAKUKAN VIA FETCH AJAX
// ============================================================
if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => 'success',
        'totalItems' => $totalItems,
        'hasActiveFilter' => $hasActiveFilter,
        'countHtml' => render_katalog_count_info($totalItems, $hasActiveFilter),
        'cardsHtml' => render_katalog_cards($destinasiList),
        'paginationHtml' => render_katalog_pagination($page, $totalPages, $_GET),
        'page' => $page,
        'totalPages' => $totalPages
    ]);
    exit;
}

// Render Header Publik Standar
require_once __DIR__ . '/includes/header.php';
?>

<!-- Header Katalog Banner -->
<section class="py-5" style="background: linear-gradient(135deg, var(--js-primary), var(--js-primary-dark)); color: white;">
    <div class="container text-center py-4">
        <span class="badge mb-3 px-3 py-2" style="background: rgba(255,255,255,0.15); font-size: 0.85rem; letter-spacing: 0.05em;">
            <i class="bi bi-geo-alt-fill text-warning me-1"></i> Direktori Resmi Kota Sukabumi
        </span>
        <h1 class="display-5 fw-bold font-display text-white mb-3">Katalog Destinasi</h1>
        <p class="lead text-white-50 mx-auto" style="max-width: 650px;">
            Temukan tempat rekreasi, kuliner khas, cagar budaya sejarah, dan ruang terbuka hijau terbaik di Kota Sukabumi.
        </p>
    </div>
</section>

<div class="container py-5" id="katalogContainer">
    <!-- Card Filter & Pencarian -->
    <div class="catalog-filter-card mb-4 shadow-sm">
        <!-- Quick Filter Category Pills -->
        <div class="quick-category-pills mb-3 d-flex align-items-center gap-2 overflow-auto pb-1" id="quickCategoryPills">
            <span class="text-muted small fw-semibold text-nowrap me-1"><i class="bi bi-funnel"></i> Kategori Cepat:</span>
            <?php
            $totalSemuaPublish = 0;
            try {
                $totalSemuaPublish = (int)$pdo->query("SELECT COUNT(*) FROM destinasi WHERE status = 'publish'")->fetchColumn();
            } catch (Exception $e) {}
            ?>
            <button type="button" class="filter-pill <?= ($selectedKategori == 0) ? 'active' : '' ?>" data-kategori-id="0">
                Semua (<?= $totalSemuaPublish ?>)
            </button>
            <?php foreach ($kategoriList as $kat): 
                $countKat = 0;
                try {
                    $stmtCK = $pdo->prepare("SELECT COUNT(*) FROM destinasi WHERE kategori_id = :kid AND status = 'publish'");
                    $stmtCK->execute([':kid' => $kat['id']]);
                    $countKat = (int)$stmtCK->fetchColumn();
                } catch (Exception $e) {}
            ?>
                <button type="button" class="filter-pill <?= ($selectedKategori == $kat['id']) ? 'active' : '' ?>" data-kategori-id="<?= $kat['id'] ?>">
                    <i class="bi <?= get_kategori_icon($kat['nama_kategori']) ?> me-1"></i>
                    <?= htmlspecialchars($kat['nama_kategori']) ?> (<?= $countKat ?>)
                </button>
            <?php endforeach; ?>
        </div>

        <form action="<?= base_url('katalog.php') ?>" method="GET" id="filterForm">
            <div class="row g-3 align-items-center">
                <!-- Kolom Search Input dengan Clear Button & Live Spinner -->
                <div class="col-lg-4 col-md-6">
                    <label class="form-label small fw-bold text-muted mb-1" for="katalogSearchInput">Cari Kata Kunci</label>
                    <div class="input-group search-input-group position-relative">
                        <span class="input-group-text bg-white border-end-0 text-muted" id="searchIconWrap">
                            <i class="bi bi-search" id="searchDefaultIcon"></i>
                            <span class="spinner-border spinner-border-sm text-primary-custom d-none" id="searchSpinner" role="status"></span>
                        </span>
                        <input 
                            type="text" 
                            name="q" 
                            id="katalogSearchInput"
                            class="form-control border-start-0 border-end-0 custom-input ps-0" 
                            placeholder="Ketik nama, kuliner, lokasi..." 
                            value="<?= htmlspecialchars($searchKeyword) ?>"
                            autocomplete="off"
                        >
                        <button type="button" class="btn btn-white border border-start-0 text-muted search-clear-btn <?= empty($searchKeyword) ? 'd-none' : '' ?>" id="searchClearBtn" title="Hapus kata kunci">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    </div>
                </div>

                <!-- Filter Kategori Dropdown -->
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small fw-bold text-muted mb-1" for="selectKategori">Kategori Destinasi</label>
                    <select name="kategori" id="selectKategori" class="form-select custom-select">
                        <option value="0">-- Semua Kategori --</option>
                        <?php foreach ($kategoriList as $kat): ?>
                            <option value="<?= $kat['id'] ?>" <?= ($selectedKategori == $kat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($kat['nama_kategori']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filter Kecamatan / Wilayah -->
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small fw-bold text-muted mb-1" for="selectWilayah">Wilayah / Kecamatan</label>
                    <select name="wilayah" id="selectWilayah" class="form-select custom-select">
                        <option value="">-- Semua Wilayah Kota --</option>
                        <?php foreach ($kecamatanList as $kec): ?>
                            <option value="<?= $kec ?>" <?= ($selectedWilayah === $kec) ? 'selected' : '' ?>>
                                Kec. <?= $kec ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Urutkan / Sort -->
                <div class="col-lg-2 col-md-6">
                    <label class="form-label small fw-bold text-muted mb-1" for="selectSort">Urutkan</label>
                    <select name="sort" id="selectSort" class="form-select custom-select">
                        <option value="terbaru" <?= ($selectedSort === 'terbaru') ? 'selected' : '' ?>>Terbaru</option>
                        <option value="nama_asc" <?= ($selectedSort === 'nama_asc') ? 'selected' : '' ?>>Nama (A - Z)</option>
                        <option value="nama_desc" <?= ($selectedSort === 'nama_desc') ? 'selected' : '' ?>>Nama (Z - A)</option>
                        <option value="terlama" <?= ($selectedSort === 'terlama') ? 'selected' : '' ?>>Terlama</option>
                    </select>
                </div>

                <!-- Tombol Submit & Reset Filter -->
                <div class="col-12 d-flex flex-wrap align-items-center justify-content-between pt-2 border-top mt-3">
                    <div class="text-muted small" id="katalogCountInfo">
                        <?= render_katalog_count_info($totalItems, $hasActiveFilter) ?>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 <?= !$hasActiveFilter ? 'd-none' : '' ?>" id="btnResetFilter">
                            <i class="bi bi-x-circle me-1"></i> Reset Filter
                        </button>
                        <button type="submit" class="btn btn-sm btn-primary-custom rounded-pill px-4 py-2" id="btnSubmitFilter">
                            <i class="bi bi-funnel-fill me-1"></i> Terapkan Filter
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Anchor scroll point -->
    <div id="katalogTop"></div>

    <!-- Loading Shimmer / Indicator -->
    <div id="katalogLoading" class="text-center py-4 d-none">
        <div class="spinner-border text-primary-custom" role="status" style="width: 2.5rem; height: 2.5rem;">
            <span class="visually-hidden">Memuat...</span>
        </div>
        <p class="text-muted small mt-2 mb-0">Menemukan destinasi terbaik Kota Sukabumi...</p>
    </div>

    <!-- Grid Destinasi -->
    <div class="row g-4 transition-all" id="katalogGrid">
        <?= render_katalog_cards($destinasiList) ?>
    </div>

    <!-- Pagination Container -->
    <div id="katalogPagination">
        <?= render_katalog_pagination($page, $totalPages, $_GET) ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
