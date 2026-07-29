<?php

require_once 'model/UserModel.php';

class AuthController {

    private $userModel;

    // Batas percobaan login sebelum harus menunggu (proteksi brute force sederhana)
    private $max_percobaan = 5;
    private $waktu_lockout = 60; // detik

    public function __construct($db) {
        $this->userModel = new UserModel($db);
    }

    // =========================
    // APAKAH USER SUDAH LOGIN?
    // =========================
    public function isLoggedIn() {
        return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
    }

    public function currentUser() {
        return $this->isLoggedIn() ? $_SESSION['user'] : null;
    }

    public function isAdmin() {
        $u = $this->currentUser();
        return $u && $u['role'] === 'admin';
    }

    // =========================
    // PROSES LOGIN
    // =========================
    public function handleLogin() {

        if (!isset($_POST['login'])) {
            return;
        }

        // CEK LOCKOUT SEDERHANA
        if (isset($_SESSION['login_gagal']) && $_SESSION['login_gagal'] >= $this->max_percobaan) {
            $sisa = $this->waktu_lockout - (time() - $_SESSION['login_gagal_waktu']);
            if ($sisa > 0) {
                setMessage("⛔ Terlalu banyak percobaan gagal. Coba lagi dalam {$sisa} detik.");
                return;
            }
            // waktu lockout sudah lewat, reset
            $_SESSION['login_gagal'] = 0;
        }

        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        if ($username === '' || $password === '') {
            setMessage("❌ Username dan password wajib diisi.");
            return;
        }

        $user = $this->userModel->authenticate($username, $password);

        if (!$user) {
            $_SESSION['login_gagal'] = isset($_SESSION['login_gagal']) ? $_SESSION['login_gagal'] + 1 : 1;
            $_SESSION['login_gagal_waktu'] = time();

            setMessage("❌ Username atau password salah.");
            return;
        }

        // LOGIN BERHASIL
        // Regenerate session id untuk mencegah session fixation
        session_regenerate_id(true);

        $_SESSION['user'] = $user;
        $_SESSION['login_gagal'] = 0;
        $_SESSION['cart'] = [];

        setMessage("✅ Selamat datang, " . $user['nama_lengkap'] . "!");
    }

    // =========================
    // LOGOUT
    // =========================
    public function handleLogout() {

        if (!isset($_GET['logout'])) {
            return;
        }

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();

        // Mulai session baru hanya untuk menampung pesan logout
        session_start();
        setMessage("👋 Anda berhasil logout.");
    }
}
?>
