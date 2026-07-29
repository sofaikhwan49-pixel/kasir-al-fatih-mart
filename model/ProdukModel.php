<?php
class ProdukModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        return $this->db->query("SELECT * FROM produk ORDER BY nama_produk ASC");
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM produk WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function add($nama, $harga, $stok) {
        $stmt = $this->db->prepare("INSERT INTO produk (nama_produk, harga, stok) VALUES (?, ?, ?)");
        $stmt->bind_param("sdi", $nama, $harga, $stok);
        return $stmt->execute();
    }

    public function seedData() {
        $this->db->query("TRUNCATE TABLE produk");
        $kategori = ['Sembako', 'Elektronik', 'Pakaian', 'Alat Tulis', 'Otomotif'];
        $items = ['Premium', 'Edisi Spesial', 'Original', 'Varian Terbaru', 'Tipe Terbaru'];

        for ($i = 1; $i <= 100; $i++) {
            $nama = $kategori[array_rand($kategori)] . " " . $items[array_rand($items)] . " " . chr(rand(65, 90)) . rand(1, 99);
            $harga = rand(10, 500) * 1000;
            $stok = rand(20, 200);
            $this->add($nama, $harga, $stok);
        }
        return true;
    }

    public function updateStok($id, $qty) {
        $stmt = $this->db->prepare("UPDATE produk SET stok = stok - ? WHERE id = ?");
        $stmt->bind_param("ii", $qty, $id);
        return $stmt->execute();
    }
}
?>