<?php
/**
 * Logout Handler - JelajahSukabumi
 */
require_once __DIR__ . '/includes/functions.php';

// Unset semua variabel session
$_SESSION = [];

// Hapus cookie session jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Hancurkan session
session_destroy();

// Mulai session baru hanya untuk menampung pesan flash
session_start();
set_flash('success', 'Anda telah berhasil keluar dari sistem.');
header('Location: ' . base_url('login.php'));
exit;
