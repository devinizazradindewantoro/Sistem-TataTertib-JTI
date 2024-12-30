<?php
require_once 'User.php';

class Admin extends User {
    public function login() {
        // Validasi input
        if (empty($this->username) || empty($this->password)) {
            echo "<script>alert('Email dan password wajib diisi!');</script>";
            return;
        }

        // Query untuk mengambil data admin berdasarkan email
        $sql = "SELECT id_admin, email, password FROM Civitas.Admin WHERE email = ?";
        $params = array($this->username);
        $stmt = sqlsrv_query($this->conn, $sql, $params);

        // Cek jika query gagal
        if ($stmt === false) {
            die("Query error: " . print_r(sqlsrv_errors(), true));
        }

        // Ambil hasil query
        $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        if ($user) {
            // Perbandingan langsung dengan password
            if ($this->password == $user['password']) {
                // Mulai session jika belum dimulai
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }

                // Set session untuk admin
                $_SESSION['id_admin'] = $user['id_admin'];
                $_SESSION['email'] = $user['email'];

                // Redirect ke halaman dashboard admin
                header("Location: Admin_menu/Dashboard_Admin.php");
                exit();
            } else {
                // Jika password salah
                echo "<script>alert('Password salah!');</script>";
            }
        } else {
            // Jika email tidak ditemukan
            echo "<script>alert('Email tidak ditemukan!');</script>";
        }
    }
}
?>
