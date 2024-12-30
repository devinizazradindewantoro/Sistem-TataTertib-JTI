<?php
require_once 'User.php';

class Mahasiswa extends User {
    public function login() {
        $sql = "SELECT nim, password FROM Civitas.Mahasiswa WHERE nim = ?";
        $params = array($this->username);
        $stmt = sqlsrv_query($this->conn, $sql, $params);

        if ($stmt === false) {
            die("Query error: " . print_r(sqlsrv_errors(), true));
        }

        $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        if ($user && $this->password == $user['password']) {
            $_SESSION['nim'] = $user['nim'];
            header("Location: Mahasiswa_menu/Dashboard_Mahasiswa.php");
            exit();
        } else {
            echo "<script>alert('NIM atau password salah!');</script>";
        }
    }
}
?>
