<?php
// Memulai session
session_start();

// Mengatur jalur file koneksi
$path = realpath('../config/koneksi.php');
if (!$path) {
    die("File koneksi.php tidak ditemukan. Cek jalur direktori dan pastikan file ada di '../config/koneksi.php'.");
}
include $path;

// Cek apakah email admin tersedia di session
if (!isset($_SESSION['email'])) {
    die("Admin tidak terdaftar, harap login.");
}

// Ambil data mahasiswa dari database
$sql = "SELECT id_mahasiswa, nim, nama, prodi, kelas, no_hp_ortu, alamat FROM Civitas.Mahasiswa";
$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die("Query error: " . print_r(sqlsrv_errors(), true));
}

// Jika ada id_mahasiswa yang dikirim via GET untuk edit
if (isset($_GET['id_mahasiswa'])) {
    $id_mahasiswa = $_GET['id_mahasiswa'];
    
    // Ambil data mahasiswa yang ingin diedit
    $sql_edit = "SELECT * FROM Civitas.Mahasiswa WHERE id_mahasiswa = ?";
    $params_edit = array($id_mahasiswa);
    $stmt_edit = sqlsrv_query($conn, $sql_edit, $params_edit);
    
    if ($stmt_edit === false) {
        die("Query error saat mengambil data mahasiswa: " . print_r(sqlsrv_errors(), true));
    }

    $mahasiswa = sqlsrv_fetch_array($stmt_edit, SQLSRV_FETCH_ASSOC);
    if (!$mahasiswa) {
        die("Mahasiswa tidak ditemukan.");
    }

    // Handle form submission for editing
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nim = $_POST['nim'];
        $nama = $_POST['nama'];
        $prodi = $_POST['prodi'];
        $kelas = $_POST['kelas'];
        $no_hp_ortu = $_POST['no_hp_ortu'];
        $alamat = $_POST['alamat'];

        // Update data mahasiswa
        $sql_update = "UPDATE Civitas.Mahasiswa SET nim = ?, nama = ?, prodi = ?, kelas = ?, no_hp_ortu = ?, alamat = ? WHERE id_mahasiswa = ?";
        $params_update = array($nim, $nama, $prodi, $kelas, $no_hp_ortu, $alamat, $id_mahasiswa);
        $stmt_update = sqlsrv_query($conn, $sql_update, $params_update);

        if ($stmt_update === false) {
            die("Query error saat update: " . print_r(sqlsrv_errors(), true));
        }

        echo "<script>alert('Data Mahasiswa berhasil diupdate!'); window.location.href = 'Kelola_Mahasiswa.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Mahasiswa</title>
    <link rel="stylesheet" href="../Mahasiswa_menu/dashboard_style.css">
    <link rel="stylesheet" href="kelola_mahasiswa_style.css">
</head>
<body>
    <div class="main">
        <div class="header">
            <h2>Kelola Identitas Mahasiswa</h2>
        </div>

        <div class="content">
            <?php if (isset($_GET['id_mahasiswa'])): ?>
                <!-- Formulir Edit Mahasiswa -->
                <form method="POST">
                    <label for="nim">NIM:</label>
                    <input type="text" id="nim" name="nim" value="<?php echo htmlspecialchars($mahasiswa['nim']); ?>" required>

                    <label for="nama">Nama Mahasiswa:</label>
                    <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($mahasiswa['nama']); ?>" required>

                    <label for="prodi">Program Studi:</label>
                    <input type="text" id="prodi" name="prodi" value="<?php echo htmlspecialchars($mahasiswa['prodi']); ?>" required>

                    <label for="kelas">Kelas:</label>
                    <input type="text" id="kelas" name="kelas" value="<?php echo htmlspecialchars($mahasiswa['kelas']); ?>" required>

                    <label for="no_hp_ortu">Nomor HP Orang Tua:</label>
                    <input type="text" id="no_hp_ortu" name="no_hp_ortu" value="<?php echo htmlspecialchars($mahasiswa['no_hp_ortu']); ?>" required>

                    <label for="alamat">Alamat:</label>
                    <input type="text" id="alamat" name="alamat" value="<?php echo htmlspecialchars($mahasiswa['alamat']); ?>" required>

                    <button type="submit">Update Data</button>
                </form>
            <?php else: ?>
                <!-- Tabel Identitas Mahasiswa -->
                <table>
                    <thead>
                        <tr>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>Program Studi</th>
                            <th>Kelas</th>
                            <th>Nomor HP Orang Tua</th>
                            <th>Alamat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($mahasiswa = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($mahasiswa['nim']); ?></td>
                                <td><?php echo htmlspecialchars($mahasiswa['nama']); ?></td>
                                <td><?php echo htmlspecialchars($mahasiswa['prodi']); ?></td>
                                <td><?php echo htmlspecialchars($mahasiswa['kelas']); ?></td>
                                <td><?php echo htmlspecialchars($mahasiswa['no_hp_ortu']); ?></td>
                                <td><?php echo htmlspecialchars($mahasiswa['alamat']); ?></td>
                                <td><a href="Kelola_Mahasiswa.php?id_mahasiswa=<?php echo $mahasiswa['id_mahasiswa']; ?>">Edit</a></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
