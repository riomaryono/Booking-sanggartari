<?php
session_start();
include "../config/koneksi.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Cusromer | Admin KJD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

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

        .card-table {
            background: #fff;
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body>

    <?php include "../partials/sidebar.php"; ?>

    <div class="main-content" id="mainContent">
        <header class="top-nav">
            <i class="bi bi-list fs-3 me-3" style="cursor:pointer;" onclick="toggleSidebar()"></i>
            <h5 class="fw-bold mb-0">Manajemen Pengguna</h5>
        </header>

        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Data Customer</h2>
                    <p class="text-muted">Daftar akun pelanggan yang aktif dalam sistem</p>
                </div>
                <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambahCustomer">
                    <i class="bi bi-person-plus me-2"></i>Tambah Customer
                </button>
            </div>

            <div class="card card-table p-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Lengkap</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Tanggal Join</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $query = mysqli_query($koneksi, "SELECT * FROM users WHERE level = 'customer' ORDER BY id DESC");
                            if (mysqli_num_rows($query) > 0) {
                                while ($row = mysqli_fetch_assoc($query)) {
                            ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                    <i class="bi bi-person text-primary"></i>
                                                </div>
                                                <span class="fw-bold"><?= $row['nama_lengkap']; ?></span>
                                            </div>
                                        </td>
                                        <td><?= $row['username']; ?></td>
                                        <td><span class="badge rounded-pill bg-primary-subtle text-primary px-3">Customer</span></td>
                                        <td class="text-muted"><?= date('d/m/Y', strtotime($row['created_at'])); ?></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-primary border-0 me-1" 
                                                    data-bs-toggle="modal" data-bs-target="#modalEditCustomer"
                                                    data-id="<?= $row['id']; ?>"
                                                    data-nama="<?= $row['nama_lengkap']; ?>"
                                                    data-username="<?= $row['username']; ?>"
                                                    data-telp="<?= $row['no_telp']; ?>">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <a href="hapus_customer.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Hapus akun customer ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                            <?php 
                                } 
                            } else { 
                                echo '<tr><td colspan="6" class="text-center py-5 text-muted">Belum ada customer yang terdaftar.</td></tr>'; 
                            } 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambahCustomer" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-plus me-2"></i>Tambah Customer</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="tambah_customer_proses.php" method="POST">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama lengkap..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Username</label>
                            <input type="text" name="username" class="form-control" placeholder="username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nomor Telepon</label>
                            <input type="text" name="no_telp" class="form-control" placeholder="08..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Password..." required>
                        </div>
                        <input type="hidden" name="level" value="customer">
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditCustomer" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-warning text-dark border-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../admin/edit_customer.php" method="POST">
                    <div class="modal-body p-4">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="edit-nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Username</label>
                            <input type="text" name="username" id="edit-username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nomor Telepon</label>
                            <input type="text" name="no_telp" id="edit-telp" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password Baru <small class="text-muted">(Kosongkan jika tidak ganti)</small></label>
                            <input type="password" name="password" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('hide');
            document.getElementById('mainContent').classList.toggle('full');
        }

        // Script untuk mengisi data ke Modal Edit
        const modalEdit = document.getElementById('modalEditCustomer');
        modalEdit.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-nama');
            const username = button.getAttribute('data-username');
            const telp = button.getAttribute('data-telp');

            modalEdit.querySelector('#edit-id').value = id;
            modalEdit.querySelector('#edit-nama').value = nama;
            modalEdit.querySelector('#edit-username').value = username;
            modalEdit.querySelector('#edit-telp').value = telp;
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>