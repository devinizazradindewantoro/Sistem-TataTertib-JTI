<?php
// Memulai session
session_start();

// Mengatur jalur file koneksi
$path = realpath('../config/koneksi.php');
if (!$path) {
    die("File koneksi.php tidak ditemukan. Cek jalur direktori dan pastikan file ada di '../config/koneksi.php'.");
}
include $path;

// Cek apakah email tersedia di session
if (isset($_SESSION['email'])) {
    // Ambil email admin dari session
    $email = $_SESSION['email'];

    // Ambil nama admin dari database berdasarkan email
    $sql = "SELECT nama FROM Civitas.Admin WHERE email = ?";
    $params = array($email);
    $stmt = sqlsrv_query($conn, $sql, $params);

    // Debug untuk cek query
    if ($stmt === false) {
        die("Query error: " . print_r(sqlsrv_errors(), true));
    }

    // Debug hasil query
    $admin = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    if ($admin) {
        $nama_admin = $admin['nama'];
        // Hapus atau nonaktifkan baris berikut:
        // echo "Nama admin ditemukan: " . $nama_admin; // Debug
    } else {
        die("Data admin tidak ditemukan di database.");
    }
    
} else {
    // Jika email tidak ada di session, tampilkan pesan error
    die("Session 'email' tidak ditemukan. Pastikan Anda sudah login.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapor !</title>
    <link rel="stylesheet" href="../Mahasiswa_menu/dashboard_style.css">
    <script>
    function toggleSubmenu() {
        var submenu = document.getElementById("submenu");
        if (submenu.style.display === "none") {
        submenu.style.display = "block";
        } else {
        submenu.style.display = "none";
        }
    }
    </script>
</head>
<body>
    <div class="sidebar">
    <div class="logo">
        <img src="../Mahasiswa_menu/JTI_logo.png" alt="Logo">
        <h1>Lapor!</h1>
    </div>
    <ul>
        <li><a href="Dashboard_Admin.php">Beranda</a></li>
        <li>
        <a href="#" onclick="toggleSubmenu()">Kelola Pengguna</a>
        <ul id="submenu" class="submenu">
            <li><a href="Kelola_Dosen.php">Kelola Dosen</a></li>
            <li><a href="Kelola_Mahasiswa.php">Kelola Mahasiswa</a></li>
        </ul>
        </li>
        <li><a href="Kelola_Laporan.php">Kelola Laporan</a></li>
    </ul>
    </div>
    
    <div class="main">
        <div class="header">
            <div class="icons">
                <span>🔄</span>
                <span>💬</span>
                <span>✉️</span>
                <span>🔔</span>
            </div>
            <div class="user">
                <img src="../Lainnya/profil_logo.png" alt="User Profile">
                <p>Welcome, <?php echo htmlspecialchars($nama_admin); ?>!</p> <!-- Menampilkan nama admin -->
            </div>
        </div>
        <div class="content">
            <h2>BERANDA</h2>
            <h3>Buku Panduan</h3>
            <p>Sebelum melakukan pelaporan dimohon untuk membaca Buku Panduan pada Link Berikut:</p>
            <a href="https://jti.polinema.ac.id">Buku_Panduan.pdf (misal)</a>
        </div>
    </div>
</body>
</html>