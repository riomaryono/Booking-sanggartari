<?php
session_start();
include "../config/koneksi.php"; 
$conn = $koneksi; 

if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'customer') {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$nama    = $_SESSION['nama_lengkap'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Dokumentasi | Sanggar KJD</title>
    
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

        /* --- SIDEBAR (SAMA PERSIS DENGAN KATALOG) --- */
        .sidebar { 
            width: 260px; 
            height: 100vh; 
            position: fixed; 
            background: var(--sidebar-dark); 
            padding: 1.5rem; 
            transition: 0.3s; 
            z-index: 1100; 
        }
        .sidebar-brand { color: #fff; font-weight: bold; font-size: 1.2rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 10px; }
        .sidebar a { color: #94a3b8; text-decoration: none; padding: 12px; display: flex; align-items: center; border-radius: 8px; margin-bottom: 5px; transition: 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.1); color: #fff; }
        .sidebar i { margin-right: 10px; }

        /* --- MAIN CONTENT --- */
        .main-content { margin-left: 260px; transition: 0.3s; min-height: 100vh; }

        /* --- TOP NAV --- */
        .top-nav { 
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem; 
            border-bottom: 1px solid #eef2f7; 
            display: flex; 
            align-items: center; 
            justify-content: space-between;
            position: sticky; 
            top: 0; 
            z-index: 1000;
        }

        /* --- GALLERY GRID & CARD --- */
        .photo-card {
            border: none; 
            border-radius: 24px; 
            background: var(--white); 
            overflow: hidden; 
            height: 100%; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.03); 
            display: flex; 
            flex-direction: column; 
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            padding: 12px;
        }
        .photo-card:hover { 
            transform: translateY(-8px); 
            box-shadow: 0 20px 40px rgba(67, 97, 238, 0.15); 
        }

        .gallery-item {
            position: relative;
            border-radius: 18px;
            overflow: hidden;
            aspect-ratio: 1/1;
            cursor: pointer;
        }
        .gallery-item img { width: 100%; height: 100%; object-fit: cover; transition: 0.6s ease; }
        .gallery-overlay {
            position: absolute; inset: 0; background: rgba(67, 97, 238, 0.7);
            display: flex; align-items: center; justify-content: center;
            opacity: 0; transition: 0.3s; backdrop-filter: blur(2px);
        }
        .gallery-item:hover .gallery-overlay { opacity: 1; }
        .gallery-item:hover img { transform: scale(1.1); }

        /* --- MOBILE RESPONSIVE & OVERLAY --- */
        .overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1050;
        }
        .overlay.active { display: block; }

        @media (max-width: 768px) { 
            .sidebar { margin-left: -260px; } 
            .sidebar.show { margin-left: 0; }
            .main-content { margin-left: 0; } 
        }

        /* Lightbox Custom */
        .lightbox-img { border-radius: 24px; border: 5px solid white; box-shadow: 0 25px 50px rgba(0,0,0,0.3); max-height: 80vh; }
    </style>
</head>
<body>

    <div class="overlay" id="overlay"></div>

    <?php include "sidebar_customer.php"; ?>

   <div class="main-content" id="content">
        <nav class="top-nav">
            <div class="d-flex align-items-center">
<button id="btn-hamburger" class="btn btn-light me-3 shadow-sm" style="border-radius: 10px;">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <div>
                    <h5 class="mb-0 fw-bold">Galeri Dokumentasi</h5>
                    <p class="text-muted small mb-0 d-none d-sm-block">Momen seni Sanggar KJD</p>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-sm-block">
                    <span class="fw-bold small d-block"><?= htmlspecialchars($nama) ?></span>
                    <span class="badge bg-primary-subtle text-primary p-1 px-2" style="font-size: 0.65rem;">CUSTOMER</span>
                </div>
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:42px; height:42px;">
                    <i class="bi bi-person-fill fs-5"></i>
                </div>
            </div>
        </nav>

        <div class="container-fluid p-4">
            <div class="mb-4 d-flex justify-content-between align-items-end">
                <div>
                    <h4 class="fw-bold mb-1">Momen Terbaru</h4>
                    <div class="bg-primary" style="width: 50px; height: 4px; border-radius: 10px;"></div>
                </div>
            </div>

            <div class="row g-4">
                <?php
                $query = mysqli_query($conn, "SELECT * FROM galeri ORDER BY id DESC");
                while($row = mysqli_fetch_assoc($query)) {
                ?>
                <div class="col-6 col-md-4 col-xl-3">
                    <div class="photo-card">
                        <div class="gallery-item" onclick="viewPhoto('../assets/img/galeri/<?= $row['foto'] ?>', '<?= htmlspecialchars($row['judul']) ?>')">
                            <img src="../assets/img/galeri/<?= $row['foto'] ?>" alt="<?= htmlspecialchars($row['judul']) ?>" loading="lazy">
                            <div class="gallery-overlay">
                                <i class="bi bi-zoom-in text-white fs-1"></i>
                            </div>
                        </div>
                        <div class="pt-3 px-1">
                            <h6 class="fw-bold text-dark text-truncate mb-1"><?= htmlspecialchars($row['judul']) ?></h6>
                            <small class="text-muted d-block"><i class="bi bi-calendar-event me-1"></i><?= date('d M Y', strtotime($row['created_at'])) ?></small>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="photoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 bg-transparent">
                <div class="modal-body p-0 text-center">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                    <img src="" id="targetImg" class="img-fluid lightbox-img">
                    <div class="mt-3">
                        <div class="bg-white d-inline-block px-4 py-2 rounded-pill shadow">
                            <span id="targetCaption" class="fw-bold text-dark"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // 1. Definisikan variabelnya dulu di atas biar bisa dipake semua fungsi
    const btnHamburger = document.getElementById('btn-hamburger');
    const sidebar = document.querySelector('.sidebar');
    const content = document.getElementById('content');
    const overlay = document.getElementById('overlay'); // Pastikan lo punya div id="overlay" di HTML

    // 2. Logic Toggle Sidebar (Desktop & Mobile)
    btnHamburger.addEventListener('click', function() {
        if (window.innerWidth > 768) {
            // Logic Desktop: Mainin Margin
            if (sidebar.style.marginLeft === "-260px") {
                sidebar.style.marginLeft = "0";
                content.style.marginLeft = "260px";
            } else {
                sidebar.style.marginLeft = "-260px";
                content.style.marginLeft = "0";
            }
        } else {
            // Logic Mobile: Mainin Class (Muncul dari kiri)
            sidebar.classList.toggle('show');
            if (overlay) overlay.classList.toggle('active');
        }
    });

    // 3. Logic Tutup Sidebar pas klik Overlay (khusus Mobile)
    if (overlay) {
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            overlay.classList.remove('active');
        });
    }

    // 4. Fungsi Galeri
    function viewPhoto(url, title) {
        document.getElementById('targetImg').src = url;
        document.getElementById('targetCaption').innerText = title;
        var myModal = new bootstrap.Modal(document.getElementById('photoModal'));
        myModal.show();
    }
</script>
</body>
</html>