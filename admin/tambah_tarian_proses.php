<?php
include "../config/koneksi.php";

// Ambil data dan amankan dari SQL Injection
$nama_tarian = mysqli_real_escape_string($koneksi, $_POST['nama_tarian']);
$asal_daerah = mysqli_real_escape_string($koneksi, $_POST['asal_daerah']);
$deskripsi   = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

$gambar = $_FILES['gambar']['name'];
$nama_gambar_baru = "";

if ($gambar != "") {
    $x = explode('.', $gambar);
    $ekstensi = strtolower(end($x));
    $file_tmp = $_FILES['gambar']['tmp_name'];
    $nama_gambar_baru = date('dmyHis') . '-' . $gambar;
    
    // Pastikan folder uploads ada
    if (!is_dir('../uploads/')) {
        mkdir('../uploads/', 0777, true);
    }
    move_uploaded_file($file_tmp, '../uploads/' . $nama_gambar_baru);
}

$query = "INSERT INTO tarian (nama_tarian, asal_daerah, deskripsi, gambar) 
          VALUES ('$nama_tarian', '$asal_daerah', '$deskripsi', '$nama_gambar_baru')";

$result = mysqli_query($koneksi, $query);

if ($result) {
    echo "<script>alert('Data Berhasil Ditambah!'); window.location='tarian.php';</script>";
} else {
    // Ini akan memunculkan pesan error asli dari MySQL jika gagal
    echo "Error: " . mysqli_error($koneksi);
}
?>