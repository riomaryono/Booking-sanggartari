<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['user_id'])) { 
    header("Location: ../index.php"); 
    exit; 
}

// Ambil data daerah tarian unik untuk dropdown filter
$daerah_tari_query = mysqli_query($koneksi, "SELECT DISTINCT asal_daerah FROM tarian WHERE asal_daerah != ''");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buat Booking | Sanggar KJD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --sidebar-dark: #1e293b; --primary: #4361ee; }
        body { font-family: 'Inter', sans-serif; background: #f8f9fc; margin: 0; }
        
        /* --- STYLE SIDEBAR ANDA (TIDAK DIRUBAH) --- */
        .sidebar { width: 260px; height: 100vh; position: fixed; background: var(--sidebar-dark); padding: 1.5rem; transition: 0.3s; z-index: 1000; overflow-y: auto; }
        .sidebar-brand { color: #fff; font-weight: bold; font-size: 1.2rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 10px; }
        .sidebar a { color: #94a3b8; text-decoration: none; padding: 12px; display: flex; align-items: center; border-radius: 8px; margin-bottom: 5px; transition: 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.1); color: #fff; }
        .sidebar i { margin-right: 10px; }
        .sidebar.hide { transform: translateX(-260px); }

        .main-content { margin-left: 260px; transition: 0.3s; min-height: 100vh; padding: 2rem; }
        .main-content.full { margin-left: 0; }

        /* --- PERBAIKAN NAV UNTUK ICON HAMBURGER --- */
        .top-nav { 
            background: transparent; 
            display: flex; 
            align-items: center; 
            margin-bottom: 2rem;
        }

        #btn-hamburger { 
            background: #fff; 
            border: none; 
            padding: 10px 14px; 
            border-radius: 10px; 
            color: var(--sidebar-dark); 
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            cursor: pointer;
            z-index: 1001; /* Di atas sidebar */
        }

        .card-booking { background: #fff; border: none; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
        .price-display { background: #f0f7ff; padding: 1.5rem; border-radius: 12px; border: 2px dashed #4361ee; }
    </style>
</head>
<body>

<?php include "sidebar_customer.php"; ?>

<div class="main-content" id="content">
    <div class="top-nav">
        <button id="btn-hamburger">
            <i class="bi bi-list fs-4"></i>
        </button>
        <h4 class="fw-bold mb-0 ms-3">Form Reservasi</h4>
    </div>

    <div class="container-fluid">
        <div class="card card-booking p-4">
            <form action="../proses/booking.php" method="POST">
                <div class="row g-4">
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Wilayah Customer (Biaya Transportasi)</label>
                        <select name="kategori_daerah" id="kategori_daerah" class="form-select" required onchange="syncData()">
                            <option value="" data-transpor="0">-- Pilih Wilayah Anda --</option>
                            <option value="Jakarta" data-transpor="50000">Jakarta (+Rp 50.000)</option>
                            <option value="Bogor" data-transpor="150000">Bogor (+Rp 150.000)</option>
                            <option value="Depok" data-transpor="100000">Depok (+Rp 100.000)</option>
                            <option value="Tangerang" data-transpor="100000">Tangerang (+Rp 100.000)</option>
                            <option value="Bekasi" data-transpor="100000">Bekasi (+Rp 100.000)</option>
                            <option value="Luar Jabodetabek" data-transpor="300000">Luar Jabodetabek (+Rp 300.000)</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Detail Alamat Lengkap</label>
                        <textarea name="kategori_alamat" class="form-control" rows="1" placeholder="Jl. Contoh No. 123..." required></textarea>
                    </div>

                    <hr>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-primary">Filter Kategori Tari (Asal Daerah)</label>
                        <select id="filter_daerah_tari" class="form-select" disabled>
                            <option value="">-- Otomatis Terisi --</option>
                            <?php 
                            mysqli_data_seek($daerah_tari_query, 0);
                            while($d = mysqli_fetch_assoc($daerah_tari_query)): 
                            ?>
                                <option value="<?= $d['asal_daerah'] ?>"><?= $d['asal_daerah'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
    <label class="form-label fw-bold text-primary">Pilih Tarian | Harga Per Orang</label>
    <select name="id_tarian" id="id_tarian" class="form-select" required onchange="syncData()">
        <option value="" data-harga="0" data-daerah="">-- Pilih Tarian --</option>
        <?php 
        $tarian_query = mysqli_query($koneksi, "SELECT * FROM tarian");
        while($t = mysqli_fetch_assoc($tarian_query)): 
        ?>
            <option value="<?= $t['id'] ?>" data-harga="<?= $t['harga_dasar'] ?>" data-daerah="<?= $t['asal_daerah'] ?>">
                <?= $t['nama_tarian'] ?> (Rp <?= number_format($t['harga_dasar'],0,',','.') ?>)
            </option>
        <?php endwhile; ?>
    </select>
    <div class="form-text text-muted mt-1">
        <i class="bi bi-info-circle"></i> *Harga yang tertera di atas adalah biaya sewa per 1 orang penari.
    </div>
</div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Jenis Acara</label>
                        <select name="jenis_acara" id="jenis_acara" class="form-select" required onchange="syncData()">
                            <option value="Pernikahan" data-add="500000">Pernikahan (+500rb)</option>
                            <option value="Acara Adat" data-add="200000">Acara Adat (+200rb)</option>
                            <option value="Wisuda" data-add="200000">Wisuda (+200rb)</option>
                            <option value="Event Perusahaan" data-add="750000">Event Perusahaan (+750rb)</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Jumlah Penari</label>
                        <input type="number" name="jumlah_penari" id="jumlah_penari" class="form-control" value="1" min="1" required oninput="syncData()">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Durasi (Jam)</label>
                        <input type="number" name="durasi_jam" id="durasi_jam" class="form-control" value="1" min="1" required oninput="syncData()">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tanggal Acara</label>
                        <input type="date" name="tanggal_booking" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Jam Mulai</label>
                        <input type="time" name="jam_mulai" class="form-control" required>
                    </div>
                </div>

                <div class="price-display text-center mt-5">
                    <span class="text-muted small fw-bold">ESTIMASI TOTAL BIAYA</span>
                    <h1 class="fw-bold text-primary mt-1" id="total_display">Rp 0</h1>
                    <input type="hidden" name="total_harga" id="total_input">
                </div>

                <button type="submit" class="btn btn-primary w-100 mt-4 py-3 fw-bold rounded-pill shadow">SIMPAN RESERVASI</button>
            </form>
        </div>
    </div>
</div>

<script>
    // FUNGSI HITUNG HARGA DAN SINKRONISASI
    function syncData() {
        const tarianSelect = document.getElementById('id_tarian');
        const selectedTarian = tarianSelect.options[tarianSelect.selectedIndex];
        
        // Update Filter Daerah Otomatis
        const daerahAsal = selectedTarian.getAttribute('data-daerah');
        document.getElementById('filter_daerah_tari').value = daerahAsal ? daerahAsal : "";

        // Logika Hitung Total
        const hargaDasar = parseInt(selectedTarian.getAttribute('data-harga')) || 0;
        const daerahCust = document.getElementById('kategori_daerah');
        const biayaTrans = parseInt(daerahCust.options[daerahCust.selectedIndex]?.getAttribute('data-transpor')) || 0;
        const acara = document.getElementById('jenis_acara');
        const biayaAcara = parseInt(acara.options[acara.selectedIndex]?.getAttribute('data-add')) || 0;
        const penari = parseInt(document.getElementById('jumlah_penari').value) || 0;
        const durasi = parseInt(document.getElementById('durasi_jam').value) || 0;

        const total = (hargaDasar * penari * durasi) + biayaTrans + biayaAcara;

        document.getElementById('total_display').innerText = "Rp " + total.toLocaleString('id-ID');
        document.getElementById('total_input').value = total;
    }

    // FUNGSI TOGGLE SIDEBAR (MEMPERBAIKI ICON HAMBURGER)
    const btnHamburger = document.getElementById('btn-hamburger');
    const sidebar = document.querySelector('.sidebar');
    const content = document.getElementById('content');

    btnHamburger.addEventListener('click', function() {
        // Toggle class sesuai style CSS asli Anda
        sidebar.classList.toggle('hide');
        content.classList.toggle('full');
    });
</script>
</body>
</html>