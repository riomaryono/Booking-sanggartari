<?php
session_start();
include "../config/koneksi.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Tarian | Admin KJD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root { --sidebar-dark: #1e293b; --primary: #4361ee; }
        body { font-family: 'Inter', sans-serif; background: #f8f9fc; margin: 0; }

        /* Sidebar - TETAP SESUAI STYLE ASLI ANDA */
        .sidebar { width: 260px; height: 100vh; position: fixed; background: var(--sidebar-dark); padding: 1.5rem; transition: 0.3s; z-index: 1000; overflow-y: auto; }
        .sidebar-brand { color: #fff; font-weight: bold; font-size: 1.2rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 10px; }
        .sidebar a { color: #94a3b8; text-decoration: none; padding: 12px; display: flex; align-items: center; border-radius: 8px; margin-bottom: 5px; transition: 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.1); color: #fff; }
        .sidebar i { margin-right: 10px; }

        .main-content { margin-left: 260px; transition: 0.3s; min-height: 100vh; }
        .top-nav { background: #fff; padding: 1rem 2rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; }
        .sidebar.hide { transform: translateX(-260px); }
        .main-content.full { margin-left: 0; }

        .card-table { background: #fff; border-radius: 12px; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .img-tarian { width: 80px; height: 50px; object-fit: cover; border-radius: 6px; }
    </style>
</head>
<body>

<?php include "../partials/sidebar.php"; ?>

<div class="main-content" id="mainContent">
    <header class="top-nav">
        <i class="bi bi-list fs-3 me-3" style="cursor:pointer;" onclick="toggleSidebar()"></i>
        <h5 class="fw-bold mb-0">Manajemen Data Tarian</h5>
    </header>

    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">Daftar Tarian</h4>
            <a href="#" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahTarianModal">
                <i class="bi bi-plus-lg me-2"></i>Tambah Tarian
            </a>
        </div>

        <div class="card card-table overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase" style="font-size: 0.8rem;">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Gambar</th>
                            <th>Nama Tarian</th>
                            <th>Asal & Alamat</th>
                            <th>Harga Dasar</th>
                            <th>Min. Penari</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($koneksi, "SELECT * FROM tarian ORDER BY id DESC");
                        while($data = mysqli_fetch_assoc($query)) { ?>
                            <tr>
                                <td class="ps-4 text-muted">#<?= $data['id']; ?></td>
                                <td>
                                    <?php if(!empty($data['gambar'])): ?>
                                        <img src="../uploads/<?= $data['gambar']; ?>" class="img-tarian shadow-sm">
                                    <?php else: ?>
                                        <div class="bg-light rounded text-muted d-flex align-items-center justify-content-center" style="width:80px; height:50px; font-size:0.7rem;">No Image</div>
                                    <?php endif; ?>
                                </td>
                                <td><span class="fw-bold text-dark"><?= $data['nama_tarian']; ?></span></td>
                                <td>
                                    <span class="badge bg-info-subtle text-info rounded-pill px-3 mb-1"><?= $data['asal_daerah']; ?></span><br>
                                    <small class="text-muted"><i class="bi bi-geo-alt"></i> <?= $data['alamat']; ?></small>
                                </td>
                                <td><span class="fw-bold text-success">Rp <?= number_format($data['harga_dasar'] ?? 0, 0, ',', '.'); ?></span></td>
                                <td><span class="badge bg-secondary"><?= $data['minimal_penari']; ?> Orang</span></td>
                                <td class="text-center">
                                    <div class="btn-group gap-2">
                                        <button class="btn btn-sm btn-light border text-warning edit-btn"
                                            data-id="<?= $data['id']; ?>"
                                            data-nama="<?= $data['nama_tarian']; ?>"
                                            data-asal="<?= $data['asal_daerah']; ?>"
                                            data-alamat="<?= $data['alamat']; ?>"
                                            data-harga="<?= $data['harga_dasar']; ?>"
                                            data-min="<?= $data['minimal_penari']; ?>"
                                            data-deskripsi="<?= $data['deskripsi']; ?>"
                                            data-bs-toggle="modal" data-bs-target="#editTarianModal">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light border text-danger delete-btn"
                                            data-id="<?= $data['id']; ?>"
                                            data-nama="<?= $data['nama_tarian']; ?>"
                                            data-bs-toggle="modal" data-bs-target="#hapusTarianModal">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tambahTarianModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="tambah_tarian_proses.php" method="post" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Data Tarian</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row">
              <div class="col-md-6 mb-3"><label>Nama Tarian</label><input type="text" name="nama_tarian" class="form-control" required></div>
              <div class="col-md-6 mb-3"><label>Asal Daerah</label><input type="text" name="asal_daerah" class="form-control" required></div>
          </div>
          <div class="row">
              <div class="col-md-6 mb-3"><label>Harga Dasar (Rp)</label><input type="number" name="harga_dasar" class="form-control" required></div>
              <div class="col-md-6 mb-3"><label>Minimal Penari</label><input type="number" name="minimal_penari" class="form-control" value="1" required></div>
          </div>
          <div class="mb-3"><label>Alamat Lokasi</label><input type="text" name="alamat" class="form-control" placeholder="Contoh: Jl. Sudirman No. 10" required></div>
          <div class="mb-3"><label>Deskripsi</label><textarea name="deskripsi" class="form-control" rows="3" required></textarea></div>
          <div class="mb-3"><label>Gambar</label><input type="file" name="gambar" class="form-control"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="editTarianModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="formEditTarian" action="edit_tarian_proses.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" id="editTarianId">
        <div class="modal-header">
          <h5 class="modal-title">Edit Data Tarian</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row">
              <div class="col-md-6 mb-3"><label>Nama Tarian</label><input type="text" name="nama_tarian" id="editNamaTarian" class="form-control" required></div>
              <div class="col-md-6 mb-3"><label>Asal Daerah</label><input type="text" name="asal_daerah" id="editAsalDaerah" class="form-control" required></div>
          </div>
          <div class="row">
              <div class="col-md-6 mb-3"><label>Harga Dasar (Rp)</label><input type="number" name="harga_dasar" id="editHargaTarian" class="form-control" required></div>
              <div class="col-md-6 mb-3"><label>Minimal Penari</label><input type="number" name="minimal_penari" id="editMinPenari" class="form-control" required></div>
          </div>
          <div class="mb-3"><label>Alamat Lokasi</label><input type="text" name="alamat" id="editAlamatTarian" class="form-control" required></div>
          <div class="mb-3"><label>Deskripsi</label><textarea name="deskripsi" id="editDeskripsi" class="form-control" rows="3" required></textarea></div>
          <div class="mb-3"><label>Gambar</label><input type="file" name="gambar" class="form-control"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="hapusTarianModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog text-center">
    <div class="modal-content">
      <form id="formHapusTarian" action="hapus_tarian.php" method="get">
        <input type="hidden" name="id" id="hapusTarianId">
        <div class="modal-header">
          <h5 class="modal-title">Hapus Tarian</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p>Yakin ingin menghapus tarian <strong id="hapusTarianNama"></strong>?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Hapus</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar(){
    document.getElementById('sidebar').classList.toggle('hide');
    document.getElementById('mainContent').classList.toggle('full');
}

document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', () => {
        const d = button.dataset;
        document.getElementById('editTarianId').value = d.id;
        document.getElementById('editNamaTarian').value = d.nama;
        document.getElementById('editAsalDaerah').value = d.asal;
        document.getElementById('editAlamatTarian').value = d.alamat;
        document.getElementById('editHargaTarian').value = d.harga;
        document.getElementById('editMinPenari').value = d.min;
        document.getElementById('editDeskripsi').value = d.deskripsi;
    });
});

document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', () => {
        document.getElementById('hapusTarianId').value = button.dataset.id;
        document.getElementById('hapusTarianNama').textContent = button.dataset.nama;
    });
});
</script>
</body>
</html>