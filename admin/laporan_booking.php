<?php
session_start();
include "../config/koneksi.php";

// Proteksi Halaman Admin
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// Logika Filter Tanggal
$tgl_mulai = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : '';
$tgl_selesai = isset($_GET['tgl_selesai']) ? $_GET['tgl_selesai'] : '';

$query_str = "SELECT bookings.*, users.nama_lengkap, tarian.nama_tarian 
              FROM bookings 
              JOIN users ON bookings.user_id = users.id
              JOIN tarian ON bookings.id_tarian = tarian.id";

if ($tgl_mulai != '' && $tgl_selesai != '') {
    $query_str .= " WHERE bookings.tanggal_booking BETWEEN '$tgl_mulai' AND '$tgl_selesai' AND bookings.status != 'pending'";
} else {
    $query_str .= " WHERE bookings.status != 'pending'";
}

/** * PERUBAHAN DISINI:
 * Menggunakan bookings.id DESC agar data yang paling baru diinput 
 * muncul di urutan paling atas (terbaru di atas).
 */
$query_str .= " ORDER BY bookings.id DESC"; 
$query = mysqli_query($koneksi, $query_str);

// Variabel Statistik
$total_income = 0;
$count_lunas = 0;
$count_total = 0;

// Ambil data ke dalam array untuk digunakan di statistik dan tabel
$data_rows = [];
while ($r = mysqli_fetch_assoc($query)) {
    $data_rows[] = $r;
    $count_total++;
    if ($r['status_bayar'] == 'lunas') {
        $total_income += $r['total_harga'];
        $count_lunas++;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Modern | Admin KJD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-dark: #1e293b;
            --primary: #4361ee;
            --glass: rgba(255, 255, 255, 0.8);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f1f5f9;
            color: #334155;
            margin: 0;
        }

        /* SIDEBAR STYLE TETAP ASLI */
        .sidebar {
            width: 260px; height: 100vh; position: fixed;
            background: var(--sidebar-dark); padding: 1.5rem;
            transition: 0.3s; z-index: 1000; overflow-y: auto;
        }
        .sidebar-brand {
            color: #fff; font-weight: bold; font-size: 1.2rem;
            margin-bottom: 2rem; display: flex; align-items: center; gap: 10px;
        }
        .sidebar a {
            color: #94a3b8; text-decoration: none; padding: 12px;
            display: flex; align-items: center; border-radius: 8px;
            margin-bottom: 5px; transition: 0.2s;
        }
        .sidebar a:hover, .sidebar a.active { background: rgba(255, 255, 255, 0.1); color: #fff; }
        .sidebar i { margin-right: 10px; }

        .main-content { margin-left: 260px; transition: 0.3s; min-height: 100vh; }
        
        .top-nav { background: var(--glass); backdrop-filter: blur(10px); padding: 1rem 2rem; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 900; }
        
        .stat-card {
            border: none; border-radius: 20px; transition: transform 0.3s;
            background: #fff; box-shadow: 0 10px 25px rgba(0,0,0,0.02);
        }
        .stat-card:hover { transform: translateY(-5px); }
        
        .card-table {
            border: none; border-radius: 24px; background: #fff;
            box-shadow: 0 20px 40px rgba(0,0,0,0.04); overflow: hidden;
        }

        .btn-modern { border-radius: 12px; padding: 10px 24px; font-weight: 600; transition: 0.3s; }
        .form-control-modern { border-radius: 12px; border: 1px solid #e2e8f0; padding: 10px 15px; }
        
        .table thead th {
            background: #f8fafc; text-transform: uppercase; font-size: 0.75rem;
            letter-spacing: 1px; color: #64748b; padding: 1.25rem; border: none;
        }
        .table tbody td { padding: 1.25rem; border-bottom: 1px solid #f1f5f9; }

        .status-pill { padding: 6px 14px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; }
        .bg-lunas { background: #dcfce7; color: #15803d; }
        .bg-pending { background: #fef9c3; color: #854d0e; }
        .bg-belum { background: #fee2e2; color: #b91c1c; }

        @media (max-width: 992px) {
            .sidebar { transform: translateX(-260px); }
            .main-content { margin-left: 0; }
        }

        @media print {
            .no-print, .sidebar, .top-nav { display: none !important; }
            .main-content { margin-left: 0 !important; }
            .card-table { box-shadow: none; border: 1px solid #eee; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <?php include "../partials/sidebar.php"; ?>
    </div>

    <div class="main-content">
        <header class="top-nav no-print d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Analytics & Reports</h5>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-dark btn-modern"><i class="bi bi-printer me-2"></i>Cetak</button>
            </div>
        </header>

        <div class="container-fluid p-4">
            
            <div class="mb-4 no-print">
                <h2 class="fw-bold">Laporan Ringkasan</h2>
                <p class="text-muted">Data terbaru ditampilkan paling atas.</p>
            </div>

            <div class="d-none d-print-block text-center mb-5">
                <h2 class="fw-bold">LAPORAN TRANSAKSI SANGGAR KJD</h2>
                <p>Periode: <?= ($tgl_mulai) ? $tgl_mulai . ' s/d ' . $tgl_selesai : 'Semua Data' ?></p>
                <hr>
            </div>

            <div class="card stat-card p-4 mb-4 no-print border-start border-primary border-4">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="small fw-bold mb-2">Dari Tanggal</label>
                        <input type="date" name="tgl_mulai" class="form-control form-control-modern" value="<?= $tgl_mulai ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-bold mb-2">Sampai Tanggal</label>
                        <input type="date" name="tgl_selesai" class="form-control form-control-modern" value="<?= $tgl_selesai ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary btn-modern px-4 me-2">Filter</button>
                        <a href="laporan_booking.php" class="btn btn-light btn-modern">Reset</a>
                    </div>
                </form>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card stat-card p-4 bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="opacity-75 mb-1">Total Pemasukan</h6>
                                <h3 class="fw-bold mb-0 text-white">Rp <?= number_format($total_income, 0, ',', '.') ?></h3>
                            </div>
                            <i class="bi bi-wallet2 fs-1 opacity-25"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Pemesanan Lunas</h6>
                                <h3 class="fw-bold mb-0 text-success"><?= $count_lunas ?> Transaksi</h3>
                            </div>
                            <i class="bi bi-check-circle fs-1 text-success opacity-25"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Aktivitas</h6>
                                <h3 class="fw-bold mb-0"><?= $count_total ?> Data</h3>
                            </div>
                            <i class="bi bi-bar-chart fs-1 text-primary opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-table">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Booking ID</th>
                                <th>Customer</th>
                                <th>Detail Tarian</th>
                                <th class="text-center">Tgl Pentas/Latihan</th>
                                <th class="text-end">Biaya</th>
                                <th class="text-center">Status Bayar</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($data_rows)): ?>
                                <tr><td colspan="8" class="text-center py-5 text-muted">Data tidak ditemukan.</td></tr>
                            <?php else: $no = 1; foreach ($data_rows as $row): ?>
                            <tr>
                                <td class="text-muted"><?= $no++ ?></td>
                                <td><span class="fw-bold text-dark">#BK-<?= $row['id'] ?></span></td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= $row['nama_lengkap'] ?></div>
                                    <small class="text-muted">ID: <?= $row['user_id'] ?></small>
                                </td>
                                <td><span class="badge bg-light text-dark border rounded-pill px-3"><?= $row['nama_tarian'] ?></span></td>
                                <td class="text-center small"><?= date('d/m/Y', strtotime($row['tanggal_booking'])) ?></td>
                                <td class="text-end fw-bold text-primary">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                                <td class="text-center">
                                    <?php 
                                    $sb = $row['status_bayar'];
                                    if($sb == 'lunas') echo '<span class="status-pill bg-lunas">LUNAS</span>';
                                    elseif($sb == 'menunggu verifikasi') echo '<span class="status-pill bg-pending">VERIFIKASI</span>';
                                    else echo '<span class="status-pill bg-belum">BELUM</span>';
                                    ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?= ($row['status'] == 'diterima') ? 'bg-success' : 'bg-danger' ?> rounded-circle p-1" title="<?= $row['status'] ?>">
                                        <i class="bi <?= ($row['status'] == 'diterima') ? 'bi-check' : 'bi-x' ?>"></i>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row mt-5 d-none d-print-flex">
                <div class="col-8"></div>
                <div class="col-4 text-center">
                    <p class="mb-5">Dicetak pada: <?= date('d/m/Y H:i') ?><br>Penanggung Jawab,</p>
                    <br><br>
                    <p class="fw-bold text-decoration-underline">Admin Sanggar KJD</p>
                </div>
            </div>

        </div>
    </div>

</body>
</html>