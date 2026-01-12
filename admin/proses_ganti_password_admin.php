<?php
session_start();
include "../config/koneksi.php";

if (isset($_POST['update_pass'])) {
    $user_id   = $_SESSION['user_id'];
    // Ambil data dan enkripsi dengan MD5 (sesuaikan jika kamu pakai password_hash)
    $pass_lama = md5($_POST['pass_lama']);
    $pass_baru = md5($_POST['pass_baru']);

    // 1. Cek apakah password lama yang diinput sama dengan yang ada di database
    $cek_user = mysqli_query($koneksi, "SELECT password FROM users WHERE id = '$user_id'");
    $data     = mysqli_fetch_assoc($cek_user);

    if ($pass_lama === $data['password']) {
        
        // 2. Jika password lama benar, lakukan update ke password baru
        $update = mysqli_query($koneksi, "UPDATE users SET password = '$pass_baru' WHERE id = '$user_id'");

        if ($update) {
            echo "<script>
                    alert('Password berhasil diperbarui! Silakan login kembali.');
                    window.location.href = '../logout.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Gagal memperbarui password. Silakan coba lagi.');
                    window.location.href = 'profil.php';
                  </script>";
        }

    } else {
        // 3. Jika password lama salah
        echo "<script>
                alert('Password lama yang Anda masukkan salah!');
                window.location.href = 'profil.php';
              </script>";
    }
} else {
    header("location: profil.php");
}
?>