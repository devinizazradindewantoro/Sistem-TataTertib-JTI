<?php
// Koneksi ke database
$serverName = "DESKTOP-KE7VNRK";
$connectionOptions = array(
    "Database" => "pelapor",
    "Uid" => "",
    "PWD" => ""
);
$conn = sqlsrv_connect($serverName, $connectionOptions);

if (!$conn) {
    die("Koneksi ke database gagal: " . print_r(sqlsrv_errors(), true));
}

session_start();

// Load kelas yang dibutuhkan
require_once './classes/Mahasiswa.php';
require_once './classes/Dosen.php';
require_once './classes/Admin.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

    if (empty($username) || empty($password)) {
        echo "<script>alert('Username atau password tidak boleh kosong!');</script>";
    } else {
        $user = null;

        // Tentukan kelas berdasarkan role
        switch ($role) {
            case 'Mahasiswa':
                $user = new Mahasiswa($conn, $username, $password);
                break;
            case 'Dosen':
                $user = new Dosen($conn, $username, $password);
                break;
            case 'Admin':
                $user = new Admin($conn, $username, $password);
                break;
            default:
                echo "<script>alert('Role tidak valid!');</script>";
                exit();
        }

        // Jalankan login
        if ($user) {
            $user->login();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>JTI Lapor!</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="Mahasiswa_menu/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="Mahasiswa_menu/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="Mahasiswa_menu/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="Mahasiswa_menu/bg.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <a href="#" class="h1"><b><img src="Mahasiswa_menu/jti_logo.png" alt="JTI" class="event-image" width="40px"></b> Lapor!</a>
        </div>
        <div class="card-body">
            <p class="login-box-msg">Login sesuai dengan data pada SIAKAD</p>

            <form method="post">
                <div class="input-group mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="role"></label>
                    <select name="role" class="form-control">
                        <option value="Admin">Admin</option>
                        <option value="Dosen">Dosen</option>
                        <option value="Mahasiswa">Mahasiswa</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </div>
            </form>

            <p class="mb-1">
                <a href="forgot-password.html">I forgot my password</a>
            </p>
        </div>
    </div>
</div>

</body>
</html>
