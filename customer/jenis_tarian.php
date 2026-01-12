<?php
session_start();
include "../config/koneksi.php"; 
$conn = $koneksi; 

// 1. Proteksi Halaman
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'customer') {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$nama    = $_SESSION['nama_lengkap'];

// 2. Ambil Data Tarian dari Database
$query = "SELECT * FROM tarian ORDER BY nama_tarian ASC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Katalog Tarian | KJD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { 
            --sidebar-dark: #1e293b; 
            --primary: #4361ee; 
            --soft-bg: #f8f9fc; 
            --white: #ffffff;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--soft-bg); 
            margin: 0; 
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar { width: 260px; height: 100vh; position: fixed; background: var(--sidebar-dark); padding: 1.5rem; transition: 0.3s; z-index: 1000; overflow-y: auto; }
        .sidebar-brand { color: #fff; font-weight: bold; font-size: 1.2rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 10px; }
        .sidebar a { color: #94a3b8; text-decoration: none; padding: 12px; display: flex; align-items: center; border-radius: 8px; margin-bottom: 5px; transition: 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.1); color: #fff; }
        .sidebar i { margin-right: 10px; }

        .main-content { margin-left: 260px; transition: 0.3s; min-height: 100vh; }
        .top-nav { background: #fff; padding: 1rem 2rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; }
        .sidebar.hide { transform: translateX(-260px); }
        .main-content.full { margin-left: 0; }

        /* --- MAIN CONTENT --- */
        .main-content { margin-left: 260px; transition: 0.3s ease; min-height: 100vh; }
        .main-content.full { margin-left: 0; }

        /* --- NAVIGATION --- */
        .top-nav { 
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem; 
            border-bottom: 1px solid #eef2f7; 
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 999;
        }

        #btn-hamburger {
            background: #f1f5f9; border: none;
            padding: 8px 12px; border-radius: 10px;
            color: var(--sidebar-dark);
        }

        /* --- DANCE CARD --- */
        .dance-card { 
            border: none; border-radius: 24px; background: var(--white); overflow: hidden; height: 100%; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.03); display: flex; flex-direction: column; 
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        .dance-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(67, 97, 238, 0.15); }
        .dance-img-wrapper { position: relative; height: 220px; overflow: hidden; }
        .dance-img { width: 100%; height: 100%; object-fit: cover; transition: 0.6s ease; }
        .dance-card:hover .dance-img { transform: scale(1.1); }
        .dance-body { padding: 1.5rem; flex-grow: 1; display: flex; flex-direction: column; }
        .dance-title { font-size: 1.15rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem; }
        .dance-text { font-size: 0.85rem; color: #64748b; line-height: 1.6; margin-bottom: 1.5rem; }
        
        .btn-booking { 
            margin-top: auto; background: var(--primary); color: white; border-radius: 14px; 
            padding: 12px; font-weight: 700; text-align: center; text-decoration: none; 
            display: flex; align-items: center; justify-content: center; gap: 8px; transition: 0.3s;
        }
        .btn-booking:hover { background: #2b46c4; color: white; box-shadow: 0 8px 15px rgba(67, 97, 238, 0.3); }

        /* --- MOBILE --- */
        .overlay {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); z-index: 1050;
        }
        .overlay.active { display: block; }

        @media (max-width: 768px) { 
            .sidebar { transform: translateX(-260px); } 
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; } 
        }
    </style>
</head>
<body>

    <div class="overlay" id="overlay"></div>

    <?php include "sidebar_customer.php"; ?>

    <div class="main-content" id="content">
        <nav class="top-nav">
            <div class="d-flex align-items-center">
                <button id="btn-hamburger" class="me-3 shadow-sm">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <div>
                    <h5 class="mb-0 fw-bold">Katalog Tarian</h5>
                    <p class="text-muted small mb-0 d-none d-sm-block">Temukan bakat Anda hari ini</p>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-sm-block">
                    <span class="fw-bold small d-block"><?= htmlspecialchars($nama) ?></span>
                    <span class="badge bg-primary-subtle text-primary p-1 px-2" style="font-size: 0.65rem;">CUSTOMER</span>
                </div>
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:42px; height:42px;">
                    <i class="bi bi-person-fill fs-5"></i>
                </div>
            </div>
        </nav>

        <div class="container-fluid p-4">
            <div class="mb-4">
                <h4 class="fw-bold mb-1">Tarian Tersedia</h4>
                <div class="bg-primary" style="width: 50px; height: 4px; border-radius: 10px;"></div>
            </div>

            <div class="row g-4">
                <?php if(mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                        <div class="dance-card">
                            <div class="dance-img-wrapper">
                                <img src="../assets/img/tarian/<?= $row['gambar'] ?>" class="dance-img" onerror="this.src='https://images.unsplash.com/photo-1547153760-18fc86324498?q=80&w=500'">
                            </div>
                            <div class="dance-body">
                                <h6 class="dance-title"><?= htmlspecialchars($row['nama_tarian']) ?></h6>
                                <p class="dance-text">Pelajari teknik dasar hingga mahir untuk tarian <?= htmlspecialchars($row['nama_tarian']) ?>.</p>
                                <a href="buat_booking.php?id_tarian=<?= $row['id'] ?>" class="btn-booking">
    <i class="bi bi-calendar-check-fill"></i> PILIH TARIAN
</a>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-folder2-open display-1 text-muted"></i>
                        <p class="text-muted mt-3">Belum ada data tarian.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        const btnHamburger = document.getElementById('btn-hamburger');
        const sidebar = document.querySelector('.sidebar');
        const content = document.getElementById('content');
        const overlay = document.getElementById('overlay');

        btnHamburger.addEventListener('click', function() {
            if (window.innerWidth > 768) {
                sidebar.classList.toggle('hide');
                content.classList.toggle('full');
            } else {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('active');
            }
        });

        overlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            overlay.classList.remove('active');
        });
    </script>
</body>
</html>