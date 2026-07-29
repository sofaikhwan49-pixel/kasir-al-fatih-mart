<?php
class Database {
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $db_name = "toko_kasir_db";
    public $conn;

    public function getConnection() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass);

        if ($this->conn->connect_error) {
            die("Koneksi gagal: " . $this->conn->connect_error);
        }

        $this->conn->query("CREATE DATABASE IF NOT EXISTS $this->db_name");
        $this->conn->select_db($this->db_name);
        
        $this->initTables();

        return $this->conn;
    }

    private function initTables() {
        $this->conn->query("CREATE TABLE IF NOT EXISTS produk (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama_produk VARCHAR(100) NOT NULL,
            harga DECIMAL(6,0) NOT NULL,
            stok INT NOT NULL
        )");

        $this->conn->query("CREATE TABLE IF NOT EXISTS transaksi (
            id INT AUTO_INCREMENT PRIMARY KEY,
            total_bayar DECIMAL(6,0) NOT NULL,
            tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        $this->conn->query("CREATE TABLE IF NOT EXISTS detail_transaksi (
            id INT AUTO_INCREMENT PRIMARY KEY,
            transaksi_id INT,
            produk_id INT,
            jumlah INT,
            subtotal DECIMAL(6,0),
            FOREIGN KEY (transaksi_id) REFERENCES transaksi(id)
        )");

        $this->conn->query("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            nama_lengkap VARCHAR(100) NOT NULL,
            role ENUM('admin','kasir') NOT NULL DEFAULT 'kasir',
            aktif TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        $this->seedDefaultUsers();
    }

    // =========================
    // SEED AKUN DEFAULT
    // Hanya dibuat jika tabel users masih kosong,
    // supaya tidak menimpa akun yang sudah diubah adminnya.
    // =========================
    private function seedDefaultUsers() {
        $cek = $this->conn->query("SELECT COUNT(*) AS total FROM users");
        $row = $cek ? $cek->fetch_assoc() : ['total' => 0];

        if ((int)$row['total'] === 0) {
            $stmt = $this->conn->prepare(
                "INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)"
            );

            $akun_default = [
                ['admin', 'admin123', 'Administrator', 'admin'],
                ['kasir', 'kasir123', 'Kasir Toko', 'kasir'],
            ];

            foreach ($akun_default as $a) {
                $hash = password_hash($a[1], PASSWORD_DEFAULT);
                $stmt->bind_param("ssss", $a[0], $hash, $a[2], $a[3]);
                $stmt->execute();
            }
        }
    }
}
?>