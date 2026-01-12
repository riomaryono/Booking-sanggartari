<?php
// ... (Bagian PHP tetap sama seperti kode kamu) ...
session_start();
include "../config/koneksi.php"; 
$conn = $koneksi; 

if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'customer') {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user  = mysqli_fetch_assoc($query);

$nama     = $user['nama_lengkap'] ?? 'Pengguna';
$username = $user['username'] ?? '-';
$telepon  = $user['no_telp'] ?? '-';
$tgl_join = isset($user['created_at']) ? date('d M Y', strtotime($user['created_at'])) : date('d M Y');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Saya | KJD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* ... (Gunakan CSS yang sudah kita buat sebelumnya) ... */
        :root { --sidebar-dark: #1e293b; --primary: #4361ee; --soft-bg: #f8f9fc; --white: #ffffff; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--soft-bg); margin: 0; }
        .sidebar { width: 260px; height: 100vh; position: fixed; background: var(--sidebar-dark); padding: 1.5rem; transition: 0.3s; z-index: 1000; overflow-y: auto; }
        .sidebar-brand { color: #fff; font-weight: bold; font-size: 1.2rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 10px; }
        .sidebar a { color: #94a3b8; text-decoration: none; padding: 12px; display: flex; align-items: center; border-radius: 8px; margin-bottom: 5px; transition: 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.1); color: #fff; }
        .sidebar i { margin-right: 10px; }
        .main-content { margin-left: 260px; transition: 0.3s; min-height: 100vh; }
        .header-cover { background: linear-gradient(135deg, #4361ee 0%, #4cc9f0 100%); height: 220px; width: 100%; border-radius: 0 0 50px 50px; position: relative; }
        .profile-wrapper { margin-top: -100px; padding: 0 2rem 3rem; }
        .main-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 35px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08); overflow: hidden; }
        .avatar-section { padding: 3rem; border-bottom: 1px solid #f1f4f9; }
        .img-profile-lg { width: 140px; height: 140px; border-radius: 40px; border: 6px solid #fff; box-shadow: 0 15px 35px rgba(67, 97, 238, 0.25); object-fit: cover; }
        .btn-edit-premium { background: var(--primary); color: white; padding: 12px 28px; border-radius: 18px; font-weight: 700; text-decoration: none; transition: 0.3s; box-shadow: 0 10px 20px rgba(67, 97, 238, 0.2); border: none; }
        .info-grid { padding: 3rem; }
        .info-tile { background: #fcfdfe; border: 1px solid #f1f4f9; border-radius: 25px; padding: 1.5rem; height: 100%; transition: 0.4s; }
        .info-tile:hover { transform: translateY(-10px); border-color: var(--primary); }
        .icon-circle { width: 50px; height: 50px; background: #f0f3ff; color: var(--primary); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.2rem; font-size: 1.4rem; }
        .label-sm { font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: #94a3b8; }
        .value-md { font-size: 1.1rem; font-weight: 700; color: #1e293b; }
        .badge-verify { background: #eef2ff; color: var(--primary); font-size: 0.7rem; font-weight: 800; padding: 5px 12px; border-radius: 10px; display: inline-block; margin-bottom: 10px; }
        .top-nav { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(15px); padding: 1rem 2rem; position: sticky; top: 0; z-index: 999; border-bottom: 1px solid rgba(238, 242, 247, 0.5); }
        @media (max-width: 768px) { .sidebar { margin-left: -260px; } .sidebar.show { margin-left: 0; } .main-content { margin-left: 0; } }
    </style>
</head>
<body>

    <?php include "sidebar_customer.php"; ?>

<div class="main-content" id="content">
    <nav class="top-nav d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <button id="btn-hamburger" class="btn btn-light me-3 shadow-sm" style="border-radius: 10px;">
                <i class="bi bi-list fs-4"></i>
            </button>
            <h6 class="mb-0 fw-bold text-dark">Manajemen Profil</h6>
        </div>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-sm-block">
                    <p class="mb-0 small fw-bold"><?= htmlspecialchars($nama) ?></p>
                    <p class="mb-0 text-muted" style="font-size: 10px;">ID: #<?= $user_id ?></p>
                </div>
                <div class="bg-white shadow-sm rounded-circle p-1">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($nama) ?>&background=4361ee&color=fff&size=35" class="rounded-circle">
                </div>
            </div>
        </nav>

        <div class="header-cover"></div>

        <div class="profile-wrapper">
            <div class="row justify-content-center">
                <div class="col-xl-10">
                    <div class="main-card">
                        <div class="avatar-section">
                            <div class="row align-items-center">
                                <div class="col-md-auto mb-4 mb-md-0 text-center">
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($nama) ?>&background=4361ee&color=fff&size=140&bold=true" class="img-profile-lg" alt="Avatar">
                                </div>
                                <div class="col-md text-center text-md-start">
                                    <span class="badge-verify"><i class="bi bi-patch-check-fill me-1"></i> ACCOUNT VERIFIED</span>
                                    <h1 class="fw-bold mb-1" style="color: #1e293b; letter-spacing: -1px;"><?= htmlspecialchars($nama) ?></h1>
                                    <p class="text-muted mb-0"><i class="bi bi-calendar3 me-2"></i>Aktif sejak <?= $tgl_join ?></p>
                                </div>
                                <div class="col-md-auto mt-4 mt-md-0 text-center">
                                    <button type="button" class="btn-edit-premium" data-bs-toggle="modal" data-bs-target="#modalEditProfil">
                                        <i class="bi bi-pencil-square me-2"></i>Update Profil
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="info-grid">
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <div class="info-tile text-center text-md-start">
                                        <div class="icon-circle mx-auto mx-md-0"><i class="bi bi-person-badge"></i></div>
                                        <p class="label-sm">Username Akun</p>
                                        <p class="value-md">@<?= htmlspecialchars($username) ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-tile text-center text-md-start">
                                        <div class="icon-circle mx-auto mx-md-0" style="background: #e6fffa; color: #38b2ac;"><i class="bi bi-whatsapp"></i></div>
                                        <p class="label-sm">Nomor WhatsApp</p>
                                        <p class="value-md"><?= htmlspecialchars($telepon) ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-tile text-center text-md-start">
                                        <div class="icon-circle mx-auto mx-md-0" style="background: #fff5f5; color: #f56565;"><i class="bi bi-shield-check"></i></div>
                                        <p class="label-sm">Level Akses</p>
                                        <p class="value-md"><?= ucfirst($user['level']) ?> Customer</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 p-4" style="background: #f8fafc; border-radius: 25px; border: 1px dashed #cbd5e1;">
                                <div class="row align-items-center">
                                    <div class="col-auto d-none d-md-block">
                                        <div class="bg-white p-3 rounded-4 shadow-sm">
                                            <i class="bi bi-lock-fill fs-3 text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <h6 class="fw-bold mb-1">Keamanan Akun</h6>
                                        <p class="small text-muted mb-0">Lindungi data Anda dengan mengganti password secara berkala.</p>
                                    </div>
                                    <div class="col-md-auto mt-3 mt-md-0">
                                        <button type="button" class="btn btn-dark btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalGantiPassword">
                                            Ganti Password
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditProfil" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 30px; border: none;">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="modal-title fw-bold">Edit Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="proses_update_profil.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control border-0 bg-light p-3" value="<?= $nama ?>" required style="border-radius:12px;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Nomor WhatsApp</label>
                            <input type="text" name="no_telp" class="form-control border-0 bg-light p-3" value="<?= $telepon ?>" required style="border-radius:12px;">
                        </div>
                        <button type="submit" name="simpan" class="btn btn-primary w-100 p-3 fw-bold mt-2" style="border-radius: 15px;">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalGantiPassword" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 30px; border: none;">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="modal-title fw-bold">Keamanan Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="proses_ganti_password.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Password Lama</label>
                            <input type="password" name="pass_lama" class="form-control border-0 bg-light p-3" required style="border-radius:12px;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Password Baru</label>
                            <input type="password" name="pass_baru" class="form-control border-0 bg-light p-3" required style="border-radius:12px;">
                        </div>
                        <button type="submit" name="update_pass" class="btn btn-dark w-100 p-3 fw-bold mt-2" style="border-radius: 15px;">Perbarui Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const btnHamburger = document.getElementById('btn-hamburger');
    const sidebar = document.querySelector('.sidebar');
    const content = document.getElementById('content');
    const overlay = document.getElementById('overlay');

    btnHamburger.addEventListener('click', function() {
        if (window.innerWidth > 768) {
            // Logic Desktop: Geser Sidebar keluar layar
            if (sidebar.style.marginLeft === "-260px") {
                sidebar.style.marginLeft = "0";
                content.style.marginLeft = "260px";
            } else {
                sidebar.style.marginLeft = "-260px";
                content.style.marginLeft = "0";
            }
        } else {
            // Logic Mobile: Tambah class show
            sidebar.classList.toggle('show');
            if (overlay) overlay.classList.toggle('active');
        }
    });

    // Klik luar (overlay) untuk menutup di mobile
    if (overlay) {
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            overlay.classList.remove('active');
        });
    }
</script>
</body>
</html>