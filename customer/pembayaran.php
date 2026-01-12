<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'customer') {
    header("Location: ../index.php");
    exit;
}

$id_booking = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Ambil data booking untuk memastikan ini milik user yang login dan statusnya Diterima
$query = "SELECT bookings.*, tarian.nama_tarian 
          FROM bookings 
          JOIN tarian ON bookings.id_tarian = tarian.id 
          WHERE bookings.id = ? AND bookings.user_id = ? AND bookings.status = 'diterima'";

$stmt = $koneksi->prepare($query);
$stmt->bind_param("ii", $id_booking, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "<script>alert('Data tidak ditemukan atau belum disetujui admin.'); window.location='riwayat_booking.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Selesaikan Pembayaran | KJD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8f9fc; color: #2d3748; }
        .payment-card { border: none; border-radius: 24px; box-shadow: 0 15px 35px rgba(0,0,0,0.05); background: #fff; }
        .bank-box { background: #f1f5f9; border-radius: 16px; padding: 20px; border: 1px solid #e2e8f0; }
        .total-badge { background: #4361ee; color: #fff; padding: 25px; border-radius: 20px; }
        .upload-area { border: 2px dashed #cbd5e1; border-radius: 16px; padding: 30px; text-align: center; cursor: pointer; transition: 0.3s; }
        .upload-area:hover { border-color: #4361ee; background: #f8faff; }
        .btn-primary { background: #4361ee; border: none; border-radius: 12px; padding: 12px; font-weight: 600; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="text-center mb-4">
                <h3 class="fw-bold">Konfirmasi Pembayaran</h3>
                <p class="text-muted">Selesaikan pembayaran untuk mengamankan jadwal Anda</p>
            </div>

            <div class="card payment-card p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <small class="text-muted d-block">ID Booking</small>
                        <span class="fw-bold">#BK-<?= $data['id'] ?></span>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block">Layanan</small>
                        <span class="fw-bold text-primary"><?= $data['nama_tarian'] ?></span>
                    </div>
                </div>

                <div class="total-badge text-center mb-4">
                    <small class="opacity-75 text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 1px;">Total Transfer</small>
                    <h2 class="fw-bold mb-0">Rp <?= number_format($data['total_harga'], 0, ',', '.') ?></h2>
                </div>

                <h6 class="fw-bold mb-3"><i class="bi bi-bank me-2"></i>Rekening Pembayaran</h6>
                <div class="bank-box mb-4">
                    <div class="d-flex align-items-center">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg" width="60" class="me-3">
                        <div>
                            <div class="fw-bold text-dark">BANK BCA</div>
                            <div class="fs-5 text-primary fw-bold">1234 - 5678 - 90</div>
                            <small class="text-muted text-uppercase">A/N SANGGAR KENCANA JATI DHARMA</small>
                        </div>
                    </div>
                </div>

                <form action="proses_pembayaran.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_booking" value="<?= $data['id'] ?>">
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Upload Bukti Transfer</label>
                        <div class="upload-area" onclick="document.getElementById('fileInput').click()">
                            <i class="bi bi-cloud-arrow-up fs-1 text-primary"></i>
                            <p class="mb-0 mt-2 small text-muted" id="fileName">Klik untuk pilih gambar atau tarik file ke sini</p>
                            <input type="file" name="bukti_bayar" id="fileInput" class="d-none" accept="image/*" required onchange="updateFileName()">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 shadow">
                        Kirim Konfirmasi Pembayaran
                    </button>
                    <a href="riwayat_booking.php" class="btn btn-link w-100 text-muted mt-2 text-decoration-none small">Kembali ke Riwayat</a>
                </form>
            </div>

            <div class="alert alert-warning border-0 rounded-4" style="font-size: 0.85rem;">
                <i class="bi bi-info-circle-fill me-2"></i> <strong>Penting:</strong> Pembayaran akan diverifikasi oleh Admin dalam waktu maksimal 1x24 jam setelah bukti diunggah.
            </div>
        </div>
    </div>
</div>

<script>
    function updateFileName() {
        const input = document.getElementById('fileInput');
        const label = document.getElementById('fileName');
        if (input.files.length > 0) {
            label.innerText = "File terpilih: " + input.files[0].name;
            label.classList.add('text-primary', 'fw-bold');
        }
    }
</script>

</body>
</html>