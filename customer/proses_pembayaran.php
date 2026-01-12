<?php
session_start();
include "../config/koneksi.php";

// 1. Proteksi Halaman
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'customer') {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_booking = $_POST['id_booking'];
    $user_id    = $_SESSION['user_id'];

    // 2. Pengaturan Upload Gambar
    $target_dir    = "../uploads/bukti_bayar/";
    $file_name     = $_FILES['bukti_bayar']['name'];
    $file_size     = $_FILES['bukti_bayar']['size'];
    $file_tmp      = $_FILES['bukti_bayar']['tmp_name'];
    $file_type     = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    // Penamaan file unik untuk menghindari file tertimpa (Contoh: BUKTI_12_928374.jpg)
    $new_file_name = "BUKTI_" . $id_booking . "_" . time() . "." . $file_type;
    $target_file   = $target_dir . $new_file_name;

    // 3. Validasi
    $errors = [];

    // Cek apakah file benar-benar gambar
    $check = getimagesize($file_tmp);
    if ($check === false) {
        $errors[] = "File yang diunggah bukan gambar.";
    }

    // Batasi tipe file (hanya jpg, jpeg, png)
    if (!in_array($file_type, ['jpg', 'jpeg', 'png'])) {
        $errors[] = "Hanya format JPG, JPEG, & PNG yang diperbolehkan.";
    }

    // Batasi ukuran file (misal maksimal 2MB)
    if ($file_size > 2097152) {
        $errors[] = "Ukuran file terlalu besar. Maksimal 2MB.";
    }

    // 4. Eksekusi Jika Tidak Ada Error
    if (empty($errors)) {
        if (move_uploaded_file($file_tmp, $target_file)) {
            
            // Update database: 
            // - status tetap 'diterima' atau bisa buat status baru 'menunggu verifikasi'
            // - simpan nama file bukti ke kolom bukti_pembayaran
            $query = "UPDATE bookings SET 
                        bukti_pembayaran = ?, 
                        status_bayar = 'menunggu verifikasi' 
                      WHERE id = ? AND user_id = ?";
            
            $stmt = $koneksi->prepare($query);
            $stmt->bind_param("sii", $new_file_name, $id_booking, $user_id);

            if ($stmt->execute()) {
                echo "<script>
                        alert('Bukti pembayaran berhasil dikirim! Mohon tunggu verifikasi admin.');
                        window.location = 'riwayat_booking.php';
                      </script>";
            } else {
                echo "Gagal memperbarui data di database.";
            }
        } else {
            echo "Gagal mengunggah gambar ke server. Pastikan folder uploads tersedia.";
        }
    } else {
        // Tampilkan pesan error
        echo "<script>
                alert('" . implode("\\n", $errors) . "');
                window.history.back();
              </script>";
    }
} else {
    header("Location: riwayat_booking.php");
}
?>