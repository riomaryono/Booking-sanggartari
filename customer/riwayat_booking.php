<?php
session_start();
include "../config/koneksi.php"; 

$conn = $koneksi; 

if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'customer') {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$nama    = $_SESSION['nama_lengkap'];

// Query dengan JOIN tabel tarian
$query = "SELECT bookings.*, tarian.nama_tarian 
          FROM bookings 
          LEFT JOIN tarian ON bookings.id_tarian = tarian.id 
          WHERE bookings.user_id = ? 
          ORDER BY bookings.id DESC"; 

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Booking | KJD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { 
            --sidebar-dark: #1e293b; 
            --primary: #4361ee; 
            --soft-bg: #f8f9fc;
            --white: #ffffff;
            --text-main: #2d3748;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--soft-bg); 
            margin: 0; 
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* Sidebar Styling (TIDAK DIRUBAH) */
        .sidebar { width: 260px; height: 100vh; position: fixed; background: var(--sidebar-dark); padding: 1.5rem; transition: 0.3s; z-index: 1000; overflow-y: auto; }
        .sidebar-brand { color: #fff; font-weight: bold; font-size: 1.2rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 10px; }
        .sidebar a { color: #94a3b8; text-decoration: none; padding: 12px; display: flex; align-items: center; border-radius: 8px; margin-bottom: 5px; transition: 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.1); color: #fff; }
        .sidebar i { margin-right: 10px; }
        .sidebar.hide { transform: translateX(-260px); }

        .main-content { margin-left: 260px; transition: 0.3s; min-height: 100vh; }
        .main-content.full { margin-left: 0; }

        .top-nav { 
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem; 
            border-bottom: 1px solid #eef2f7; 
            display: flex; 
            align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 999;
        }

        .card-history { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); background: var(--white); overflow: hidden; }

        .table thead th {
            background-color: #fcfcfd;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.05em;
            font-weight: 700;
            color: #718096;
            padding: 1.25rem 1rem;
            border-bottom: 1px solid #edf2f7;
        }

        .table tbody td { padding: 1.25rem 1rem; vertical-align: middle; font-size: 0.85rem; border-bottom: 1px solid #edf2f7; }

        .badge-custom { padding: 0.5rem 1rem; font-weight: 600; font-size: 0.75rem; border-radius: 10px; display: inline-flex; align-items: center; gap: 6px; }
        .status-diterima { background-color: #dcfce7; color: #166534; }
        .status-ditolak { background-color: #fee2e2; color: #991b1b; }
        .status-pending { background-color: #fffbeb; color: #d97706; }
        .status-lunas { background-color: #e0f2fe; color: #0369a1; } /* Style Baru untuk Lunas */

        .price-text { font-weight: 700; color: var(--primary); }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-260px); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>

    <?php include "sidebar_customer.php"; ?>

    <div class="main-content" id="content">
        <nav class="top-nav">
            <div class="d-flex align-items-center">
                <button id="btn-hamburger" class="btn btn-light me-3">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Riwayat Pesanan</h5>
                    <p class="text-muted small mb-0">Kelola reservasi tarian Anda</p>
                </div>
            </div>
            <div class="user-info d-flex align-items-center gap-3">
                <div class="text-end d-none d-sm-block">
                    <span class="small fw-bold d-block"><?= $nama ?></span>
                    <span class="text-muted" style="font-size: 0.7rem;">Customer Account</span>
                </div>
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:40px; height:40px;">
                    <i class="bi bi-person-fill"></i>
                </div>
            </div>
        </nav>

        <div class="container-fluid p-4">
            <div class="card card-history">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Detail Tarian</th>
                                <th>Jadwal & Lokasi</th>
                                <th>Biaya Total</th>
                                <th>Status Pembayaran</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
<tbody>
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
        <?php 
            $data_json = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
            $status = strtolower($row['status']);
            $status_bayar = strtolower($row['status_bayar']); // Variabel yang benar
            
            // Logika Penentuan Badge Status
            if ($status_bayar == 'lunas') {
                $class = "status-lunas"; $icon = "bi-patch-check-fill"; $statusText = "Lunas";
            } elseif ($status_bayar == 'menunggu verifikasi') {
                $class = "status-pending"; $icon = "bi-clock-history"; $statusText = "Menunggu Verifikasi";
            } elseif ($status == 'diterima' && $status_bayar == 'belum') {
                $class = "status-pending"; $icon = "bi-wallet2"; $statusText = "Menunggu Pembayaran";
            } elseif ($status == 'ditolak') {
                $class = "status-ditolak"; $icon = "bi-x-circle-fill"; $statusText = "Ditolak";
            } else {
                $class = "status-pending"; $icon = "bi-hourglass-split"; $statusText = "Menunggu Persetujuan";
            }
        ?>
        <tr>
            <td class="ps-4">
                <div class="fw-bold text-dark"><?= $row['nama_tarian'] ?></div>
                <div class="text-muted small">#BK-<?= $row['id'] ?> | <?= $row['jenis_acara'] ?></div>
            </td>
            <td>
                <div class="small fw-medium"><i class="bi bi-calendar-event me-1"></i> <?= date('d M Y', strtotime($row['tanggal_booking'])) ?></div>
                <div class="text-primary small"><?= $row['kategori_daerah'] ?></div>
            </td>
            <td><span class="price-text">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></span></td>
            <td>
                <div class="badge-custom <?= $class ?>">
                    <i class="bi <?= $icon ?>"></i> <?= $statusText ?>
                </div>
            </td>
            <td class="text-center">
                <div class="d-flex justify-content-center gap-2">
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick='tampilDetail(<?= $data_json ?>)'>
                        <i class="bi bi-eye-fill"></i> Detail
                    </button>
                    
                    <?php if($status == 'diterima' && $status_bayar == 'belum'): ?>
                        <a href="pembayaran.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">
                            <i class="bi bi-credit-card"></i> Bayar
                        </a>
                    <?php endif; ?>

                    <?php if($status_bayar == 'lunas'): ?>
                        <a href="cetak_nota.php?id=<?= $row['id'] ?>" target="_blank" class="btn btn-sm btn-dark rounded-pill px-3">
                            <i class="bi bi-printer"></i> Nota
                        </a>
                    <?php endif; ?>

                    <?php if($status == 'pending'): ?>
                        <a href="batal_booking.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill" 
                           onclick="return confirm('Yakin ingin membatalkan pesanan ini?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="5" class="text-center py-5 text-muted">Belum ada riwayat pemesanan.</td>
        </tr>
    <?php endif; ?>
</tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold"><i class="bi bi-receipt me-2 text-primary"></i>Rincian Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" id="isiDetail"></div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar Toggle
        const btnHamburger = document.getElementById('btn-hamburger');
        const sidebar = document.querySelector('.sidebar');
        const content = document.getElementById('content');

        btnHamburger.addEventListener('click', function() {
            if (window.innerWidth > 768) {
                sidebar.classList.toggle('hide');
                content.classList.toggle('full');
            } else {
                sidebar.classList.toggle('show');
            }
        });

        function tampilDetail(data) {
            let ketAdmin = '';
            let statusBayarInfo = '';

            // Info Tambahan jika sudah Bayar
            if(data.status_bayar === 'lunas') {
                statusBayarInfo = `<div class="alert alert-success mt-3 small"><i class="bi bi-check-circle-fill me-2"></i>Pembayaran telah diverifikasi. Silakan cetak nota sebagai bukti.</div>`;
            }

            if(data.status.toLowerCase() === 'ditolak') {
                ketAdmin = `
                    <div class="mt-3 p-3 bg-danger bg-opacity-10 border-start border-4 border-danger rounded-3">
                        <small class="text-danger fw-bold d-block text-uppercase" style="font-size: 0.65rem;">Alasan Penolakan Admin:</small>
                        <p class="mb-0 small text-dark fw-medium">${data.alasan_tolak || 'Tidak ada alasan spesifik.'}</p>
                    </div>
                `;
            }

            const html = `
                <div class="d-flex align-items-center mb-4 p-3 bg-light rounded-4">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="bi bi-person-badge fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Pemesan:</small>
                        <div class="fw-bold text-dark"><?= $nama ?></div>
                        <div class="text-muted small">Booking ID: #BK-${data.id}</div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-6"><div class="p-3 border rounded-3"><small class="text-muted d-block fw-bold" style="font-size: 0.65rem;">Layanan</small><span class="fw-bold text-primary">${data.nama_tarian}</span></div></div>
                    <div class="col-6"><div class="p-3 border rounded-3"><small class="text-muted d-block fw-bold" style="font-size: 0.65rem;">Penari</small><span class="fw-bold text-dark">${data.jumlah_penari} Orang</span></div></div>
                    <div class="col-12"><div class="p-3 border rounded-3 bg-white"><small class="text-muted d-block fw-bold" style="font-size: 0.65rem;">Lokasi</small><div class="fw-bold text-dark">${data.kategori_alamat}</div><span class="badge bg-primary-subtle text-primary rounded-pill">${data.kategori_daerah}</span></div></div>
                    <div class="col-12 mt-4"><div class="p-3 bg-dark text-white rounded-4 d-flex justify-content-between align-items-center"><div><small class="text-white-50 d-block fw-bold" style="font-size: 0.65rem;">Total Biaya</small><h4 class="fw-bold mb-0 text-warning">Rp ${new Intl.NumberFormat('id-ID').format(data.total_harga)}</h4></div><i class="bi bi-wallet2 fs-2 opacity-50"></i></div></div>
                    ${ketAdmin}
                    ${statusBayarInfo}
                </div>`;
            document.getElementById('isiDetail').innerHTML = html;
            new bootstrap.Modal(document.getElementById('modalDetail')).show();
        }
    </script>
</body>
</html>