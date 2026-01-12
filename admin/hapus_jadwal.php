<?php
include "../config/koneksi.php";

// Pastikan ada ID yang dikirim melalui URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query untuk menghapus data berdasarkan ID
    $query = "DELETE FROM jadwal WHERE id = '$id'";

    if (mysqli_query($koneksi, $query)) {
        // Jika berhasil, munculkan alert dan balik ke halaman jadwal
        echo "<script>
                alert('Jadwal Berhasil Dihapus!');
                window.location='jadwal.php';
              </script>";
    } else {
        // Jika gagal, tampilkan pesan error
        echo "Gagal menghapus data: " . mysqli_error($koneksi);
    }
} else {
    // Jika tidak ada ID di URL, langsung balik ke halaman jadwal
    header("Location: jadwal.php");
}
?>