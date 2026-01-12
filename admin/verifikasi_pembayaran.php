<?php
session_start();
include "../config/koneksi.php";

// Proteksi Admin
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// Ambil data booking yang sudah upload bukti tapi belum diverifikasi
$query = "SELECT bookings.*, users.nama_lengkap, tarian.nama_tarian 
          FROM bookings 
          JOIN users ON bookings.user_id = users.id 
          JOIN tarian ON bookings.id_tarian = tarian.id 
          WHERE bookings.status_bayar = 'menunggu verifikasi' 
          ORDER BY bookings.id DESC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Pembayaran | Admin KJD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { --sidebar-dark: #1e293b; --primary: #4361ee; --soft-bg: #f8f9fc; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--soft-bg); margin: 0; overflow-x: hidden; }

        /* SIDEBAR - STYLE TETAP TIDAK BERUBAH SESUAI INSTRUKSI */
        .sidebar { width: 260px; height: 100vh; position: fixed; background: var(--sidebar-dark); padding: 1.5rem; transition: 0.3s; z-index: 1000; overflow-y: auto; }
        .sidebar-brand { color: #fff; font-weight: bold; font-size: 1.2rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 10px; }
        .sidebar a { color: #94a3b8; text-decoration: none; padding: 12px; display: flex; align-items: center; border-radius: 8px; margin-bottom: 5px; transition: 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255, 255, 255, 0.1); color: #fff; }
        .sidebar i { margin-right: 10px; }
        .sidebar.hide { transform: translateX(-260px); }
        
        .main-content { margin-left: 260px; transition: 0.3s; min-height: 100vh; }
        .main-content.full { margin-left: 0; }
        .top-nav { background: #fff; padding: 1rem 2rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; }

        /* MODERN TABLE & CARD */
        .card-verif { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); background: #fff; }
        .table thead th { background: #fcfcfd; color: #64748b; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; padding: 1.2rem; border-bottom: 1px solid #f1f5f9; }
        
        .bukti-thumb {
            width: 70px; height: 70px; object-fit: cover; border-radius: 12px; 
            cursor: pointer; transition: 0.3s; border: 2px solid #f1f5f9;
        }
        .bukti-thumb:hover { transform: scale(1.05); border-color: var(--primary); }

        .modal-content { border-radius: 24px; border: none; }
    </style>
</head>
<body>

    <?php include "../partials/sidebar.php"; ?>

    <div class="main-content" id="mainContent">
        <header class="top-nav">
            <i class="bi bi-list fs-3 me-3" style="cursor:pointer;" onclick="toggleSidebar()"></i>
            <h5 class="fw-bold mb-0">Verifikasi Pembayaran</h5>
        </header>

        <div class="container-fluid p-4">
            <div class="mb-4">
                <h2 class="fw-bold text-dark">Validasi Bukti Transfer</h2>
                <p class="text-muted">Pastikan nominal sesuai sebelum melakukan konfirmasi lunas.</p>
            </div>

            <div class="card card-verif overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Customer</th>
                                <th>Total Tagihan</th>
                                <th>Bukti Transfer</th>
                                <th>Tanggal Kirim</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark"><?= $row['nama_lengkap'] ?></div>
                                        <div class="text-primary small fw-semibold">#BK-<?= $row['id'] ?> • <?= $row['nama_tarian'] ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></div>
                                    </td>
                                    <td>
                                        <img src="../uploads/bukti_bayar/<?= $row['bukti_pembayaran'] ?>" 
                                             class="bukti-thumb shadow-sm" 
                                             onclick="showPreview('../uploads/bukti_bayar/<?= $row['bukti_pembayaran'] ?>')">
                                    </td>
                                    <td class="small text-muted">
                                        <i class="bi bi-clock me-1"></i> <?= date('d M Y, H:i', strtotime($row['tanggal_booking'])) ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="proses_verifikasi.php?id=<?= $row['id'] ?>&action=setuju" 
                                               class="btn btn-success btn-sm rounded-pill px-3 shadow-sm"
                                               onclick="return confirm('Konfirmasi pembayaran lunas?')">
                                                <i class="bi bi-check-circle me-1"></i> Terima
                                            </a>
                                            <a href="proses_verifikasi.php?id=<?= $row['id'] ?>&action=tolak" 
                                               class="btn btn-outline-danger btn-sm rounded-pill px-3"
                                               onclick="return confirm('Tolak bukti ini? Customer harus upload ulang.')">
                                                <i class="bi bi-x-circle me-1"></i> Tolak
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-check2-all fs-1 d-block mb-2"></i>
                                        Semua pembayaran sudah diverifikasi.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="previewModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold mb-0">Rincian Bukti Transfer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <img src="" id="imgPreview" class="img-fluid rounded-4 border shadow-sm">
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('hide');
            document.getElementById('mainContent').classList.toggle('full');
        }

        function showPreview(src) {
            document.getElementById('imgPreview').src = src;
            new bootstrap.Modal(document.getElementById('previewModal')).show();
        }
    </script>
</body>
</html><?php
session_start();
include "../config/koneksi.php";

// Proteksi Admin
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// Ambil data booking yang sudah upload bukti tapi belum diverifikasi
$query = "SELECT bookings.*, users.nama_lengkap, tarian.nama_tarian 
          FROM bookings 
          JOIN users ON bookings.user_id = users.id 
          JOIN tarian ON bookings.id_tarian = tarian.id 
          WHERE bookings.status_bayar = 'menunggu verifikasi' 
          ORDER BY bookings.id DESC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Pembayaran | Admin KJD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { --sidebar-dark: #1e293b; --primary: #4361ee; --soft-bg: #f8f9fc; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--soft-bg); margin: 0; overflow-x: hidden; }

        /* SIDEBAR - STYLE TETAP TIDAK BERUBAH SESUAI INSTRUKSI */
        .sidebar { width: 260px; height: 100vh; position: fixed; background: var(--sidebar-dark); padding: 1.5rem; transition: 0.3s; z-index: 1000; overflow-y: auto; }
        .sidebar-brand { color: #fff; font-weight: bold; font-size: 1.2rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 10px; }
        .sidebar a { color: #94a3b8; text-decoration: none; padding: 12px; display: flex; align-items: center; border-radius: 8px; margin-bottom: 5px; transition: 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255, 255, 255, 0.1); color: #fff; }
        .sidebar i { margin-right: 10px; }
        .sidebar.hide { transform: translateX(-260px); }
        
        .main-content { margin-left: 260px; transition: 0.3s; min-height: 100vh; }
        .main-content.full { margin-left: 0; }
        .top-nav { background: #fff; padding: 1rem 2rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; }

        /* MODERN TABLE & CARD */
        .card-verif { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); background: #fff; }
        .table thead th { background: #fcfcfd; color: #64748b; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; padding: 1.2rem; border-bottom: 1px solid #f1f5f9; }
        
        .bukti-thumb {
            width: 70px; height: 70px; object-fit: cover; border-radius: 12px; 
            cursor: pointer; transition: 0.3s; border: 2px solid #f1f5f9;
        }
        .bukti-thumb:hover { transform: scale(1.05); border-color: var(--primary); }

        .modal-content { border-radius: 24px; border: none; }
    </style>
</head>
<body>

    <?php include "../partials/sidebar.php"; ?>

    <div class="main-content" id="mainContent">
        <header class="top-nav">
            <i class="bi bi-list fs-3 me-3" style="cursor:pointer;" onclick="toggleSidebar()"></i>
            <h5 class="fw-bold mb-0">Verifikasi Pembayaran</h5>
        </header>

        <div class="container-fluid p-4">
            <div class="mb-4">
                <h2 class="fw-bold text-dark">Validasi Bukti Transfer</h2>
                <p class="text-muted">Pastikan nominal sesuai sebelum melakukan konfirmasi lunas.</p>
            </div>

            <div class="card card-verif overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Customer</th>
                                <th>Total Tagihan</th>
                                <th>Bukti Transfer</th>
                                <th>Tanggal Kirim</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark"><?= $row['nama_lengkap'] ?></div>
                                        <div class="text-primary small fw-semibold">#BK-<?= $row['id'] ?> • <?= $row['nama_tarian'] ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></div>
                                    </td>
                                    <td>
                                        <img src="../uploads/bukti_bayar/<?= $row['bukti_pembayaran'] ?>" 
                                             class="bukti-thumb shadow-sm" 
                                             onclick="showPreview('../uploads/bukti_bayar/<?= $row['bukti_pembayaran'] ?>')">
                                    </td>
                                    <td class="small text-muted">
                                        <i class="bi bi-clock me-1"></i> <?= date('d M Y, H:i', strtotime($row['tanggal_booking'])) ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="proses_verifikasi.php?id=<?= $row['id'] ?>&action=setuju" 
                                               class="btn btn-success btn-sm rounded-pill px-3 shadow-sm"
                                               onclick="return confirm('Konfirmasi pembayaran lunas?')">
                                                <i class="bi bi-check-circle me-1"></i> Terima
                                            </a>
                                            <a href="proses_verifikasi.php?id=<?= $row['id'] ?>&action=tolak" 
                                               class="btn btn-outline-danger btn-sm rounded-pill px-3"
                                               onclick="return confirm('Tolak bukti ini? Customer harus upload ulang.')">
                                                <i class="bi bi-x-circle me-1"></i> Tolak
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-check2-all fs-1 d-block mb-2"></i>
                                        Semua pembayaran sudah diverifikasi.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="previewModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold mb-0">Rincian Bukti Transfer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <img src="" id="imgPreview" class="img-fluid rounded-4 border shadow-sm">
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('hide');
            document.getElementById('mainContent').classList.toggle('full');
        }

        function showPreview(src) {
            document.getElementById('imgPreview').src = src;
            new bootstrap.Modal(document.getElementById('previewModal')).show();
        }
    </script>
</body>
</html>