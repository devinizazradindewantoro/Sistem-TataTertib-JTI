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
// Baca data dari file JSON
$laporan_file = 'laporan.json';
$laporan_data = file_exists($laporan_file) ? json_decode(file_get_contents($laporan_file), true) : [];

// Filter riwayat laporan berdasarkan nip_dosen yang sedang login
$filtered_laporan = array_filter($laporan_data, function ($item) use ($nim) {
    return isset($item['nim']) && $item['nim'] == $nim;  // Pastikan nip_dosen ada dalam data laporan
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapor!</title>
    <link rel="stylesheet" href="hl_styles.css">
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
            <li><a href="Sanksi.php">Sanksi</a></li>
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
                <img src="profil_logo.png" alt="User Profile">
                <p>Welcome, <?php echo htmlspecialchars($nama); ?> (<?php echo htmlspecialchars($prodi); ?> - <?php echo htmlspecialchars($kelas); ?>)</p> <!-- Menampilkan nama, prodi, dan kelas -->
            </div>
        </div>
        <div class="content">
            <h2>HISTORY LAPORAN</h2>
            <p>Berikut adalah riwayat pelanggaran yang telah anda lakukan:</p>
            <table>
                <thead>
                    <tr>
                        <th>TGL</th>
                        <th>NAMA</th>
                        <th>TINGKAT</th>
                        <th>BUKTI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filtered_laporan as $laporan): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($laporan['tanggal']); ?></td>
                                <td><?php echo htmlspecialchars($laporan['nama']); ?></td>
                                <td><?php echo htmlspecialchars($laporan['tingkat']); ?></td>
                                <td>
                                    <a href="../Dosen_menu/uploads/<?php echo htmlspecialchars($laporan['bukti']); ?>" target="_blank">Lihat Bukti</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
