<?php
require_once 'User.php';

class Dosen extends User {
    public function login() {
        $sql = "SELECT nip, password FROM Civitas.Dosen WHERE nip = ?";
        $params = array($this->username);
        $stmt = sqlsrv_query($this->conn, $sql, $params);

        if ($stmt === false) {
            die("Query error: " . print_r(sqlsrv_errors(), true));
        }

        $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        if ($user && $this->password == $user['password']) {
            $_SESSION['nip'] = $user['nip'];
            header("Location: Dosen_menu/Dashboard_Dosen.php");
            exit();
        } else {
            echo "<script>alert('NIP atau password salah!');</script>";
        }
    }
}
?>
