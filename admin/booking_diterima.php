<?php
session_start();
include "../config/koneksi.php";

// Proteksi Halaman Admin
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// Logika Filter Tab
$filter = isset($_GET['pembayaran']) ? $_GET['pembayaran'] : 'semua';
$where_clause = "WHERE bookings.status = 'diterima'";

if ($filter == 'perlu_verifikasi') {
    $where_clause .= " AND bookings.status_bayar = 'menunggu verifikasi'";
} elseif ($filter == 'lunas') {
    $where_clause .= " AND bookings.status_bayar = 'lunas'";
} elseif ($filter == 'belum') {
    $where_clause .= " AND (bookings.status_bayar = 'belum' OR bookings.status_bayar IS NULL)";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Booking Diterima | Admin KJD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-dark: #1e293b;
            --primary: #4361ee;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8f9fc;
            margin: 0;
            overflow-x: hidden;
        }

        /* --- STYLE SIDEBAR ASLI ANDA (DIPERBAIKI) --- */
        .sidebar {
            width: 260px; 
            height: 100vh; 
            position: fixed;
            background: var(--sidebar-dark); 
            padding: 1.5rem;
            transition: 0.3s; 
            z-index: 1000; 
            overflow-y: auto;
            left: 0;
            top: 0;
        }

        .sidebar-brand {
            color: #fff; 
            font-weight: bold; 
            font-size: 1.2rem;
            margin-bottom: 2rem; 
            display: flex; 
            align-items: center; 
            gap: 10px;
        }

        .sidebar a {
            color: #94a3b8; 
            text-decoration: none; 
            padding: 12px;
            display: flex; 
            align-items: center; 
            border-radius: 8px;
            margin-bottom: 5px; 
            transition: 0.2s;
        }

        .sidebar a:hover, .sidebar a.active {
            background: rgba(255, 255, 255, 0.1); 
            color: #fff;
        }

        .sidebar i { margin-right: 10px; }

        /* --- PENYESUAIAN KONTEN UTAMA --- */
        .main-content { 
            margin-left: 260px; /* Harus sama dengan lebar sidebar */
            padding: 20px; 
            min-height: 100vh;
            transition: 0.3s;
        }

        @media (max-width: 992px) {
            .sidebar { transform: translateX(-260px); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }

        /* MODAL DETAIL STYLE */
        .modal-header-custom { background-color: #0d6efd; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 15px 20px; }
        .section-title { font-size: 0.95rem; font-weight: 700; color: #0d6efd; display: flex; align-items: center; gap: 8px; margin-bottom: 15px; }
        .info-label { font-size: 0.85rem; color: #64748b; margin-bottom: 2px; }
        .info-value { font-size: 0.95rem; font-weight: 700; color: #1e293b; margin-bottom: 12px; }
        .price-value { color: #dc3545; font-size: 1.1rem; font-weight: 800; }
        .address-box { background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 10px; padding: 15px; margin-top: 15px; }
        .btn-tutup { background-color: #6c757d; color: white; border-radius: 20px; padding: 8px 30px; border: none; }
        
        /* Badge styling */
        .badge-lunas { background-color: #d1e7dd; color: #0f5132; }
        .badge-proses { background-color: #fff3cd; color: #856404; }
        .badge-belum { background-color: #e2e3e5; color: #41464b; }
    </style>
</head>
<body>

    <?php include "../partials/sidebar.php"; ?>

    <div class="main-content">
        <header class="bg-white p-3 border-bottom d-flex align-items-center mb-4 rounded shadow-sm">
            <i class="bi bi-list fs-3 me-3" style="cursor:pointer;"></i>
            <h5 class="fw-bold mb-0">Manajemen Pembayaran</h5>
        </header>

        <div class="container-fluid">
            <div class="mb-4">
                <h2 class="fw-bold mb-1">Booking Diterima</h2>
                <p class="text-muted">Kelola jadwal dan verifikasi status customer</p>
            </div>

            <ul class="nav nav-pills mb-4">
                <li class="nav-item"><a class="nav-link <?= $filter == 'semua' ? 'active' : '' ?>" href="?pembayaran=semua">Semua</a></li>
                <li class="nav-item"><a class="nav-link <?= $filter == 'perlu_verifikasi' ? 'active' : '' ?>" href="?pembayaran=perlu_verifikasi">Perlu Verifikasi</a></li>
                <li class="nav-item"><a class="nav-link <?= $filter == 'lunas' ? 'active' : '' ?>" href="?pembayaran=lunas">Lunas</a></li>
            </ul>

            <div class="card border-0 shadow-sm p-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr class="text-muted small">
                                <th>NO</th>
                                <th>ID BOOKING</th>
                                <th>CUSTOMER</th>
                                <th>TARIAN</th>
                                <th>TANGGAL</th>
                                <th>PEMBAYARAN</th>
                                <th class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $sql = "SELECT bookings.*, users.nama_lengkap, users.username, users.no_telp, tarian.nama_tarian 
                                    FROM bookings 
                                    JOIN users ON bookings.user_id = users.id 
                                    JOIN tarian ON bookings.id_tarian = tarian.id 
                                    $where_clause ORDER BY bookings.id DESC";
                            $query = mysqli_query($koneksi, $sql);
                            
                            while ($row = mysqli_fetch_assoc($query)) {
                                $data_json = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                                $st_bayar = $row['status_bayar'];
                                
                                $badge_class = "badge-belum";
                                if($st_bayar == 'lunas') $badge_class = "badge-lunas";
                                if($st_bayar == 'menunggu verifikasi') $badge_class = "badge-proses";
                            ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td class="fw-bold text-primary">#BOK-<?= $row['id']; ?></td>
                                    <td><?= $row['nama_lengkap']; ?></td>
                                    <td><?= $row['nama_tarian']; ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['tanggal_booking'])); ?></td>
                                    <td>
                                        <span class="badge <?= $badge_class ?> px-3 py-2 rounded-pill">
                                            <?= ucfirst($st_bayar ?: 'Belum Bayar') ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick='tampilDetail(<?= $data_json ?>)'>
                                                <i class="bi bi-search"></i> Detail
                                            </button>
                                            
                                            <a href="cetak_invoice.php?id=<?= $row['id']; ?>" target="_blank" class="btn btn-sm btn-outline-dark rounded-circle" style="width:32px; height:32px; display:flex; align-items:center; justify-content:center;">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetail" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow border-0" style="border-radius: 15px;">
                <div class="modal-header-custom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-phone me-2"></i>Detail Lengkap Reservasi</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" id="isiDetail"></div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn-tutup" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function tampilDetail(data) {
            const container = document.getElementById('isiDetail');
            const formatter = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 });
            container.innerHTML = `
                <div class="row">
                    <div class="col-md-6 border-end">
                        <div class="section-title"><i class="bi bi-person-fill"></i> Identitas Customer</div>
                        <div class="info-label">Nama Lengkap:</div><div class="info-value text-capitalize">${data.nama_lengkap}</div>
                        <div class="info-label">Username Akun:</div><div class="info-value">${data.username}</div>
                        <div class="info-label">Nomor WhatsApp:</div><div class="info-value text-success"><i class="bi bi-whatsapp"></i> ${data.no_telp}</div>
                    </div>
                    <div class="col-md-6 ps-md-4">
                        <div class="section-title"><i class="bi bi-journal-text"></i> Info Reservasi</div>
                        <div class="info-label">Jenis Tarian:</div><div class="mb-2"><span class="badge bg-info-subtle text-info">${data.nama_tarian}</span></div>
                        <div class="info-label">Tanggal & Waktu:</div><div class="info-value">${data.tanggal_booking} | ${data.jam_mulai.substring(0,5)} WIB</div>
                        <div class="info-label">Total Harga:</div><div class="info-value price-value">${formatter.format(data.total_harga)}</div>
                    </div>
                </div>
                <div class="address-box">
                    <div class="fw-bold text-danger mb-1"><i class="bi bi-geo-alt-fill"></i> Alamat & Catatan</div>
                    <div class="fw-bold text-dark mb-1">${data.kategori_alamat} (${data.kategori_daerah})</div>
                    <div class="small text-muted italic">"${data.catatan || 'Tidak ada catatan khusus.'}"</div>
                </div>`;
            new bootstrap.Modal(document.getElementById('modalDetail')).show();
        }
    </script>
</body>
</html>