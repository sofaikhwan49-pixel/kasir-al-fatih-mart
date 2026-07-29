<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'beranda';
$msg = getMessage();

// =========================
// PROTEKSI HAK AKSES (ROLE)
// Halaman inventori & riwayat hanya untuk admin
// =========================
$halaman_admin_only = ['inventori', 'riwayat'];

if (in_array($page, $halaman_admin_only) && (!isset($auth) || !$auth->isAdmin())) {
    $page = 'beranda';
    setMessage("⛔ Anda tidak memiliki akses ke halaman tersebut.");
    $msg = getMessage();
}

// Menyiapkan data produk untuk semua halaman yang membutuhkan
$produk_list = null;
$produk_count = 0;

if (isset($db)) {
    $produkModel = new ProdukModel($db);
    $produk_query = $produkModel->getAll();
    if ($produk_query) {
        $produk_list = $produk_query;
        $produk_count = $produk_query->num_rows;
    }
}
?>
<?php if(isset($controller) && $controller->maintenance): ?>
    <div class="bg-red-500 text-white p-4 rounded-xl mb-6 text-center font-bold shadow-lg">
    ⚠️ Sistem Sedang Dalam Perawatan
</div>

<?php endif; ?>
<main class="container mx-auto p-4 md:p-8 min-h-screen">
    <?php if($msg): ?>
    <div class="mb-6 p-4 bg-indigo-600 text-white rounded-xl shadow-lg flex items-center justify-between animate-bounce">
        <div class="flex items-center gap-3">
            <span class="font-bold"><?= $msg ?></span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-2xl font-bold">&times;</button>
    </div>
    <?php endif; ?>

    <?php 
    switch ($page) {
        case 'inventori':
            include 'view/inventori.php';
            break;
        case 'riwayat':
            include 'view/riwayat.php';
            break;
        default:
            include 'view/beranda.php';
            break;
    }
    ?>
</main>