<?php
include "../config/koneksi.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Pastikan tidak menghapus admin secara tidak sengaja melalui URL
    $query = "DELETE FROM users WHERE id = '$id' AND level = 'customer'";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Akun Customer Berhasil Dihapus!'); window.location='customer.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>