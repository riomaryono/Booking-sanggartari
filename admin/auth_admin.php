<?php
// Cek dulu, apakah session sudah jalan? Kalau belum, baru start.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek level admin
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
    header("Location: ../index.php?pesan=terlarang");
    exit();
}
?>