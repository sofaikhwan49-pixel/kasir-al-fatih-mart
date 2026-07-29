<?php
class UserModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // =========================
    // CEK LOGIN
    // Mengembalikan data user (tanpa password) jika berhasil,
    // atau null jika username/password salah / akun nonaktif.
    // =========================
    public function authenticate($username, $password) {

        $stmt = $this->db->prepare(
            "SELECT id, username, password, nama_lengkap, role, aktif
             FROM users
             WHERE username = ?
             LIMIT 1"
        );
        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            return null; // username tidak ditemukan
        }

        if ((int)$user['aktif'] !== 1) {
            return null; // akun dinonaktifkan
        }

        if (!password_verify($password, $user['password'])) {
            return null; // password salah
        }

        unset($user['password']); // jangan pernah simpan hash ke session

        return $user;
    }

    public function getById($id) {
        $stmt = $this->db->prepare(
            "SELECT id, username, nama_lengkap, role, aktif
             FROM users
             WHERE id = ?
             LIMIT 1"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }
}
?>
