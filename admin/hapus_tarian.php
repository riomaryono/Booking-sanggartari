<?php
include "../config/koneksi.php";

$id = $_GET['id'];

// Ambil info gambar untuk dihapus dari folder uploads
$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT gambar FROM tarian WHERE id='$id'"));

if (!empty($data['gambar']) && file_exists('../uploads/' . $data['gambar'])) {
    unlink('../uploads/' . $data['gambar']);
}

$query = "DELETE FROM tarian WHERE id='$id'";
$result = mysqli_query($koneksi, $query);

if ($result) {
    echo "<script>alert('Data Berhasil Dihapus!'); window.location='tarian.php';</script>";
} else {
    echo "<script>alert('Gagal Menghapus Data!'); window.location='tarian.php';</script>";
}
?>