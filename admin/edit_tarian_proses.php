<?php
include "../config/koneksi.php";

// Ambil data dari form
$id          = $_POST['id'];
$nama_tarian = mysqli_real_escape_string($koneksi, $_POST['nama_tarian']);
$asal_daerah = mysqli_real_escape_string($koneksi, $_POST['asal_daerah']);
$deskripsi   = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

// Cek apakah ada file gambar yang diunggah
$gambar = $_FILES['gambar']['name'];

if ($gambar != "") {
    // 1. Ambil nama gambar lama dari database untuk dihapus
    $data_lama = mysqli_query($koneksi, "SELECT gambar FROM tarian WHERE id='$id'");
    $row = mysqli_fetch_assoc($data_lama);
    
    // Hapus file fisik gambar lama jika ada di folder uploads
    if ($row['gambar'] != "" && file_exists('../uploads/' . $row['gambar'])) {
        unlink('../uploads/' . $row['gambar']);
    }

    // 2. Proses upload gambar baru
    $x = explode('.', $gambar);
    $ekstensi = strtolower(end($x));
    $nama_gambar_baru = date('dmyHis') . '-' . $gambar;
    $file_tmp = $_FILES['gambar']['tmp_name'];

    move_uploaded_file($file_tmp, '../uploads/' . $nama_gambar_baru);

    // 3. Update data termasuk gambar baru
    $query = "UPDATE tarian SET 
                nama_tarian = '$nama_tarian', 
                asal_daerah = '$asal_daerah', 
                deskripsi   = '$deskripsi', 
                gambar      = '$nama_gambar_baru' 
              WHERE id = '$id'";
} else {
    // 4. Update data TANPA mengubah gambar
    $query = "UPDATE tarian SET 
                nama_tarian = '$nama_tarian', 
                asal_daerah = '$asal_daerah', 
                deskripsi   = '$deskripsi' 
              WHERE id = '$id'";
}

$result = mysqli_query($koneksi, $query);

if ($result) {
    echo "<script>alert('Data Berhasil Diperbarui!'); window.location='tarian.php';</script>";
} else {
    echo "Error: " . mysqli_error($koneksi);
}
?>