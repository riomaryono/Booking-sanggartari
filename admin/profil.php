<?php
$page = 'profil.php'; 
include "auth_admin.php"; 
include "../config/koneksi.php";

$user_id = $_SESSION['user_id'];
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$user_id'");
$user  = mysqli_fetch_assoc($query);

// Variabel penampung data admin
$nama     = $user['nama_lengkap'] ?? 'Administrator';
$username = $user['username'] ?? '-';
$telepon  = $user['no_telp'] ?? '-';
$tgl_join = isset($user['created_at']) ? date('d M Y', strtotime($user['created_at'])) : '-';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Admin | KJD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-dark: #1e293b;
            --primary: #4361ee;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fc;
            margin: 0;
        }

        /* SIDEBAR TETAP (TIDAK BERUBAH) */
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

        .main-content { margin-left: 260px; transition: 0.3s; min-height: 100vh; }

        .top-nav {
            background: #fff; padding: 1rem 2rem;
            border-bottom: 1px solid #e2e8f0; display: flex; align-items: center;
        }

        .sidebar.hide { transform: translateX(-260px); }
        .main-content.full { margin-left: 0; }

        .card-table {
            background: #fff; border-radius: 12px; border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        /* CSS TAMBAHAN UNTUK STATUS */
        .status-badge {
            padding: 5px 12px; border-radius: 50px;
            font-size: 0.75rem; font-weight: 600; display: inline-block;
        }
    </style>
</head>
<body>

    <?php include "../partials/sidebar.php"; ?>

    <div class="main-content" id="mainContent">
        <header class="top-nav">
            <i class="bi bi-list fs-3 me-3" style="cursor:pointer;" onclick="toggleSidebar()"></i>
            <h5 class="fw-bold mb-0">Manajemen Akun</h5>
        </header>

        <div class="container-fluid p-4">
            <div class="mb-4">
                <h2 class="fw-bold text-dark mb-1">Pengaturan Profil</h2>
                <p class="text-muted">Kelola informasi data diri dan keamanan akun Anda</p>
            </div>

            <div class="profile-card">
                <div class="admin-header"></div>
                
                <div class="admin-avatar-wrapper d-md-flex align-items-end justify-content-between">
                    <div class="d-md-flex align-items-end">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($nama) ?>&background=4361ee&color=fff&size=120&bold=true" class="admin-avatar">
                        <div class="ms-md-4 mt-3 mt-md-0">
                            <h3 class="fw-bold mb-1"><?= htmlspecialchars($nama) ?></h3>
                            <span class="badge bg-primary px-3 rounded-pill shadow-sm">Super Admin</span>
                        </div>
                    </div>
                    <button class="btn btn-primary rounded-pill px-4 mt-3 mt-md-0 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEditAdmin">
                        <i class="bi bi-pencil-square me-2"></i>Edit Data Profil
                    </button>
                </div>

                <hr class="mx-4 my-0 opacity-25">

                <div class="p-4 p-md-5 pt-4">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="bg-light-info text-center text-md-start">
                                <p class="info-label mb-1">Username Admin</p>
                                <p class="info-value">@<?= htmlspecialchars($username) ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light-info text-center text-md-start">
                                <p class="info-label mb-1">No. WhatsApp</p>
                                <p class="info-value"><?= htmlspecialchars($telepon) ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light-info text-center text-md-start">
                                <p class="info-label mb-1">Terdaftar Sejak</p>
                                <p class="info-value"><?= $tgl_join ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 p-4 rounded-4 d-flex flex-column flex-md-row align-items-center justify-content-between shadow-sm border border-primary border-opacity-10" style="background: #eef2ff;">
                        <div class="d-flex align-items-center mb-3 mb-md-0 text-center text-md-start">
                            <div class="bg-primary text-white p-3 rounded-3 me-md-3 d-none d-md-block">
                                <i class="bi bi-shield-lock fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Keamanan Password</h6>
                                <p class="text-muted small mb-0">Disarankan untuk mengganti password secara berkala untuk menjaga keamanan sistem.</p>
                            </div>
                        </div>
                        <button class="btn btn-dark rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalPassAdmin">
                            Ganti Password
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditAdmin" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 overflow-hidden">
                <div class="modal-header bg-primary text-white p-4">
                    <h5 class="modal-title fw-bold">Update Data Admin</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="proses_update_admin.php" method="POST">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control p-3 bg-light border-0 shadow-sm" value="<?= $nama ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">No. WhatsApp</label>
                            <input type="text" name="no_telp" class="form-control p-3 bg-light border-0 shadow-sm" value="<?= $telepon ?>" required>
                        </div>
                        <button type="submit" name="simpan" class="btn btn-primary w-100 p-3 fw-bold mt-2 rounded-3 shadow">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPassAdmin" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 overflow-hidden">
                <div class="modal-header bg-dark text-white p-4">
                    <h5 class="modal-title fw-bold">Ganti Password Admin</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="proses_ganti_password_admin.php" method="POST">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Password Saat Ini</label>
                            <input type="password" name="pass_lama" class="form-control p-3 bg-light border-0 shadow-sm" placeholder="Masukkan password lama" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Password Baru</label>
                            <input type="password" name="pass_baru" class="form-control p-3 bg-light border-0 shadow-sm" placeholder="Masukkan password baru" required>
                        </div>
                        <button type="submit" name="update_pass" class="btn btn-dark w-100 p-3 fw-bold mt-2 rounded-3 shadow">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fungsi Toggle Sidebar yang sama dengan booking.php
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('hide');
            document.getElementById('mainContent').classList.toggle('full');
        }
    </script>
</body>
</html>