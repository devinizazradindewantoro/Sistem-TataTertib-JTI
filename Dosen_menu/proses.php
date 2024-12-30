<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil data dari form
    $nim = $_POST['nim'];
    $nama = $_POST['nama_mahasiswa'];
    $tanggal = $_POST['tanggal'];
    $deskripsi_pelanggaran = $_POST['deskripsi_pelanggaran'];
    $tingkat = $_POST['tingkat_pelanggaran'];

    // Pastikan session NIP dosen sudah diset
    if (!isset($_SESSION['nip']) || empty($_SESSION['nip'])) {
        echo "<script>alert('Anda harus login terlebih dahulu!'); window.location = 'login.php';</script>";
        exit();
    }

    $nip_dosen = $_SESSION['nip'];

    // Direktori untuk menyimpan file upload
    $upload_dir = "uploads/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_name = basename($_FILES["bukti"]["name"]);
    $target_file = $upload_dir . $file_name;

    // Proses upload file
    if (move_uploaded_file($_FILES["bukti"]["tmp_name"], $target_file)) {
        // Koneksi ke database SQL Server
        $serverName = "DESKTOP-KE7VNRK"; // Ubah sesuai konfigurasi server Anda
        $connectionOptions = [
            "Database" => "pelapor", // Nama database
            "Uid" => "",            // Username
            "PWD" => ""             // Password
        ];
        $conn = sqlsrv_connect($serverName, $connectionOptions);

        if ($conn === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        // Cari id_dosen berdasarkan nip
        $query_dosen = "SELECT id_dosen FROM Civitas.Dosen WHERE nip = ?";
        $params_dosen = [$nip_dosen];
        $stmt_dosen = sqlsrv_query($conn, $query_dosen, $params_dosen);

        if ($stmt_dosen && sqlsrv_fetch($stmt_dosen)) {
            $id_dosen = sqlsrv_get_field($stmt_dosen, 0);

            // Query untuk menyimpan laporan
            $query = "INSERT INTO Tatib.Pelaporan_Admin 
                    (nim, nama_mahasiswa, tanggal, deskripsi_pelanggaran, tingkat_pelanggaran, bukti, id_dosen, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')";
            $params = [$nim, $nama, $tanggal, $deskripsi_pelanggaran, $tingkat, $file_name, $id_dosen];

            $stmt = sqlsrv_query($conn, $query, $params);

            if ($stmt) {
                echo "<script>alert('Laporan berhasil dikirim!'); window.location = 'Memantau.php';</script>";
            } else {
                echo "<script>alert('Gagal menyimpan laporan ke database.'); window.location = 'index.php';</script>";
            }
        } else {
            echo "<script>alert('NIP dosen tidak valid atau tidak ditemukan.'); window.location = 'login.php';</script>";
        }

        // Tutup koneksi ke database
        sqlsrv_close($conn);
    } else {
        echo "<script>alert('Gagal mengunggah bukti.'); window.location = 'index.php';</script>";
    }
}
?>
