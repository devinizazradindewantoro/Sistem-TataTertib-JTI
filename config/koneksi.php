<?php
$serverName = "DESKTOP-I45KNFU"; // Nama server SQL Server
$connectionOptions = array(
    "Database" => "pelapor", // Nama database
    "Uid" => "",  // Ganti dengan username SQL Server
    "PWD" => ""   // Ganti dengan password SQL Server
);

// Membuat koneksi ke SQL Server
$conn = sqlsrv_connect($serverName, $connectionOptions);

// Mengecek apakah koneksi berhasil
if (!$conn) {
    die(print_r(sqlsrv_errors(), true)); // Menampilkan kesalahan koneksi
}
?>
