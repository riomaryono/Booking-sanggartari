<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'customer') {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$nama    = $_SESSION['nama_lengkap'];

// Ambil riwayat booking terbaru (JOIN dengan tarian menggunakan id_tarian)
$query_booking = mysqli_query($koneksi, "SELECT b.*, t.nama_tarian 
                                        FROM bookings b 
                                        LEFT JOIN tarian t ON b.id_tarian = t.id 
                                        WHERE b.user_id = '$user_id' 
                                        ORDER BY b.id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Customer | Sanggar KJD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root { --sidebar-dark: #1e293b; --primary: #4361ee; --soft-bg: #f8f9fc; }
        body { font-family: 'Inter', sans-serif; background: var(--soft-bg); margin: 0; overflow-x: hidden; }

        /* Sidebar Sesuai Style Anda */
        .sidebar { width: 260px; height: 100vh; position: fixed; background: var(--sidebar-dark); padding: 1.5rem; transition: 0.3s; z-index: 1000; overflow-y: auto; }
        .sidebar-brand { color: #fff; font-weight: bold; font-size: 1.2rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 10px; }
        .sidebar a { color: #94a3b8; text-decoration: none; padding: 12px; display: flex; align-items: center; border-radius: 8px; margin-bottom: 5px; transition: 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.1); color: #fff; }
        .sidebar i { margin-right: 10px; }
        .sidebar.hide { transform: translateX(-260px); }

        .main-content { margin-left: 260px; transition: 0.3s; min-height: 100vh; }
        .main-content.full { margin-left: 0; }

        .top-nav { background: #fff; padding: 0.8rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 999; }
        #btn-hamburger { background: #f1f5f9; border: none; padding: 8px 12px; border-radius: 8px; color: var(--sidebar-dark); }

        .hero-user { background: linear-gradient(135deg, var(--primary), #4895ef); color: white; padding: 2.5rem; border-radius: 1.5rem; }
        .card-custom { background: #fff; border: none; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }

        .status-badge { padding: 5px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        .bg-pending { background: #fef3c7; color: #92400e; }
        .bg-success-soft { background: #dcfce7; color: #166534; }
        .bg-danger-soft { background: #fee2e2; color: #991b1b; }

        @media (max-width: 768px) { .sidebar { margin-left: -260px; } .sidebar.show { margin-left: 0; } .main-content { margin-left: 0; } }
    </style>
</head>
<body>

    <?php include "sidebar_customer.php"; ?>

    <div class="main-content" id="content">
        <nav class="top-nav">
            <div class="d-flex align-items-center">
                <button id="btn-hamburger"><i class="bi bi-list fs-5"></i></button>
                <h6 class="mb-0 ms-3 fw-bold text-secondary">Dashboard</h6>
            </div>
            <div class="user-profile d-flex align-items-center">
                <span class="fw-bold me-2 d-none d-sm-block"><?= $nama ?></span>
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:40px; height:40px;"><i class="bi bi-person-fill"></i></div>
            </div>
        </nav>

        <div class="container-fluid p-4">
            <div class="hero-user mb-4">
                <h2 class="fw-bold">Halo, <?= $nama ?>! 👋</h2>
                <p class="opacity-75">Kelola jadwal tarian dan lihat riwayat pesanan Anda.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card card-custom p-4 text-center">
                        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width:60px; height:60px; background:#eef2ff;"><i class="bi bi-calendar-plus text-primary fs-3"></i></div>
                        <h5>Ingin Latihan?</h5>
                        <a href="buat_booking.php" class="btn btn-primary w-100 fw-bold py-2 rounded-pill mt-3">BUAT BOOKING</a>
                    </div>
                </div>
               <div class="col-md-8">
    <div class="card card-custom border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Aktivitas Terakhir</h5>
                <a href="riwayat_booking.php" class="btn btn-sm btn-light text-primary fw-bold px-3">Lihat Semua</a>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 rounded-start">Detail Pesanan</th>
                            <th class="border-0">Jadwal Tampil</th>
                            <th class="border-0 text-center">Status</th>
                            <th class="border-0 rounded-end text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php 
    mysqli_data_seek($query_booking, 0); 
    if(mysqli_num_rows($query_booking) > 0):
        while($row = mysqli_fetch_assoc($query_booking)): 
            // Ambil status dan status bayar
            $s = strtolower($row['status']);
            $sb = strtolower($row['status_bayar']);
            
            // LOGIKA SINKRONISASI (Sama dengan riwayat_booking.php)
            if ($sb == 'lunas') {
                $badge = "bg-success-soft"; $icon = "bi-patch-check-fill"; $statusText = "Lunas";
            } elseif ($sb == 'menunggu verifikasi') {
                $badge = "bg-warning-soft"; $icon = "bi-clock-history"; $statusText = "Verifikasi";
            } elseif ($s == 'diterima' && $sb == 'belum') {
                $badge = "bg-warning-soft"; $icon = "bi-wallet2"; $statusText = "Bayar";
            } elseif ($s == 'ditolak') {
                $badge = "bg-danger-soft"; $icon = "bi-x-circle-fill"; $statusText = "Ditolak";
            } else {
                $badge = "bg-pending"; $icon = "bi-hourglass-split"; $statusText = "Pending";
            }
            
            $data_json = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
    ?>
    <tr>
        <td>
            <div class="fw-bold text-dark"><?= $row['nama_tarian'] ?></div>
            <small class="text-muted"><i class="bi bi-tag me-1"></i><?= $row['jenis_acara'] ?></small>
        </td>
        <td>
            <div class="small fw-medium"><i class="bi bi-calendar3 me-2 text-primary"></i><?= date('d M Y', strtotime($row['tanggal_booking'])) ?></div>
            <div class="small text-muted"><i class="bi bi-clock me-2"></i><?= substr($row['jam_mulai'], 0, 5) ?> WIB</div>
        </td>
        <td class="text-center">
            <span class="status-badge <?= $badge ?> d-inline-flex align-items-center gap-1">
                <i class="bi <?= $icon ?> font-size-sm"></i>
                <?= $statusText ?>
            </span>
        </td>
        <td class="text-center">
            <a href="riwayat_booking.php" class="btn btn-sm btn-outline-primary rounded-circle" title="Lihat Detail">
                <i class="bi bi-arrow-right-short fs-5"></i>
            </a>
        </td>
    </tr>
    <?php 
        endwhile; 
    else: 
    ?>
    <tr>
        <td colspan="4" class="text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            Belum ada riwayat booking.
        </td>
    </tr>
    <?php endif; ?>
</tbody>
                </table>
            </div>
        </div>
    </div>
</div>
            </div>
        </div>
    </div>

    <script>
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
    </script>
</body>
</html>