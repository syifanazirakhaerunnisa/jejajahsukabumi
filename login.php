<?php
/**
 * Halaman Login Admin - JelajahSukabumi
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Jika sudah login, langsung arahkan ke dashboard admin
if (!empty($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: ' . base_url('admin/index.php'));
    exit;
}

$pageTitle = 'Login Admin';
$errors = [];

// Proses form login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

    // Validasi CSRF
    if (!verify_csrf($token)) {
        $errors[] = 'Token keamanan CSRF tidak valid. Silakan muat ulang halaman.';
    }

    // Validasi Input
    if (empty($email)) {
        $errors[] = 'Email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }

    if (empty($password)) {
        $errors[] = 'Password wajib diisi.';
    }

    // Cek Autentikasi ke Database
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Regenerate session id untuk mencegah session fixation
                session_regenerate_id(true);

                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_nama'] = $user['nama'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['logged_in'] = true;

                set_flash('success', 'Selamat datang kembali, ' . htmlspecialchars($user['nama']) . '!');
                header('Location: ' . base_url('admin/index.php'));
                exit;
            } else {
                $errors[] = 'Kombinasi email atau password tidak sesuai.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Terjadi kesalahan sistem saat memproses login.';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="py-5" style="min-height: 75vh; display: flex; align-items: center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-8">
                <!-- Card Login -->
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <!-- Header Card -->
                    <div class="p-4 text-center text-white" style="background: linear-gradient(135deg, var(--js-primary), var(--js-primary-dark));">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 58px; height: 58px; background: rgba(255,255,255,0.18);">
                            <i class="bi bi-shield-lock-fill fs-3"></i>
                        </div>
                        <h3 class="fw-bold mb-1">Masuk Panel Admin</h3>
                        <p class="small text-white-50 mb-0">Kelola Direktori Destinasi & Kategori Kota Sukabumi</p>
                    </div>

                    <!-- Body Card -->
                    <div class="card-body p-4 p-md-5 bg-white">
                        <?php display_flash(); ?>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger alert-dismissible fade show mb-4 small" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <ul class="mb-0 ps-3">
                                    <?php foreach ($errors as $err): ?>
                                        <li><?= htmlspecialchars($err) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('login.php') ?>" method="POST" autocomplete="off" id="adminLoginForm">
                            <?= csrf_field() ?>

                            <!-- Email Input -->
                            <div class="mb-3">
                                <label for="email" class="form-label small fw-bold text-muted">Email Administrator</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope-fill"></i></span>
                                    <input 
                                        type="email" 
                                        name="email" 
                                        id="email" 
                                        class="form-control border-start-0 custom-input ps-0" 
                                        placeholder="admin@jelajahsukabumi.id" 
                                        value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                                        required 
                                        autofocus
                                    >
                                </div>
                            </div>

                            <!-- Password Input -->
                            <div class="mb-4">
                                <label for="password" class="form-label small fw-bold text-muted">Kata Sandi</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-key-fill"></i></span>
                                    <input 
                                        type="password" 
                                        name="password" 
                                        id="password" 
                                        class="form-control border-start-0 custom-input ps-0" 
                                        placeholder="••••••••" 
                                        required
                                    >
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary-custom py-2" id="btnLoginSubmit">
                                    <i class="bi bi-box-arrow-in-right"></i> Masuk Sekarang
                                </button>
                            </div>
                        </form>

                        <!-- Hint Kredensial untuk Pengujian & Presentasi Akademik -->
                        <div class="mt-4 p-3 rounded-3 bg-light border">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="bi bi-info-circle-fill text-primary-custom"></i>
                                <span class="fw-bold small text-primary-custom">Akun Pengujian Demo (Akademik):</span>
                            </div>
                            <div class="small text-muted">
                                <div><strong>Email:</strong> <code>admin@jelajahsukabumi.id</code></div>
                                <div><strong>Password:</strong> <code>admin123</code></div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <a href="<?= base_url('index.php') ?>" class="text-decoration-none small text-muted">
                                <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda Publik
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
