<?php 
// Mengambil nama file aktif untuk memberikan class 'active' pada menu
$page = basename($_SERVER['PHP_SELF']); 
?>

<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="bg-primary rounded p-1 d-inline-flex align-items-center justify-content-center" style="width:32px; height:32px;">
            <i class="bi bi-stars text-white"></i>
        </div>
        <span class="ms-1">Admin KJD</span>
    </div>
    

    <a href="dashboard.php" class="<?= $page == 'dashboard.php' ? 'active' : '' ?>">
        <i class="bi bi-grid-1x2-fill me-2"></i> Dashboard
    </a>

    <small class="text-uppercase text-secondary ms-2 mt-3 d-block" style="font-size: 0.7rem; letter-spacing: 1px;">Data Master</small>
    <a href="tarian.php" class="<?= $page == 'tarian.php' ? 'active' : '' ?>">
        <i class="bi bi-music-note-beamed me-2"></i> Data Tarian
    </a>
    <a href="jadwal.php" class="<?= $page == 'jadwal.php' ? 'active' : '' ?>">
        <i class="bi bi-calendar2-week-fill me-2"></i> Jadwal Pentas
    </a>
    <a href="customer.php" class="<?= $page == 'customer.php' ? 'active' : '' ?>">
        <i class="bi bi-people-fill me-2"></i> Data Customer
    </a>

    <small class="text-uppercase text-secondary ms-2 mt-3 d-block" style="font-size: 0.7rem; letter-spacing: 1px;">Transaksi</small>
    <a href="booking.php" class="<?= $page == 'booking_masuk.php' ? 'active' : '' ?>">
        <i class="bi bi-inbox-fill me-2"></i> Booking Masuk
    </a>
    <a href="booking_diterima.php" class="<?= $page == 'booking_diterima.php' ? 'active' : '' ?>">
        <i class="bi bi-check-circle-fill me-2"></i> Booking Diterima
    </a>
    <a href="booking_ditolak.php" class="<?= $page == 'booking_ditolak.php' ? 'active' : '' ?>">
        <i class="bi bi-x-circle-fill me-2"></i> Booking Ditolak
    </a>

    <a href="verifikasi_pembayaran.php" class="<?= ($page == 'verifikasi') ? 'active' : '' ?>">
    <i class="bi bi-cash-stack"></i>
    <span>Verifikasi Bayar</span>
</a>

    <small class="text-uppercase text-secondary ms-2 mt-3 d-block" style="font-size: 0.7rem; letter-spacing: 1px;">Media</small>
    <a href="galeri.php" class="<?= $page == 'galeri.php' ? 'active' : '' ?>">
        <i class="bi bi-images me-2"></i> Galeri
    </a>

    <small class="text-uppercase text-secondary ms-2 mt-3 d-block" style="font-size: 0.7rem; letter-spacing: 1px;">Laporan</small>
    <a href="laporan_booking.php" class="<?= $page == 'laporan_booking.php' ? 'active' : '' ?>">
        <i class="bi bi-file-earmark-text-fill me-2"></i> Laporan Booking
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

