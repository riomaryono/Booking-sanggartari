<?php
include "../config/koneksi.php";
$id = $_GET['id'];
mysqli_query($koneksi, "DELETE FROM bookings WHERE id = '$id'");
header("Location: booking_ditolak.php");
?>