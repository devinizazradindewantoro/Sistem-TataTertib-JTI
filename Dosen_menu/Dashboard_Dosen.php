<?php
// Memulai session
session_start();

// Mengatur jalur file koneksi
$path = realpath('../config/koneksi.php');
if (!$path) {
    die("File koneksi.php tidak ditemukan. Cek jalur direktori dan pastikan file ada di '../config/koneksi.php'.");
}
include $path;

// Cek apakah nip tersedia di session
if (isset($_SESSION['nip'])) {
    // Ambil nip dari session
    $nip = $_SESSION['nip'];

    // Ambil nama dosen dari database berdasarkan nip
    $sql = "SELECT nama FROM Civitas.Dosen WHERE nip = ?";
    $params = array($nip);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die("Query error: " . print_r(sqlsrv_errors(), true));
    }

    $dosen = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    if ($dosen) {
        $nama_dosen = $dosen['nama'];
    } else {
        $nama_dosen = 'Dosen Tidak Ada';
    }
} else {
    // Jika nip tidak ada di session, tampilkan pesan error
    $nama_dosen = 'Dosen Tidak Ditemukan';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dosen</title>
    <link rel="stylesheet" href="../Mahasiswa_menu/dashboard_style.css"> <!-- Pastikan jalur benar -->
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="../Mahasiswa_menu/jti_logo.png" alt="Logo"> <!-- Pastikan file gambar ada -->
            <h1>Lapor!</h1>
        </div>
        <ul>
            <li><a href="Dashboard_Dosen.php">Beranda</a></li>
            <li><a href="Melaporkan.html">Melaporkan</a></li>
            <li><a href="Memantau.php">History Laporan</a></li>
        </ul>
    </div>
    <div class="main">
        <div class="header">
            <div class="icons">
                <!-- Tambahkan icon atau fitur lain jika diperlukan -->
            </div>
            <div class="user">
                <img src="../Lainnya/profil_logo.png" alt="User Profile"> <!-- Pastikan file gambar ada -->
                <p>Welcome, <?php echo htmlspecialchars($nama_dosen); ?>!</p> <!-- Menampilkan nama dosen -->
            </div>
        </div>
        <div class="content">
            <h2>BERANDA</h2>
            <h3>Buku Panduan</h3>
            <p>Sebelum melakukan pelaporan dimohon untuk membaca Buku Panduan pada Link Berikut:</p>
            <a href="https://jti.polinema.ac.id/index.php/buku-pedoman-akademik-d3/">Buku_Panduan.pdf (misal)</a>
        </div>
    </div>
</body>
</html>
