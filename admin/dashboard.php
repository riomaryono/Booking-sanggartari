<?php
session_start();
include "../config/koneksi.php";

// Proteksi Halaman Admin
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// Ambil Data Statistik
$total_booking = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM bookings"))['total'];
$pending_booking = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM bookings WHERE status='pending'"))['total'];
$rejected_booking = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM bookings WHERE status='ditolak'"))['total'];
$total_user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users WHERE level='customer'"))['total'];

// Hitung Estimasi Pendapatan
$pendapatan_query = mysqli_query($koneksi, "SELECT SUM(total_harga) as total FROM bookings WHERE status_bayar='lunas'");
$total_pendapatan = mysqli_fetch_assoc($pendapatan_query)['total'] ?? 0;

// Query Tabel
$query_str = "SELECT b.*, 
                     u.nama_lengkap AS nama_customer, 
                     u.username AS username_customer,
                     u.no_telp AS telp_customer, 
                     t.nama_tarian AS jenis_tarian 
              FROM bookings b 
              JOIN users u ON b.user_id = u.id 
              JOIN tarian t ON b.id_tarian = t.id 
              ORDER BY b.id DESC LIMIT 10";
$data = mysqli_query($koneksi, $query_str);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin | KJD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root { --sidebar-dark: #1e293b; --primary: #4361ee; --soft-bg: #f8f9fc; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--soft-bg); margin: 0; overflow-x: hidden; }

        /* SIDEBAR TETAP */
        .sidebar { width: 260px; height: 100vh; position: fixed; background: var(--sidebar-dark); padding: 1.5rem; transition: 0.3s; z-index: 1000; overflow-y: auto; }
        .sidebar-brand { color: #fff; font-weight: bold; font-size: 1.2rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 10px; }
        .sidebar a { color: #94a3b8; text-decoration: none; padding: 12px; display: flex; align-items: center; border-radius: 8px; margin-bottom: 5px; transition: 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255, 255, 255, 0.1); color: #fff; }
        .sidebar i { margin-right: 10px; }
        .sidebar.hide { transform: translateX(-260px); }
        
        .main-content { margin-left: 260px; transition: 0.3s; min-height: 100vh; }
        .main-content.full { margin-left: 0; }
        .top-nav { background: #fff; padding: 1rem 2rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; }

        .card-stat { border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: 0.3s; }
        .icon-box { width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        .card-custom { background: #fff; border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .status-badge { padding: 5px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        
        .bg-pending { background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; }
        .bg-success-soft { background: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }
        .bg-danger-soft { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }
        .bg-belum { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

        .action-group .btn { width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; }
    </style>
</head>
<body>

    <?php include "../partials/sidebar.php"; ?>

    <div class="main-content" id="mainContent">
        <header class="top-nav">
            <i class="bi bi-list fs-3 me-3" style="cursor:pointer;" id="btn-hamburger"></i>
            <h5 class="fw-bold mb-0">Dashboard Overview</h5>
        </header>

        <div class="container-fluid p-4">
            <div class="row g-3 mb-4">
                <div class="col-md">
                    <div class="card card-stat p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-primary bg-opacity-10 text-primary me-3"><i class="bi bi-calendar-event"></i></div>
                            <div><small class="text-muted d-block">Total Booking</small><h5 class="fw-bold mb-0"><?= $total_booking ?></h5></div>
                        </div>
                    </div>
                </div>
                <div class="col-md">
                    <div class="card card-stat p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-warning bg-opacity-10 text-warning me-3"><i class="bi bi-hourglass-split"></i></div>
                            <div><small class="text-muted d-block">Pending</small><h5 class="fw-bold mb-0"><?= $pending_booking ?></h5></div>
                        </div>
                    </div>
                </div>
                <div class="col-md">
                    <div class="card card-stat p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-danger bg-opacity-10 text-danger me-3"><i class="bi bi-x-circle"></i></div>
                            <div><small class="text-muted d-block">Ditolak</small><h5 class="fw-bold mb-0"><?= $rejected_booking ?></h5></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-stat p-3 bg-success text-white">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-white bg-opacity-20 text-white me-3"><i class="bi bi-wallet2"></i></div>
                            <div><small class="text-white-50 d-block">Omzet Lunas</small><h5 class="fw-bold mb-0">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></h5></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-custom p-4">
                <h6 class="fw-bold mb-4">10 Booking Terbaru</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr class="text-muted small">
                                <th>CUSTOMER</th>
                                <th>LAYANAN</th>
                                <th>JADWAL</th>
                                <th class="text-center">STATUS BAYAR</th>
                                <th class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($b = mysqli_fetch_assoc($data)): 
                                $data_json = htmlspecialchars(json_encode($b), ENT_QUOTES, 'UTF-8');
                                $st_bayar = $b['status_bayar'];
                                $st_booking = strtolower($b['status']);

                                // Logika Warna Status
                                if($st_booking == 'ditolak') {
                                    $badge_class = "bg-danger-soft"; $status_label = "Ditolak";
                                } elseif($st_bayar == 'lunas') {
                                    $badge_class = "bg-success-soft"; $status_label = "Lunas";
                                } elseif($st_bayar == 'menunggu verifikasi') {
                                    $badge_class = "bg-pending"; $status_label = "Perlu Verifikasi";
                                } else {
                                    $badge_class = "bg-belum"; $status_label = "Belum Bayar";
                                }
                            ?>
                            <tr>
                                <td><strong><?= $b['nama_customer'] ?></strong><br><small class="text-muted">#BK-<?= $b['id'] ?></small></td>
                                <td><span class="badge bg-light text-dark border"><?= $b['jenis_tarian'] ?></span></td>
                                <td><small><?= date('d/m/Y', strtotime($b['tanggal_booking'])) ?><br><?= substr($b['jam_mulai'],0,5) ?> WIB</small></td>
                                <td class="text-center"><span class="status-badge <?= $badge_class ?>"><?= $status_label ?></span></td>
                                <td class="text-center">
                                    <div class="action-group d-flex justify-content-center gap-1">
                                        <button class="btn btn-sm btn-outline-primary" onclick='tampilDetailLengkap(<?= $data_json ?>)'>
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <?php if($st_booking == 'pending'): ?>
                                            <a href="update_status_booking.php?id=<?= $b['id'] ?>&status=diterima" class="btn btn-sm btn-success" onclick="return confirm('Terima?')"><i class="bi bi-check-lg"></i></a>
                                            <button class="btn btn-sm btn-danger" onclick="bukaModalTolak(<?= $b['id'] ?>)"><i class="bi bi-x-lg"></i></button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetailBooking" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow" style="border-radius: 15px;">
                <div class="modal-header border-0 bg-primary text-white" style="border-radius: 15px 15px 0 0;">
                    <h5 class="fw-bold mb-0"><i class="bi bi-person-badge me-2"></i>Detail Reservasi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" id="contentDetailLengkap"></div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTolakBooking" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="update_status_booking.php" method="GET" class="modal-content border-0 shadow" style="border-radius: 15px;">
                <div class="modal-header border-0 pb-0"><h5 class="fw-bold text-danger">Tolak Pesanan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="id_tolak">
                    <input type="hidden" name="status" value="ditolak">
                    <label class="form-label small fw-bold">Alasan Penolakan</label>
                    <textarea name="alasan_tolak" class="form-control" rows="3" required placeholder="Tulis alasan..."></textarea>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Tolak Pesanan</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Sidebar
        const btnHamburger = document.getElementById('btn-hamburger');
        btnHamburger.addEventListener('click', () => {
            document.querySelector('.sidebar').classList.toggle('hide');
            document.getElementById('mainContent').classList.toggle('full');
        });

        function tampilDetailLengkap(data) {
            const waLink = data.telp_customer ? data.telp_customer.replace(/\D/g,'') : '';
            const html = `
                <div class="row g-4">
                    <div class="col-md-6 border-end">
                        <h6 class="fw-bold text-primary mb-3">Identitas Customer</h6>
                        <p class="mb-1 small text-muted">Nama: <b>${data.nama_customer}</b></p>
                        <p class="mb-1 small text-muted">WhatsApp: <b class="text-success">${data.telp_customer}</b></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold text-primary mb-3">Info Reservasi</h6>
                        <p class="mb-1 small text-muted">Tarian: <b>${data.jenis_tarian}</b></p>
                        <p class="mb-1 small text-muted">Total: <b class="text-danger text-end">Rp ${new Intl.NumberFormat('id-ID').format(data.total_harga)}</b></p>
                    </div>
                </div>`;
            document.getElementById('contentDetailLengkap').innerHTML = html;
            new bootstrap.Modal(document.getElementById('modalDetailBooking')).show();
        }

        function bukaModalTolak(id) {
            document.getElementById('id_tolak').value = id;
            new bootstrap.Modal(document.getElementById('modalTolakBooking')).show();
        }
    </script>
</body>
</html>