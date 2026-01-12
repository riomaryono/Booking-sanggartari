<?php
session_start();
include "../config/koneksi.php";

// Proteksi halaman admin
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Booking Masuk | Admin KJD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-dark: #1e293b;
            --primary: #4361ee;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fc;
            margin: 0;
            overflow-x: hidden;
        }

        /* SIDEBAR TETAP (TIDAK BERUBAH SESUAI PERMINTAAN) */
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

        .sidebar a:hover, .sidebar a.active {
            background: rgba(255, 255, 255, 0.1); color: #fff;
        }

        .sidebar i { margin-right: 10px; }
        .sidebar.hide { transform: translateX(-260px); }

        .main-content { margin-left: 260px; transition: 0.3s; min-height: 100vh; }
        .main-content.full { margin-left: 0; }

        .top-nav {
            background: #fff; padding: 1rem 2rem;
            border-bottom: 1px solid #e2e8f0; display: flex; align-items: center;
        }

        /* CSS TAMBAHAN UNTUK TABEL DAN TOMBOL */
        .card-table {
            background: #fff; border-radius: 12px; border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .price-text {
            color: var(--primary);
            font-weight: 700;
        }

        /* Perbaikan tombol aksi agar rapi */
        .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: center;
        }
        
        .btn-action {
            padding: 5px 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            font-size: 0.85rem;
            border-radius: 6px;
        }
    </style>
</head>
<body>

    <?php include "../partials/sidebar.php"; ?>

    <div class="main-content" id="mainContent">
        <header class="top-nav">
            <i class="bi bi-list fs-3 me-3" style="cursor:pointer;" id="btn-hamburger"></i>
            <h5 class="fw-bold mb-0">Manajemen Pesanan</h5>
        </header>

        <div class="container-fluid p-4">
            <div class="mb-4">
                <h2 class="fw-bold text-dark mb-1">Booking Masuk</h2>
                <p class="text-muted">Kelola permintaan penyewaan sanggar tari</p>
            </div>

            <div class="card card-table p-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Customer</th>
                                <th>Tarian</th>
                                <th>Jadwal</th>
                                <th>Total Biaya</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $sql = "SELECT bookings.*, users.nama_lengkap, users.no_telp, tarian.nama_tarian 
                                    FROM bookings 
                                    JOIN users ON bookings.user_id = users.id 
                                    JOIN tarian ON bookings.id_tarian = tarian.id 
                                    WHERE bookings.status = 'pending' 
                                    ORDER BY bookings.id DESC";

                            $query = mysqli_query($koneksi, $sql);
                            
                            if(mysqli_num_rows($query) > 0) {
                                while ($row = mysqli_fetch_assoc($query)) {
                                    $data_json = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                            ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td>
                                        <div class="fw-bold"><?= $row['nama_lengkap']; ?></div>
                                        <small class="text-muted">#BOK-<?= $row['id']; ?></small>
                                    </td>
                                    <td><span class="badge bg-light text-dark border"><?= $row['nama_tarian']; ?></span></td>
                                    <td>
                                        <div class="small fw-bold"><?= date('d/m/Y', strtotime($row['tanggal_booking'])); ?></div>
                                        <div class="small text-muted"><?= substr($row['jam_mulai'], 0, 5); ?> WIB</div>
                                    </td>
                                    <td><span class="price-text">Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-action btn-outline-primary" onclick='tampilDetail(<?= $data_json ?>)'>
                                                <i class="bi bi-eye"></i> Detail
                                            </button>
                                            
                                            <a href="update_status_booking.php?id=<?= $row['id']; ?>&status=diterima" 
                                               class="btn btn-action btn-success" onclick="return confirm('Terima pesanan ini?')">
                                                <i class="bi bi-check-circle"></i> Terima
                                            </a>
                                            
                                            <button class="btn btn-action btn-danger" onclick="tampilModalTolak(<?= $row['id']; ?>)">
                                                <i class="bi bi-x-circle"></i> Tolak
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php 
                                } 
                            } else {
                                echo '<tr><td colspan="6" class="text-center py-5 text-muted">Belum ada booking baru.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-info-circle me-2"></i>Detail Pesanan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" id="isiDetail">
                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTolak" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="update_status_booking.php" method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Alasan Penolakan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id_booking_tolak">
                    <input type="hidden" name="status" value="ditolak">
                    <div class="mb-3">
                        <textarea name="alasan_tolak" class="form-control" rows="3" placeholder="Masukkan alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="proses_tolak" class="btn btn-danger w-100">Kirim & Tolak</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // SCRIPT SIDEBAR (TETAP)
        const btnHamburger = document.getElementById('btn-hamburger');
        btnHamburger.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('hide');
            document.getElementById('mainContent').classList.toggle('full');
        });

        // FUNGSI DETAIL
        function tampilDetail(data) {
    const html = `
        <div class="row g-3">
            <div class="col-6">
                <small class="text-muted d-block">Customer</small>
                <strong>${data.nama_lengkap}</strong>
            </div>
            <div class="col-6">
                <small class="text-muted d-block">No. WhatsApp</small>
                <strong>${data.no_telp || '-'}</strong>
            </div>
            <div class="col-6">
                <small class="text-muted d-block">Tarian</small>
                <strong>${data.nama_tarian}</strong>
            </div>
            <div class="col-6">
                <small class="text-muted d-block">Jumlah Penari</small>
                <strong>${data.jumlah_penari} Orang</strong>
            </div>
            <div class="col-12">
                <small class="text-muted d-block">Acara</small>
                <strong>${data.jenis_acara}</strong>
            </div>
            <div class="col-12">
                <small class="text-muted d-block">Kategori Daerah</small>
                <span class="badge bg-info text-dark">${data.kategori_daerah}</span>
            </div>
            <div class="col-12">
                <small class="text-muted d-block">Alamat Lengkap (Lokasi)</small>
                <p class="mb-0 text-dark p-2 bg-light border rounded">${data.kategori_alamat}</p>
            </div>
            <div class="col-12">
                <div class="p-2 rounded small border-start border-4 border-primary bg-light">
                    <small class="text-muted d-block">Catatan Tambahan:</small>
                    ${data.catatan || '-'}
                </div>
            </div>
        </div>
    `;
    document.getElementById('isiDetail').innerHTML = html;
    new bootstrap.Modal(document.getElementById('modalDetail')).show();
}

        // FUNGSI TOLAK
        function tampilModalTolak(id) {
            document.getElementById('id_booking_tolak').value = id;
            new bootstrap.Modal(document.getElementById('modalTolak')).show();
        }
    </script>
</body>
</html>