<?php
// Memulai session
session_start();

// Koneksi ke database
$path = realpath('../config/koneksi.php');
if (!$path) {
    die("File koneksi.php tidak ditemukan.");
}
include $path;

// Periksa apakah admin sudah login
if (!isset($_SESSION['email'])) {
    die("Admin tidak terdaftar, harap login.");
}

// Ambil email admin dari session
$email = $_SESSION['email'];

// Query untuk mendapatkan nama admin
$sql = "SELECT nama FROM Civitas.Admin WHERE email = ?";
$params = array($email);
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die("Query error: " . print_r(sqlsrv_errors(), true));
}

$admin = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
if ($admin) {
    $nama_admin = $admin['nama'];
} else {
    die("Admin tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapor! - Kelola Laporan Pelanggaran</title>
    <link rel="stylesheet" href="../Mahasiswa_menu/dashboard_style.css">
    <link rel="stylesheet" href="../Dosen_menu/input_pelaporan.css">
    <link rel="stylesheet" href="../Dosen_menu/styles.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="../Mahasiswa_menu/jti_logo.png" alt="Logo">
            <h1>Lapor!</h1>
        </div>
        <ul>
            <li><a href="Dashboard_Admin.php">Beranda</a></li>
            <li><a href="#" onclick="toggleSubmenu()">Kelola Pengguna</a>
                <ul id="submenu" class="submenu">
                    <li><a href="Kelola_Dosen.php">Kelola Dosen</a></li>
                    <li><a href="Kelola_Mahasiswa.php">Kelola Mahasiswa</a></li>
                </ul>
            </li>
            <li><a href="Kelola_Laporan.php">Kelola Laporan</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main">
        <div class="header">
            <div class="user">
                <img src="../Lainnya/profil_logo.png" alt="User Profile">
                <span>Welcome, <?php echo htmlspecialchars($nama_admin); ?>!</span>
            </div>
            <h2>Kelola Laporan Pelanggaran</h2>
        </div>
        <div class="content">
            <table>
                <thead>
                    <tr>
                        <th>ID Laporan</th>
                        <th>Tanggal</th>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Tingkat Pelanggaran</th>
                        <th>Bukti</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query untuk mengambil data laporan dari tabel Pelaporan_Admin
                    $sql = "SELECT id_pelaporan_admin, tanggal, nim, nama_mahasiswa, tingkat_pelanggaran, bukti, status FROM Tatib.Pelaporan_Admin";
                    $stmt = sqlsrv_query($conn, $sql);

                    if ($stmt === false) {
                        die("Query error: " . print_r(sqlsrv_errors(), true));
                    }

                    // Tampilkan data laporan
                    if (sqlsrv_has_rows($stmt)) {
                        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id_pelaporan_admin']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['tanggal']->format('Y-m-d')) . "</td>";
                            echo "<td>" . htmlspecialchars($row['nim']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama_mahasiswa']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['tingkat_pelanggaran']) . "</td>";
                            
                            // Periksa apakah bukti ada
                            $bukti = htmlspecialchars($row['bukti']);
                            $file_path = '../Dosen_menu/uploads/' . $bukti; // Menambahkan direktori relatif jika perlu
                            if (!empty($file_path) && file_exists($file_path)) {
                                // Jika file ada, tampilkan link
                                echo "<td><a href='" . $file_path . "' target='_blank'>Lihat Bukti</a></td>";
                            } else {
                                // Jika file tidak ada
                                echo "<td>Tidak ada bukti</td>";
                            }

                            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                            echo "<td><a href='verifikasi.php?id=" . htmlspecialchars($row['id_pelaporan_admin']) . "'>Verifikasi</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>Tidak ada laporan pelanggaran.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- JavaScript for toggle submenu -->
    <script>
        function toggleSubmenu() {
            var submenu = document.getElementById('submenu');
            submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
        }
    </script>
</body>
</html>
