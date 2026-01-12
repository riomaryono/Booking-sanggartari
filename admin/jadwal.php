<?php
session_start();
include "../config/koneksi.php";

// Proteksi Halaman Admin
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

/** * LOGIKA FILTER KHUSUS JADWAL PENTAS
 * Kita hanya mengambil yang status bookingnya 'diterima' DAN status bayarnya 'lunas'
 */
$where_clause = "WHERE bookings.status = 'diterima' AND bookings.status_bayar = 'lunas'";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Pentas Terverifikasi | Admin KJD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-dark: #1e293b;
            --primary: #4361ee;
            --success-green: #198754;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8f9fc;
            margin: 0;
            overflow-x: hidden;
        }

        /* --- STYLE SIDEBAR ASLI TETAP DIJAGA --- */
        .sidebar {
            width: 260px; height: 100vh; position: fixed;
            background: var(--sidebar-dark); padding: 1.5rem;
            transition: 0.3s; z-index: 1000; overflow-y: auto;
            left: 0; top: 0;
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
        .sidebar a:hover, .sidebar a.active {
            background: rgba(255, 255, 255, 0.1); color: #fff;
        }
        .sidebar i { margin-right: 10px; }

        /* --- MAIN CONTENT --- */
        .main-content { 
            margin-left: 260px; padding: 20px; min-height: 100vh; transition: 0.3s;
        }

        @media (max-width: 992px) {
            .sidebar { transform: translateX(-260px); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }

        /* KHUSUS TABEL JADWAL */
        .card-jadwal { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .badge-lunas { background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
        .modal-header-pentas { background: var(--success-green); color: white; border-radius: 15px 15px 0 0; }
    </style>
</head>
<body>

    <?php include "../partials/sidebar.php"; ?>

    <div class="main-content">
        <header class="bg-white p-3 border-bottom d-flex align-items-center mb-4 rounded shadow-sm">
            <i class="bi bi-list fs-3 me-3" style="cursor:pointer;" onclick="toggleSidebar()"></i>
            <h5 class="fw-bold mb-0">Agenda Operasional</h5>
        </header>

        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1 text-dark">Jadwal Pentas</h2>
                    <p class="text-muted small">Hanya menampilkan pementasan dengan status <span class="badge badge-lunas">Lunas</span></p>
                </div>
                
            </div>

            <div class="card card-jadwal p-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr class="text-muted small">
                                <th>NO</th>
                                <th>INVOICE</th>
                                <th>PELANGGAN</th>
                                <th>TARIAN</th>
                                <th>TANGGAL & JAM</th>
                                <th>LOKASI</th>
                                <th class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            // Query Join antar 3 tabel
                            $sql = "SELECT bookings.*, users.nama_lengkap, users.no_telp, tarian.nama_tarian 
                                    FROM bookings 
                                    JOIN users ON bookings.user_id = users.id 
                                    JOIN tarian ON bookings.id_tarian = tarian.id 
                                    $where_clause 
                                    ORDER BY bookings.tanggal_booking ASC";
                            
                            $query = mysqli_query($koneksi, $sql);
                            
                            if (mysqli_num_rows($query) > 0) {
                                while ($row = mysqli_fetch_assoc($query)) {
                                    $data_json = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                            ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td class="fw-bold text-primary">#INV-<?= $row['id']; ?></td>
                                        <td>
                                            <div class="fw-bold"><?= $row['nama_lengkap']; ?></div>
                                            <small class="text-muted"><?= $row['no_telp']; ?></small>
                                        </td>
                                        <td><span class="badge bg-info-subtle text-info px-3"><?= $row['nama_tarian']; ?></span></td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= date('d M Y', strtotime($row['tanggal_booking'])); ?></div>
                                            <small class="text-muted"><i class="bi bi-clock"></i> <?= substr($row['jam_mulai'], 0, 5); ?> WIB</small>
                                        </td>
                                        <td>
                                            <small class="d-block text-truncate" style="max-width: 150px;">
                                                <i class="bi bi-geo-alt-fill text-danger"></i> <?= $row['kategori_daerah']; ?>
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-dark rounded-pill px-3" onclick='tampilDetail(<?= $data_json ?>)'>
                                                Lihat Lokasi
                                            </button>
                                        </td>
                                    </tr>
                            <?php 
                                }
                            } else {
                                echo '<tr><td colspan="7" class="text-center py-5 text-muted">Belum ada jadwal pentas yang lunas hari ini.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetail" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 15px;">
                <div class="modal-header modal-header-pentas">
                    <h6 class="modal-title fw-bold"><i class="bi bi-map me-2"></i>Detail Lokasi Pentas</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" id="isiDetail"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('show');
        }

        function tampilDetail(data) {
            const container = document.getElementById('isiDetail');
            container.innerHTML = `
                <div class="mb-3">
                    <label class="text-muted small d-block">Nama Customer</label>
                    <div class="fw-bold text-dark">${data.nama_lengkap}</div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Alamat Lengkap</label>
                    <div class="p-3 bg-light rounded border">
                        <i class="bi bi-geo-alt text-danger me-1"></i> 
                        <strong>${data.kategori_alamat}</strong><br>
                        <span class="small">${data.kategori_daerah}</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Catatan Khusus</label>
                    <div class="fst-italic text-secondary small">"${data.catatan || 'Tidak ada catatan'}"</div>
                </div>
                <div class="d-grid mt-4">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Tutup</button>
                </div>`;
            new bootstrap.Modal(document.getElementById('modalDetail')).show();
        }
    </script>
</body>
</html>