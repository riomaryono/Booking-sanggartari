<?php
session_start();
include "../config/koneksi.php";

if (isset($_POST['upload'])) {
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    
    // Cek apakah judul kosong
    if (empty(trim($judul))) {
        echo "<script>alert('Judul kegiatan wajib diisi!'); window.history.back();</script>";
        exit;
    }

    // Cek apakah ada file yang diupload
    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] == 4) {
        echo "<script>alert('Anda wajib memilih gambar!'); window.history.back();</script>";
        exit;
    }

    $foto_nama = $_FILES['foto']['name'];
    $foto_size = $_FILES['foto']['size'];
    $foto_temp = $_FILES['foto']['tmp_name'];
    
    $ekstensi_valid = ['jpg', 'jpeg', 'png', 'webp'];
    $ekstensi = strtolower(pathinfo($foto_nama, PATHINFO_EXTENSION));

    // Validasi Ekstensi
    if (!in_array($ekstensi, $ekstensi_valid)) {
        echo "<script>alert('Format file tidak didukung! Gunakan JPG/PNG/WebP.'); window.history.back();</script>";
        exit;
    }

    // Validasi Ukuran (Maks 2MB)
    if ($foto_size > 2000000) {
        echo "<script>alert('Ukuran foto terlalu besar! Maksimal 2MB.'); window.history.back();</script>";
        exit;
    }

    // Buat folder jika belum ada
    $target_dir = "../assets/img/galeri/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $nama_baru = uniqid() . "." . $ekstensi;
    $tujuan = $target_dir . $nama_baru;

    if (move_uploaded_file($foto_temp, $tujuan)) {
        $query = mysqli_query($koneksi, "INSERT INTO galeri (judul, foto) VALUES ('$judul', '$nama_baru')");
        if ($query) {
            echo "<script>alert('Foto berhasil ditambahkan ke galeri!'); window.location='galeri.php';</script>";
        }
    } else {
        echo "<script>alert('Terjadi kesalahan saat upload file.'); window.history.back();</script>";
    }
} else {
    header("location: galeri.php");
}
?>