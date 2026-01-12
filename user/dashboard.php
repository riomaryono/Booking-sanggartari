<?php
session_start();
include "../config/koneksi.php";
$id = $_SESSION['user']['id'];
$data = mysqli_query($koneksi,"SELECT * FROM jadwal WHERE status='Tersedia'");
?>
<h3>Booking Jadwal</h3>
<table border="1">
<tr><th>Tanggal</th><th>Jam</th><th>Aksi</th></tr>
<?php while($j=mysqli_fetch_assoc($data)){ ?>
<tr>
<td><?= $j['tanggal'] ?></td>
<td><?= $j['jam_mulai'] ?> - <?= $j['jam_selesai'] ?></td>
<td>
<form method="POST" action="../proses/booking.php">
<input type="hidden" name="jadwal_id" value="<?= $j['id'] ?>">
<button>Booking</button>
</form>
</td>
</tr>
<?php } ?>
</table>
