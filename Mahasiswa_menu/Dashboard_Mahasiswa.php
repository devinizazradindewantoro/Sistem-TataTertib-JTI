<?php
// Memulai session
session_start();

// Mengatur jalur file koneksi
$path = realpath('../config/koneksi.php');
if (!$path) {
    die("File koneksi.php tidak ditemukan. Cek jalur direktori dan pastikan file ada di '../config/koneksi.php'.");
}
include $path;

// Cek apakah nim tersedia di session
if (isset($_SESSION['nim'])) {
    // Ambil nim dari session
    $nim = $_SESSION['nim'];

    // Ambil data mahasiswa dari database berdasarkan nim
    $sql = "SELECT nama, prodi, kelas FROM Civitas.Mahasiswa WHERE nim = ?";
    $params = array($nim);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die("Query error: " . print_r(sqlsrv_errors(), true));
    }

    $mahasiswa = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    if ($mahasiswa) {
        $nama = $mahasiswa['nama'];
        $prodi = $mahasiswa['prodi'];
        $kelas = $mahasiswa['kelas'];
    } else {
        $nama = 'Nama Tidak Ditemukan';
        $prodi = 'Prodi Tidak Ditemukan';
        $kelas = 'Kelas Tidak Ditemukan';
    }
} else {
    // Jika nim tidak ada di session, redirect ke halaman login
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard Mahasiswa - Lapor!</title>
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="jti_logo.png" alt="Logo">
            <h1>Lapor!</h1>
        </div>
        <ul>
            <li><a href="Dashboard_Mahasiswa.php">Beranda</a></li>
            <li><a href="Info_Poin.php">Info Poin</a></li>
            <li><a href="History_laporan.php">History Laporan</a></li>
        </ul>
    </div>
    <div class="main">
        <div class="header">
            <div class="icons">
                <span>🔄</span>
                <span>💬</span>
                <span>✉</span>
                <span>🔔</span>
            </div>
            <div class="user">
                <img src="profil_logo.png" alt="User Profile">
                <p>Welcome, <?php echo htmlspecialchars($nama); ?> (<?php echo htmlspecialchars($prodi); ?> - <?php echo htmlspecialchars($kelas); ?>)</p> <!-- Menampilkan nama, prodi, dan kelas -->
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