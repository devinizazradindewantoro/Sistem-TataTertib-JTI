<?php
abstract class User {
    protected $username;
    protected $password;
    protected $conn;

    public function __construct($conn, $username, $password) {
        $this->conn = $conn;
        $this->username = $username;
        $this->password = $password;
    }

    // Metode abstrak untuk login
    abstract public function login();
}
?>
