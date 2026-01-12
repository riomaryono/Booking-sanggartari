<?php
session_start();
include "config/koneksi.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sanggar KJD - Booking Jasa Penari & Member</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-gold: #c5a059;
            --dark-navy: #0f172a;
        }
        body { font-family: 'Poppins', sans-serif; color: #333; scroll-behavior: smooth; overflow-x: hidden; }
        h1, h2, h3, .navbar-brand { font-family: 'Playfair Display', serif; }

        /* Navbar Styling */
        .navbar { background: rgba(15, 23, 42, 0.98); padding: 15px 0; z-index: 1030; transition: 0.3s; }
        .nav-link { font-weight: 500; transition: 0.3s; }
        .nav-link:hover { color: var(--primary-gold) !important; }
        
        /* Hero Section */
        .hero { 
            background: linear-gradient(rgba(0,0,0,0.75), rgba(0,0,0,0.75)), url('assets/img/hero-bg.jpg'); 
            background-size: cover; background-position: center; background-attachment: fixed;
            color: white; min-height: 100vh; display: flex; align-items: center;
        }
        .hero h1 { font-size: 4rem; color: var(--primary-gold); line-height: 1.2; }

        /* Utilities */
        .bg-gold { background-color: var(--primary-gold) !important; }
        .text-gold { color: var(--primary-gold) !important; }
        .btn-gold { background-color: var(--primary-gold); color: white !important; font-weight: 600; border: none; }
        .btn-gold:hover { background-color: #b08d4a; transform: translateY(-2px); }
        .btn-wa { background-color: #25d366; color: white !important; font-weight: 600; border: none; }
        .btn-wa:hover { background-color: #128c7e; }

        /* Card & Section */
        .section-padding { padding: 100px 0; }
        .service-card { border: none; border-radius: 20px; transition: 0.4s; box-shadow: 0 15px 35px rgba(0,0,0,0.05); background: #fff; }
        .service-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        
        /* Modal Custom */
        .modal-content { border-radius: 20px; border: none; overflow: hidden; }
        .modal-header { border-bottom: none; padding: 25px 30px 10px; }
        .form-control { border-radius: 10px; padding: 12px; border: 1px solid #eee; background: #f8f9fa; }

        /* Galeri */
        .img-gallery { width: 100%; height: 300px; object-fit: cover; border-radius: 15px; cursor: pointer; transition: 0.3s; }
        .img-gallery:hover { filter: brightness(70%); }

        /* WA Float */
        .wa-float {
            position: fixed; bottom: 30px; right: 30px; background: #25d366; color: white;
            width: 60px; height: 60px; border-radius: 50px; text-align: center; font-size: 30px;
            box-shadow: 0 5px 15px rgba(37, 211, 102, 0.4); z-index: 1000; display: flex; align-items: center; justify-content: center;
            text-decoration: none;
        }
    </style>
</head>
<body data-bs-spy="scroll" data-bs-target=".navbar" data-bs-offset="100">

<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3" href="#">SANGGAR KJD</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#layanan">Layanan</a></li>
                <li class="nav-item"><a class="nav-link" href="#jadwal">Jadwal</a></li>
                <li class="nav-item"><a class="nav-link" href="#galeri">Galeri</a></li>
            </ul>

            <div class="d-flex gap-2">
                <?php if(!isset($_SESSION['user_id'])){ ?>
                    <button class="btn btn-outline-light rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button>
                    <a href="https://wa.me/6281113809088?text=Halo%20Admin,%20saya%20mau%20daftar%20jadi%20member" target="_blank" class="btn btn-wa rounded-pill px-4">
                        <i class="bi bi-whatsapp me-1"></i> Jadi Member
                    </a>
                <?php } else { ?>
                    <a href="customer/index.php" class="btn btn-gold rounded-pill px-4">Panel Saya</a>
                    <a href="logout.php" class="btn btn-danger rounded-circle" onclick="return confirm('Logout?')"><i class="bi bi-box-arrow-right"></i></a>
                <?php } ?>
            </div>
        </div>
    </div>
</nav>

<section id="home" class="hero text-center">
    <div class="container">
        <h1 class="fw-bold mb-3">Sewa Penari Profesional<br>& Lestarikan Budaya</h1>
        <p class="lead mb-5 opacity-75 mx-auto" style="max-width: 800px;">Kami menyediakan berbagai tarian tradisional untuk memeriahkan acara pernikahan, penyambutan tamu, hingga acara kenegaraan.</p>
        <?php if(!isset($_SESSION['user_id'])): ?>
            <button class="btn btn-gold btn-lg px-5 py-3 rounded-pill shadow-lg" data-bs-toggle="modal" data-bs-target="#loginModal">Mulai Booking Jasa</button>
        <?php endif; ?>
    </div>
</section>

<section id="layanan" class="section-padding bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Layanan Unggulan Kami</h2>
            <hr class="mx-auto bg-gold" style="width: 80px; height: 4px; opacity: 1;">
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="service-card p-4 text-center">
                    <div class="bg-gold text-white rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="bi bi-stars fs-1"></i>
                    </div>
                    <h4>Tari Tradisional</h4>
                    <p class="text-muted">Tari Jaipong, Merak, Piring, dan tarian nusantara lainnya dengan kostum premium.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="service-card p-4 text-center">
                    <div class="bg-dark text-white rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="bi bi-people-fill fs-1"></i>
                    </div>
                    <h4>Pelatihan Member</h4>
                    <p class="text-muted">Kelas tari rutin untuk anak-anak dan dewasa setiap akhir pekan.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="service-card p-4 text-center">
                    <div class="bg-gold text-white rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="bi bi-camera-reels-fill fs-1"></i>
                    </div>
                    <h4>Workshop Seni</h4>
                    <p class="text-muted">Kami menerima undangan untuk mengisi workshop seni di sekolah atau instansi.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="jadwal" class="section-padding bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Jadwal Latihan Rutin</h2>
            <p class="text-muted">Terbuka untuk semua member Sanggar KJD</p>
        </div>
        
        <div class="table-responsive table-custom">
            <table class="table mb-0 bg-white">
                <thead>
                    <tr>
                        <th>Nama Tarian</th>
                        <th>Jam Mulai</th>
                        <th>Jam Selesai</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold">Tari Pendet</td>
                        <td class="text-gold fw-bold">09:00 WIB</td>
                        <td>10:30 WIB</td>
                        <td class="text-center"><span class="badge bg-success rounded-pill px-3 py-2">Tersedia</span></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Tari Pendet (Lanjutan)</td>
                        <td class="text-gold fw-bold">09:00 WIB</td>
                        <td>10:30 WIB</td>
                        <td class="text-center"><span class="badge bg-success rounded-pill px-3 py-2">Tersedia</span></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Tari Piring</td>
                        <td class="text-gold fw-bold">10:30 WIB</td>
                        <td>12:00 WIB</td>
                        <td class="text-center"><span class="badge bg-success rounded-pill px-3 py-2">Tersedia</span></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Tari Jaipong</td>
                        <td class="text-gold fw-bold">12:30 WIB</td>
                        <td>14:00 WIB</td>
                        <td class="text-center"><span class="badge bg-success rounded-pill px-3 py-2">Tersedia</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<section id="galeri" class="section-padding bg-dark text-white">
    <div class="container">
        <h2 class="text-center fw-bold mb-5">Galeri Kegiatan</h2>
        <div class="row g-3">
            <div class="col-md-4"><img src="assets/img/gallery1.png" class="img-gallery shadow"></div>
            <div class="col-md-4"><img src="assets/img/gallery2.png" class="img-gallery shadow"></div>
            <div class="col-md-4"><img src="assets/img/gallery3.png" class="img-gallery shadow"></div>
        </div>
    </div>
</section>

<footer class="py-5 bg-white border-top">
    <div class="container text-center">
        <h4 class="fw-bold text-gold">SANGGAR KJD</h4>
        <p class="text-muted mb-4">Membangun Karakter Bangsa Lewat Seni Tari Tradisional.</p>
        <div class="d-flex justify-content-center gap-3 mb-4 fs-4">
            <a href="#" class="text-dark"><i class="bi bi-instagram"></i></a>
            <a href="#" class="text-dark"><i class="bi bi-youtube"></i></a>
            <a href="https://wa.me/6281113809088" class="text-dark"><i class="bi bi-whatsapp"></i></a>
        </div>
        <p class="small text-muted">&copy; 2026 Sanggar Tari Kandank Jurank Doank. All Rights Reserved.</p>
    </div>
</footer>

<div class="modal fade" id="loginModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold">Login User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="proses/login.php">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-gold w-100 py-3 mt-2 rounded-3">MASUK KE DASHBOARD</button>
                    <p class="text-center mt-3 small">Mau booking tapi belum punya akun? <a href="javascript:void(0)" class="text-gold fw-bold" data-bs-toggle="modal" data-bs-target="#registerModal">Daftar Akun</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="registerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-gold text-white">
                <h5 class="modal-title fw-bold">Buat Akun Pelanggan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="proses/register.php">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" placeholder="Budi Santoso" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Untuk login" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Nomor Telepon</label>
                        <input type="number" name="no_telp" class="form-control" placeholder="0812..." required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Min. 6 Karakter" required>
                    </div>
                    <button type="submit" class="btn btn-gold w-100 py-3 mt-3 rounded-3 shadow-sm">DAFTAR SEKARANG</button>
                </div>
            </form>
        </div>
    </div>
</div>

<a href="https://wa.me/6281113809088" class="wa-float" target="_blank">
    <i class="bi bi-whatsapp"></i>
</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>