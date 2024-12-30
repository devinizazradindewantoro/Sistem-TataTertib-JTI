<?php
$koneksi = mysqli_connect('localhost', 'root', '', 'pelaporan');

// Cek koneksi
if (!$koneksi) {
    die("Koneksi Gagal: " . mysqli_connect_error());
} else {
    echo "Koneksi Berhasil";
}
?>
