<?php
/**
 * ==========================
 * FILE LOGOUT
 * ==========================
 * Menghapus session user/admin
 * Redirect ke halaman index.php
 */

session_start();

/* Hapus semua data session */
$_SESSION = [];
session_unset();
session_destroy();

/* Cegah cache halaman sebelumnya */
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

/* Redirect ke halaman utama */
header("Location: index.php");
exit;
