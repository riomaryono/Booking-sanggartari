<?php
include "../config/koneksi.php";

// Pastikan ada ID yang dikirim melalui URL
if (!isset($_GET['id'])) {
    echo "ID tidak ditemukan.";
    exit;
}

$id = $_GET['id'];

// Ambil data detail booking dengan JOIN lengkap untuk mendapatkan nama customer dan nama tarian
$query = mysqli_query($koneksi, "SELECT b.*, u.nama_lengkap, u.no_telp, t.nama_tarian 
                                 FROM bookings b 
                                 JOIN users u ON b.user_id = u.id 
                                 JOIN tarian t ON b.id_tarian = t.id 
                                 WHERE b.id = '$id'");
$d = mysqli_fetch_assoc($query);

// Jika data tidak ditemukan
if (!$d) { 
    echo "<script>alert('Data tidak ditemukan!'); window.close();</script>"; 
    exit; 
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #INV-<?= $d['id'] ?> - <?= $d['nama_lengkap'] ?></title>
    <style>
        /* PENGATURAN KERTAS A4 */
        @page {
            size: A4;
            margin: 15mm;
        }

        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            color: #333; 
            background: #f5f5f5; 
            margin: 0; 
            padding: 0; 
        }

        /* Kontainer Utama - Ukuran pas A4 */
        .invoice-box { 
            width: 180mm; 
            margin: 20px auto; 
            padding: 10mm; 
            background: #fff; 
            border: 1px solid #eee;
            position: relative;
            box-sizing: border-box;
        }

        .w-100 { width: 100%; border-collapse: collapse; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        /* HEADER STYLE */
        .header-section {
            border-bottom: 3px solid #4361ee;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .brand h1 { margin: 0; color: #4361ee; text-transform: uppercase; font-size: 26pt; letter-spacing: 1px; }
        .brand p { margin: 5px 0 0 0; font-size: 10pt; color: #666; }

        .invoice-id h2 { margin: 0; font-size: 22pt; color: #333; }
        .invoice-id p { margin: 0; font-size: 11pt; color: #4361ee; font-weight: bold; }

        /* INFO SECTION */
        .info-table { margin-bottom: 40px; }
        .info-table td { vertical-align: top; font-size: 10pt; line-height: 1.5; }
        .info-title { 
            color: #888; 
            font-size: 9pt; 
            font-weight: bold; 
            text-transform: uppercase; 
            border-bottom: 1px solid #eee; 
            display: block; 
            margin-bottom: 8px;
        }

        /* TABLE ITEMS */
        table.items { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 30px; 
        }
        table.items th { 
            background: #f8fafc; 
            color: #4361ee; 
            padding: 12px; 
            text-align: left; 
            text-transform: uppercase;
            font-size: 9pt;
            border-bottom: 2px solid #4361ee;
        }
        table.items td { 
            padding: 15px 12px; 
            border-bottom: 1px solid #f1f5f9; 
            font-size: 10pt;
            vertical-align: top;
        }

        /* TOTALS */
        .total-table { width: 40%; margin-left: auto; }
        .total-table td { padding: 8px 5px; font-size: 10pt; }
        .grand-total { 
            font-size: 14pt; 
            font-weight: bold; 
            color: #4361ee; 
            border-top: 2px solid #4361ee; 
        }

        /* STEMPEL LUNAS */
        .stamp-lunas {
            position: absolute;
            top: 45%;
            left: 15%;
            border: 4px double #22c55e;
            color: #22c55e;
            padding: 10px 25px;
            font-size: 25pt;
            font-weight: bold;
            text-transform: uppercase;
            transform: rotate(-20deg);
            opacity: 0.7;
            border-radius: 10px;
            pointer-events: none;
        }

        /* FOOTER */
        .footer {
            margin-top: 60mm;
            text-align: center;
            font-size: 9pt;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        /* MEDIA PRINT SETTINGS */
        @media print {
            body { background: #fff; }
            .no-print { display: none; }
            .invoice-box { 
                margin: 0; 
                border: none; 
                width: 100%; 
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: center; padding: 20px; background: #333;">
        <button onclick="window.print()" style="padding: 12px 30px; background: #4361ee; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 11pt;">
            MULAI CETAK (A4)
        </button>
        <button onclick="window.close()" style="padding: 12px 30px; background: #666; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            KEMBALI
        </button>
    </div>

    <div class="invoice-box">
        <div class="stamp-lunas">LUNAS</div>

        <table class="w-100 header-section">
            <tr>
                <td class="brand">
                    <h1>SANGGAR KJD</h1>
                    <p>Jl. Contoh Lokasi Sanggar No. 45, Kota<br>WhatsApp: 0812-xxxx-xxxx | Email: sanggarkjd@gmail.com</p>
                </td>
                <td class="text-right invoice-id">
                    <h2>INVOICE</h2>
                    <p>#INV-<?= date('Ymd', strtotime($d['tanggal_booking'])) . $d['id'] ?></p>
                </td>
            </tr>
        </table>

        <table class="w-100 info-table">
            <tr>
                <td style="width: 50%;">
                    <span class="info-title">Ditagihkan Kepada:</span>
                    <strong><?= $d['nama_lengkap'] ?></strong><br>
                    <?= $d['no_telp'] ?><br>
                    <?= $d['kategori_daerah'] ?>
                </td>
                <td style="width: 50%;" class="text-right">
                    <span class="info-title">Detail Pemesanan:</span>
                    Tanggal Acara: <strong><?= date('d F Y', strtotime($d['tanggal_booking'])) ?></strong><br>
                    Jam Mulai: <strong><?= substr($d['jam_mulai'],0,5) ?> WIB</strong><br>
                    Metode: Transfer Bank
                </td>
            </tr>
        </table>

        <table class="items">
            <thead>
                <tr>
                    <th style="width: 55%;">Deskripsi Layanan</th>
                    <th style="width: 20%;" class="text-center">Kategori</th>
                    <th style="width: 25%;" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong style="font-size: 11pt;"><?= $d['nama_tarian'] ?></strong><br>
                        <small style="color: #666;">Catatan: <?= $d['catatan'] ?: 'Tidak ada catatan khusus.' ?></small>
                    </td>
                    <td class="text-center"><?= $d['jenis_acara'] ?></td>
                    <td class="text-right">Rp <?= number_format($d['total_harga'], 0, ',', '.') ?></td>
                </tr>
            </tbody>
        </table>

        <table class="total-table">
            <tr>
                <td>Subtotal</td>
                <td class="text-right">Rp <?= number_format($d['total_harga'], 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td>Pajak / Biaya Lain</td>
                <td class="text-right">Rp 0</td>
            </tr>
            <tr class="grand-total">
                <td>TOTAL BAYAR</td>
                <td class="text-right">Rp <?= number_format($d['total_harga'], 0, ',', '.') ?></td>
            </tr>
        </table>

        <div class="footer">
            <p>Terima kasih telah mempercayai Sanggar Seni KJD untuk acara Anda.</p>
            <p><strong>Nota ini adalah bukti pembayaran yang sah.</strong></p>
            <p style="margin-top: 5px; font-style: italic;">Dicetak otomatis pada: <?= date('d/m/Y H:i') ?></p>
        </div>
    </div>

    <script>
        // Membuka dialog print secara otomatis saat halaman terbuka
        window.onload = function() {
            // window.print(); // Hapus tanda komentar jika ingin otomatis print
        }
    </script>
</body>
</html>