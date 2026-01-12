<?php 
// Mengambil nama file aktif untuk class 'active'
$page = basename($_SERVER['PHP_SELF']); 
?>

<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="bg-primary rounded p-1 d-inline-flex align-items-center justify-content-center" style="width:32px; height:32px;">
            <i class="bi bi-stars text-white"></i>
        </div>
        <span class="ms-1">Panel Customer</span>
    </div>

    <a href="index.php" class="<?= $page == 'index.php' ? 'active' : '' ?>">
        <i class="bi bi-house-door-fill me-2"></i> Dashboard
    </a>

    <small class="text-uppercase text-secondary ms-2 mt-3 d-block" style="font-size: 0.7rem; letter-spacing: 1px;">Pemesanan</small>
    
    <a href="buat_booking.php" class="<?= $page == 'buat_booking.php' ? 'active' : '' ?>">
        <i class="bi bi-calendar-plus-fill me-2"></i> Booking
    </a>

    <a href="riwayat_booking.php" class="<?= $page == 'riwayat_booking.php' ? 'active' : '' ?>">
        <i class="bi bi-clock-history me-2"></i> Riwayat Booking
    </a>

    <small class="text-uppercase text-secondary ms-2 mt-3 d-block" style="font-size: 0.7rem; letter-spacing: 1px;">Informasi Sanggar</small>
    
    <a href="jenis_tarian.php" class="<?= $page == 'jadwal_tarian.php' ? 'active' : '' ?>">
        <i class="bi bi-music-note-list me-2"></i> Jenis Tarian
    </a>
    
    <a href="galeri.php" class="<?= $page == 'galeri.php' ? 'active' : '' ?>">
        <i class="bi bi-images me-2"></i> Galeri Foto
    </a>

    <small class="text-uppercase text-secondary ms-2 mt-3 d-block" style="font-size: 0.7rem; letter-spacing: 1px;">Akun</small>
    
    <a href="profil.php" class="<?= $page == 'profil.php' ? 'active' : '' ?>">
        <i class="bi bi-person-circle me-2"></i> Profil Saya
    </a>

    <div class="mt-auto pt-3">
        <hr class="text-secondary opacity-25">
        <a href="../logout.php" class="nav-link text-danger fw-bold" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
            <i class="bi bi-box-arrow-left me-2"></i> Logout
        </a>
    </div>
</div>