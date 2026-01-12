<?php
session_start();
include "../config/koneksi.php"; 

// Pastikan hanya admin yang bisa mengakses file ini
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    // Menggunakan mysqli_real_escape_string untuk keamanan dari SQL Injection
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
    $status = mysqli_real_escape_string($koneksi, $_GET['status']);
    
    // Tangkap alasan_tolak jika ada (dikirim dari modal tolak admin)
    $alasan = isset($_GET['alasan_tolak']) ? mysqli_real_escape_string($koneksi, $_GET['alasan_tolak']) : '';

    // Update status dan alasan_tolak sekaligus
    $query_str = "UPDATE bookings SET 
                  status = '$status', 
                  alasan_tolak = '$alasan' 
                  WHERE id = '$id'";
    
    $exec_query = mysqli_query($koneksi, $query_str);

    if ($exec_query) {
        echo "<script>
            alert('Status booking berhasil diperbarui!');
            window.location.href = 'dashboard.php'; 
        </script>";
    } else {
        echo "Gagal memperbarui status: " . mysqli_error($koneksi);
    }
} else {
    // Jika diakses tanpa parameter, kembalikan ke dashboard
    header("Location: dashboard.php");
    exit;
}
?>