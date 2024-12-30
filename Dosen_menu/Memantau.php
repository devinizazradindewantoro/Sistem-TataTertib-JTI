<?php
session_start();

// Cek apakah NIP dosen ada di session
if (!isset($_SESSION['nip'])) {
    echo "<script>alert('Anda harus login terlebih dahulu!'); window.location = 'login.php';</script>";
    exit();
}

$nip_dosen = $_SESSION['nip'];  // NIP dosen yang sedang login

// Koneksi ke database SQL Server
$serverName = "DESKTOP-KE7VNRK"; // Ganti dengan nama server SQL Anda
$connectionOptions = [
    "Database" => "pelapor", // Nama database Anda
    "Uid" => "",             // Username SQL Server Anda
    "PWD" => ""              // Password SQL Server Anda
];
$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Query untuk mengambil riwayat pelaporan berdasarkan NIP dosen
$query = "
    SELECT 
        p.nim, 
        p.nama_mahasiswa AS nama, 
        p.tanggal, 
        p.tingkat_pelanggaran AS tingkat, 
        p.bukti
    FROM Tatib.Pelaporan_Admin p
    JOIN Civitas.Dosen d ON p.id_dosen = d.id_dosen
    WHERE d.nip = ?
";
$params = [$nip_dosen];
$stmt = sqlsrv_query($conn, $query, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pelaporan</title>
    <link rel="stylesheet" href="../Mahasiswa_menu/styles.css"> <!-- Sesuaikan dengan file CSS Anda -->
    <link rel="stylesheet" href="../Mahasiswa_menu/dashboard_style.css"> <!-- Menambahkan style dashboard -->
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="../Mahasiswa_menu/jti_logo.png" alt="Logo">
            <h1>Lapor!</h1>
        </div>
        <ul>
            <li><a href="Dashboard_Dosen.php">Beranda</a></li>
            <li><a href="Memantau.php">History Laporan</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="header">
            <div class="user">
                <img src="../Lainnya/profil_logo.png" alt="User Profile">
                <span>Yth. Dosen</span>
            </div>
        </div>

        <div class="content">
            <h2>Riwayat Pelaporan</h2>

            <?php if (sqlsrv_has_rows($stmt) === false): ?>
                <p>Belum ada laporan untuk Anda.</p>
            <?php else: ?>
                <table border="1" class="table-bordered">
                    <thead>
                        <tr>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>Tanggal Pelanggaran</th>
                            <th>Tingkat Pelanggaran</th>
                            <th>Bukti</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['nim']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                <td><?php echo htmlspecialchars($row['tanggal']->format('Y-m-d')); ?></td>
                                <td><?php echo htmlspecialchars($row['tingkat']); ?></td>
                                <td>
                                    <a href="uploads/<?php echo htmlspecialchars($row['bukti']); ?>" target="_blank">Lihat Bukti</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <br>
            <a href="Dashboard_Dosen.php" class="btn-back">Kembali ke Halaman Utama</a>
        </div>
    </div>
</body>
</html>

<?php
// Menutup koneksi
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>
