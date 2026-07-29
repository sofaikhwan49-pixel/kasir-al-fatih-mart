<?php

require_once 'model/ProdukModel.php';

class KasirController {

    private $produkModel;
    private $db;

    // =========================
    // MODE MAINTENANCE
    // true  = sistem maintenance
    // false = sistem normal
    // =========================
    public $maintenance = false;

    public function __construct($db) {

        $this->db = $db;

        $this->produkModel = new ProdukModel($db);
    }

    // =========================
    // CEK ROLE ADMIN (dari session, diisi oleh AuthController saat login)
    // =========================
    private function isAdmin() {
        return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
    }

    public function handleRequest() {

        // =========================
        // SESSION
        // =========================
        if (session_status() === PHP_SESSION_NONE) {

            session_start();
        }

        // =========================
        // CART
        // =========================
        if (!isset($_SESSION['cart'])) {

            $_SESSION['cart'] = [];
        }

        // =========================
        // CEK MAINTENANCE
        // =========================
        if ($this->maintenance) {

            setMessage("⚠️ Sistem sedang dalam perawatan. Silakan coba lagi nanti.");

            return;
        }

        // =========================
        // TAMBAH PRODUK (ADMIN ONLY)
        // =========================
        if (isset($_POST['add_product'])) {

            if (!$this->isAdmin()) {
                setMessage("⛔ Anda tidak memiliki akses untuk aksi ini.");
                return;
            }

            $nama = isset($_POST['nama_produk'])
                ? trim($_POST['nama_produk'])
                : '';

            $harga = isset($_POST['harga'])
                ? (int) $_POST['harga']
                : 0;

            $stok = isset($_POST['stok'])
                ? (int) $_POST['stok']
                : 0;

            // VALIDASI
            if ($nama == '' || $harga <= 0 || $stok < 0) {

                setMessage("❌ Data produk tidak valid.");

                return;
            }

            $this->produkModel->add($nama, $harga, $stok);

            setMessage("✅ Produk berhasil ditambahkan!");
        }

        // =========================
        // KOSONGKAN SEMUA DATA (ADMIN ONLY)
        // =========================
        if (isset($_POST['generate_data'])) {

            if (!$this->isAdmin()) {
                setMessage("⛔ Anda tidak memiliki akses untuk aksi ini.");
                return;
            }

            // Hapus detail transaksi
            $this->db->query("
                DELETE FROM detail_transaksi
            ");

            // Hapus transaksi
            $this->db->query("
                DELETE FROM transaksi
            ");

            // Hapus semua produk
            $this->db->query("
                DELETE FROM produk
            ");

            // Reset AUTO_INCREMENT
            $this->db->query("
                ALTER TABLE produk AUTO_INCREMENT = 1
            ");

            $this->db->query("
                ALTER TABLE transaksi AUTO_INCREMENT = 1
            ");

            $this->db->query("
                ALTER TABLE detail_transaksi AUTO_INCREMENT = 1
            ");

            // Kosongkan cart
            $_SESSION['cart'] = [];

            setMessage("🗑️ Semua data berhasil dikosongkan!");
        }

        // =========================
        // TAMBAH KE CART
        // =========================
        if (isset($_POST['add_to_cart'])) {

            $id = isset($_POST['produk_id'])
                ? (int) $_POST['produk_id']
                : 0;

            $qty = isset($_POST['qty'])
                ? (int) $_POST['qty']
                : 1;

            // VALIDASI PRODUK
            if ($id <= 0) {

    echo "
    <script>
        alert('Oops! Barang tidak ada.');
        window.history.back();
    </script>
    ";

    exit;
}

            // VALIDASI QTY
            if ($qty <= 0) {

                setMessage("❌ Jumlah tidak valid.");

                return;
            }

            // AMBIL PRODUK
            $p = $this->produkModel->getById($id);

            // VALIDASI PRODUK ADA
            if (!$p) {

    echo "
    <script>
        alert('Oops! Barang tidak ada di database.');
        window.history.back();
    </script>
    ";

    exit;
}

            // VALIDASI STOK
            if ($p['stok'] < $qty) {

                setMessage("❌ Stok tidak cukup. Sisa stok: " . $p['stok']);

                return;
            }

            // TAMBAH KE CART
            if (isset($_SESSION['cart'][$id])) {

                $_SESSION['cart'][$id] += $qty;

            } else {

                $_SESSION['cart'][$id] = $qty;
            }

            setMessage("✅ Produk berhasil ditambahkan ke keranjang.");
        }

        // =========================
        // CHECKOUT
        // =========================
        if (isset($_POST['checkout'])) {

            $this->processCheckout();
        }

        // =========================
        // RESET CART
        // =========================
        if (isset($_POST['reset_cart'])) {

            $_SESSION['cart'] = [];

            setMessage("🗑️ Keranjang berhasil dikosongkan.");
        }
    }

    // =========================
    // PROCESS CHECKOUT
    // =========================
    private function processCheckout() {

        // VALIDASI CART
        if (empty($_SESSION['cart'])) {

            setMessage("❌ Keranjang masih kosong.");

            return;
        }

        $total = 0;

        // HITUNG TOTAL
        foreach ($_SESSION['cart'] as $id => $qty) {

            $p = $this->produkModel->getById($id);

            if ($p) {

                $total += ($p['harga'] * $qty);
            }
        }

        // SIMPAN TRANSAKSI
        $stmtTrx = $this->db->prepare("INSERT INTO transaksi (total_bayar) VALUES (?)");
        $stmtTrx->bind_param("i", $total);
        $stmtTrx->execute();

        $t_id = $this->db->insert_id;

        // DETAIL TRANSAKSI
        $stmtDetail = $this->db->prepare(
            "INSERT INTO detail_transaksi (transaksi_id, produk_id, jumlah, subtotal)
             VALUES (?, ?, ?, ?)"
        );

        foreach ($_SESSION['cart'] as $id => $qty) {

            $p = $this->produkModel->getById($id);

            if ($p) {

                $sub = $p['harga'] * $qty;

                $stmtDetail->bind_param("iiii", $t_id, $id, $qty, $sub);
                $stmtDetail->execute();

                // UPDATE STOK
                $this->produkModel->updateStok($id, $qty);
            }
        }

        // KOSONGKAN CART
        $_SESSION['cart'] = [];

        setMessage(
            "✅ Checkout berhasil! Total bayar: "
            . formatRupiah($total)
        );
    }
}

?>