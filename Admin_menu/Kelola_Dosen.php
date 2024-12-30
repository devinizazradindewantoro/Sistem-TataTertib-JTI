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

// Ambil data dosen dari database
$sql = "SELECT id_dosen, nama, nip, email, no_hp FROM Civitas.Dosen";
$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die("Query error: " . print_r(sqlsrv_errors(), true));
}

// Jika ada id_dosen yang dikirim via GET untuk edit
if (isset($_GET['id_dosen'])) {
    $id_dosen = $_GET['id_dosen'];
    
    // Ambil data dosen yang ingin diedit
    $sql_edit = "SELECT * FROM Civitas.Dosen WHERE id_dosen = ?";
    $params_edit = array($id_dosen);
    $stmt_edit = sqlsrv_query($conn, $sql_edit, $params_edit);
    
    if ($stmt_edit === false) {
        die("Query error saat mengambil data dosen: " . print_r(sqlsrv_errors(), true));
    }

    $dosen = sqlsrv_fetch_array($stmt_edit, SQLSRV_FETCH_ASSOC);
    if (!$dosen) {
        die("Dosen tidak ditemukan.");
    }

    // Handle form submission for editing
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama = $_POST['nama'];
        $email = $_POST['email'];
        $nip = $_POST['nip'];
        $no_hp = $_POST['no_hp'];

        // Update data dosen
        $sql_update = "UPDATE Civitas.Dosen SET nama = ?, email = ?, nip = ?, no_hp = ? WHERE id_dosen = ?";
        $params_update = array($nama, $email, $nip, $no_hp, $id_dosen);
        $stmt_update = sqlsrv_query($conn, $sql_update, $params_update);

        if ($stmt_update === false) {
            die("Query error saat update: " . print_r(sqlsrv_errors(), true));
        }

        echo "<script>alert('Data Dosen berhasil diupdate!'); window.location.href = 'Kelola_Dosen.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Dosen</title>
    <link rel="stylesheet" href="../Mahasiswa_menu/dashboard_style.css">
    <link rel="stylesheet" href="kelola_dosen_style.css">
</head>
<body>
    <div class="main">
        <div class="header">
            <h2>Kelola Identitas Dosen</h2>
        </div>

        <div class="content">
            <?php if (isset($_GET['id_dosen'])): ?>
                <!-- Formulir Edit Dosen -->
                <form method="POST">
                    <label for="nama">Nama Dosen:</label>
                    <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($dosen['nama']); ?>" required>

                    <label for="email">Email Dosen:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($dosen['email']); ?>" required>

                    <label for="nip">NIP:</label>
                    <input type="text" id="nip" name="nip" value="<?php echo htmlspecialchars($dosen['nip']); ?>" required>

                    <label for="no_hp">Nomor HP:</label>
                    <input type="text" id="no_hp" name="no_hp" value="<?php echo htmlspecialchars($dosen['no_hp']); ?>" required>

                    <button type="submit">Update Data</button>
                </form>
            <?php else: ?>
                <!-- Tabel Identitas Dosen -->
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>NIP</th>
                            <th>Email</th>
                            <th>Nomor HP</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($dosen = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($dosen['nama']); ?></td>
                                <td><?php echo htmlspecialchars($dosen['nip']); ?></td>
                                <td><?php echo htmlspecialchars($dosen['email']); ?></td>
                                <td><?php echo htmlspecialchars($dosen['no_hp']); ?></td>
                                <td><a href="Kelola_Dosen.php?id_dosen=<?php echo $dosen['id_dosen']; ?>">Edit</a></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
