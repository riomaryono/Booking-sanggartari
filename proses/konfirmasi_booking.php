<?php
include "../config/koneksi.php";
$id=$_GET['id'];
$s=$_GET['s'];
mysqli_query($koneksi,"UPDATE booking SET status='$s' WHERE id=$id");
header("Location: ../admin/dashboard.php");
