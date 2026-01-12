<?php
session_start();
include "../config/koneksi.php";

// Proteksi Halaman Admin
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Booking Ditolak | Admin KJD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-dark: #1e293b;
            --primary: #4361ee;
            --danger-soft: #fee2e2;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fc;
            margin: 0;
        }

        /* Sidebar (Tetap sesuai style asli Anda) */
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
        .sidebar.hide { transform: translateX(-260px); }

        /* Content Area */
        .main-content { margin-left: 260px; transition: 0.3s; min-height: 100vh; }
        .main-content.full { margin-left: 0; }

        .top-nav {
            background: #fff; padding: 1rem 2rem;
            border-bottom: 1px solid #e2e8f0; display: flex; align-items: center;
        }

        .card-table {
            background: #fff; border-radius: 12px; border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .badge-reason {
            display: block; font-size: 0.75rem; color: #dc3545;
            margin-top: 4px; font-style: italic;
        }
    </style>
</head>
<body>

    <?php include "../partials/sidebar.php"; ?>

    <div class="main-content" id="mainContent">
        <header class="top-nav">
            <i class="bi bi-list fs-3 me-3" style="cursor:pointer;" id="btn-toggle"></i>
            <h5 class="fw-bold mb-0">Riwayat Pembatalan</h5>
        </header>

        <div class="container-fluid p-4">
            <div class="mb-4">
                <h2 class="fw-bold text-dark mb-1">Booking Ditolak</h2>
                <p class="text-muted">Daftar pesanan yang telah dibatalkan atau ditolak</p>
            </div>

            <div class="card card-table p-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>ID Booking</th>
                                <th>Nama Customer</th>
                                <th>Tarian</th>
                                <th>Tanggal & Waktu</th>
                                <th class="text-center">Status & Alasan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            // QUERY DIPERBARUI: Mengambil no_telp dan username sesuai struktur tabel users Anda
                            $sql = "SELECT bookings.*, 
                                           users.nama_lengkap, 
                                           users.username, 
                                           users.no_telp, 
                                           tarian.nama_tarian 
                                    FROM bookings 
                                    JOIN users ON bookings.user_id = users.id 
                                    JOIN tarian ON bookings.id_tarian = tarian.id 
                                    WHERE bookings.status = 'ditolak' 
                                    ORDER BY bookings.id DESC";

                            $query = mysqli_query($koneksi, $sql);
                            
                            if (mysqli_num_rows($query) > 0) {
                                while ($row = mysqli_fetch_assoc($query)) {
                                    $data_json = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                            ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><span class="text-muted fw-bold">#BOK-<?= $row['id']; ?></span></td>
                                    <td><div class="fw-bold"><?= $row['nama_lengkap']; ?></div></td>
                                    <td><span class="badge bg-light text-dark border"><?= $row['nama_tarian']; ?></span></td>
                                    <td>
                                        <div class="small fw-medium"><?= date('d/m/Y', strtotime($row['tanggal_booking'])); ?></div>
                                        <div class="small text-muted"><?= substr($row['jam_mulai'], 0, 5); ?> WIB</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger text-white">Ditolak</span>
                                        <?php if(!empty($row['alasan_tolak'])): ?>
                                            <div class="badge-reason text-truncate" style="max-width: 150px;" title="<?= $row['alasan_tolak']; ?>">
                                                <?= $row['alasan_tolak']; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-sm btn-outline-info border-0" onclick='tampilDetailDitolak(<?= $data_json ?>)'>
                                                <i class="bi bi-eye-fill"></i>
                                            </button>
                                            <a href="hapus_booking.php?id=<?= $row['id']; ?>" 
                                               class="btn btn-sm btn-outline-danger border-0" 
                                               onclick="return confirm('Hapus riwayat booking ini?')">
                                                <i class="bi bi-trash-fill"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php 
                                } 
                            } else { 
                                echo '<tr><td colspan="7" class="text-center py-5 text-muted">Tidak ada data riwayat yang ditolak.</td></tr>'; 
                            } 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetailDitolak" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow" style="border-radius: 15px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold"><i class="bi bi-receipt me-2 text-primary"></i>Rincian Penolakan & Identitas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" id="isiDetailDitolak">
                    </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar Toggle
        document.getElementById('btn-toggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('hide');
            document.getElementById('mainContent').classList.toggle('full');
        });

        // Function Modal Detail
        function tampilDetailDitolak(data) {
            // Format link WhatsApp
            const waLink = data.no_telp ? data.no_telp.replace(/\D/g,'') : '';
            
            const html = `
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary"><i class="bi bi-person-fill"></i> Identitas Customer</h6>
                        <div class="mb-2">
                            <small class="text-muted d-block">Nama Lengkap</small>
                            <span class="fw-bold">${data.nama_lengkap}</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block">Username</small>
                            <span class="fw-bold">${data.username}</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block">WhatsApp / Telp</small>
                            <a href="https://wa.me/${waLink}" target="_blank" class="text-success fw-bold text-decoration-none">
                                <i class="bi bi-whatsapp"></i> ${data.no_telp || '-'}
                            </a>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary"><i class="bi bi-info-circle-fill"></i> Detail Pesanan</h6>
                        <div class="mb-2">
                            <small class="text-muted d-block">Tarian & Jumlah Penari</small>
                            <span class="fw-bold">${data.nama_tarian} (${data.jumlah_penari} Orang)</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block">Total Harga</small>
                            <span class="fw-bold text-danger">Rp ${new Intl.NumberFormat('id-ID').format(data.total_harga)}</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block">Jadwal Acara</small>
                            <span class="fw-bold">${data.tanggal_booking} | ${data.jam_mulai.substring(0,5)} WIB</span>
                        </div>
                    </div>

                    <div class="col-12 border-top pt-3">
                        <div class="mb-3">
                            <small class="text-muted d-block">Lokasi/Alamat Acara</small>
                            <div class="fw-bold text-dark">${data.kategori_alamat}</div>
                            <div class="text-primary small fw-bold">${data.kategori_daerah}</div>
                        </div>
                        <div class="bg-light p-3 rounded mb-3">
                            <small class="text-muted d-block mb-1">Catatan Customer:</small>
                            <span class="small italic text-secondary">"${data.catatan || 'Tidak ada catatan.'}"</span>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded border-start border-4 border-danger">
                            <small class="text-danger fw-bold d-block">Alasan Penolakan Admin:</small>
                            <p class="mb-0 small text-dark fw-medium">${data.alasan_tolak || 'Tidak ada alasan spesifik.'}</p>
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('isiDetailDitolak').innerHTML = html;
            new bootstrap.Modal(document.getElementById('modalDetailDitolak')).show();
        }
    </script>
</body>
</html>