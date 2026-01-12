<?php
$page = 'galeri.php';
include "auth_admin.php";
include "../config/koneksi.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Galeri | Admin KJD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-dark: #1e293b;
            --primary: #4361ee;
            --danger: #ef4444;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8f9fc;
            margin: 0;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            background: var(--sidebar-dark);
            padding: 1.5rem;
            transition: 0.3s;
            z-index: 1000;
            overflow-y: auto;
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

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .sidebar i {
            margin-right: 10px;
        }

        
        .main-content {
            margin-left: 260px;
            transition: 0.3s;
            min-height: 100vh;
        }

        .top-nav {
            background: #fff;
            padding: 1rem 2rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
        }

        .sidebar.hide {
            transform: translateX(-260px);
        }

         .main-content.full {
            margin-left: 0;
        }



        /* MODERN GALLERY DESIGN */
        .gallery-card {
            background: #fff;
            border: none;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .gallery-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .img-container {
            position: relative;
            width: 100%;
            padding-top: 75%; 
            overflow: hidden;
            background: #f1f5f9;
            cursor: zoom-in; /* Menandakan gambar bisa di-zoom */
        }

        .img-container img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .gallery-card:hover img {
            transform: scale(1.1);
        }

        /* Overlay Hapus */
        .btn-delete-overlay {
            position: absolute;
            top: 12px;
            right: 12px;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 2;
        }

        .gallery-card:hover .btn-delete-overlay {
            opacity: 1;
            transform: translateY(0);
        }

        .btn-delete {
            background: rgba(239, 68, 68, 0.9);
            color: white;
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
            text-decoration: none;
        }

        .btn-delete:hover { background: #dc2626; color: white; }

        .card-body-custom { padding: 15px; background: white; }
        .photo-title { font-size: 0.9rem; font-weight: 700; color: #1e293b; margin-bottom: 2px; }
        .photo-date { font-size: 0.75rem; color: #64748b; }

        /* Custom Style for Zoom Modal */
        #modalZoom .modal-content { background: none; border: none; }
        #modalZoom .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
    </style>
</head>
<body>

    <?php include "../partials/sidebar.php"; ?>

    <div class="main-content" id="mainContent">
        <header class="top-nav">
            <i class="bi bi-list fs-3 me-3" style="cursor:pointer;" onclick="toggleSidebar()"></i>
            <h5 class="fw-bold mb-0">Galeri Foto</h5>
        </header>

        <div class="container-fluid p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Koleksi Galeri</h2>
                    <p class="text-muted mb-0">Kelola memori dan dokumentasi kegiatan sanggar</p>
                </div>
                <button class="btn btn-primary rounded-pill px-4 py-2 shadow-sm d-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#modalUpload">
                    <i class="bi bi-cloud-arrow-up-fill fs-5 me-2"></i>Tambah Foto Baru
                </button>
            </div>

            <div class="row g-4">
                <?php
                $query = mysqli_query($koneksi, "SELECT * FROM galeri ORDER BY id DESC");
                if(mysqli_num_rows($query) > 0) {
                    while($row = mysqli_fetch_assoc($query)) {
                ?>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="card gallery-card">
                        <div class="img-container" onclick="zoomGambar('../assets/img/galeri/<?= $row['foto'] ?>', '<?= htmlspecialchars($row['judul']) ?>')">
                            <div class="btn-delete-overlay">
                                <a href="proses_hapus_galeri.php?id=<?= $row['id'] ?>" class="btn-delete shadow-sm" onclick="event.stopPropagation(); return confirm('Hapus foto ini secara permanen?')">
                                    <i class="bi bi-trash3-fill"></i>
                                </a>
                            </div>
                            <img src="../assets/img/galeri/<?= $row['foto'] ?>" alt="<?= htmlspecialchars($row['judul']) ?>">
                        </div>
                        <div class="card-body-custom">
                            <h6 class="photo-title text-truncate"><?= htmlspecialchars($row['judul']) ?></h6>
                            <p class="photo-date mb-0">
                                <i class="bi bi-calendar3 me-1"></i> <?= date('d M Y', strtotime($row['created_at'])) ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php 
                    }
                } else {
                ?>
                <div class="col-12 text-center py-5">
                    <div class="py-5">
                        <i class="bi bi-images text-light-emphasis" style="font-size: 5rem;"></i>
                        <h5 class="mt-3 text-muted">Belum ada koleksi foto</h5>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalUpload" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="fw-bold">Upload Dokumentasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="proses_upload_galeri.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Nama Kegiatan / Judul Foto</label>
                            <input type="text" name="judul" class="form-control p-3 bg-light border-0 shadow-sm" placeholder="Misal: Pentas Seni Akhir Tahun" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">File Gambar</label>
                            <div class="p-3 bg-light rounded-3 border-dashed text-center position-relative" style="border: 2px dashed #cbd5e1;">
                                <input type="file" name="foto" class="form-control opacity-0 position-absolute top-0 start-0 h-100 w-100" style="cursor: pointer;" accept="image/*" required id="inputFoto">
                                <div id="previewText">
                                    <i class="bi bi-image fs-1 text-secondary"></i>
                                    <p class="small text-muted mb-0">Klik untuk pilih atau drop foto di sini</p>
                                </div>
                            </div>
                        </div>
                        <button type="submit" name="upload" class="btn btn-primary w-100 p-3 fw-bold rounded-3 shadow">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalZoom" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-body p-0 text-center position-relative">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3 shadow-none" data-bs-dismiss="modal" aria-label="Close" style="z-index: 10;"></button>
                    <img src="" id="imgFull" class="img-fluid rounded-3" style="max-height: 85vh; width: 100%; object-fit: contain;">
                    <div class="p-3 bg-dark text-white rounded-bottom">
                        <h6 id="captionFull" class="mb-0 fw-bold"></h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('hide');
            document.getElementById('mainContent').classList.toggle('full');
        }

        // Fungsi Pop-up Zoom
        function zoomGambar(src, judul) {
            document.getElementById('imgFull').src = src;
            document.getElementById('captionFull').innerText = judul;
            var myModal = new bootstrap.Modal(document.getElementById('modalZoom'));
            myModal.show();
        }

        // Preview nama file upload
        document.getElementById('inputFoto').addEventListener('change', function(e){
            let fileName = e.target.files[0].name;
            document.getElementById('previewText').innerHTML = `
                <i class="bi bi-check-circle-fill fs-1 text-success"></i>
                <p class="small text-dark fw-bold mb-0">${fileName}</p>
            `;
        });
    </script>
</body>
</html>