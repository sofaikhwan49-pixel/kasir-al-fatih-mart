<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php

// =========================
// VALIDASI DATABASE
// =========================

if (!isset($db)) {
    die("Koneksi database tidak ditemukan.");
}

// =========================
// HAPUS PRODUK
// =========================

if (isset($_POST['hapus_produk'])) {

    $hapus_id = isset($_POST['hapus_id'])
        ? (int) $_POST['hapus_id']
        : 0;

    if ($hapus_id > 0) {

        // Hapus produk
        $stmtHapus = $db->prepare("DELETE FROM produk WHERE id = ?");
        $stmtHapus->bind_param("i", $hapus_id);
        $hapus = $stmtHapus->execute();

        if ($hapus) {

            echo "
            <div class='mb-4 p-4 bg-emerald-100 text-emerald-700 rounded-xl font-bold'>
                Produk berhasil dihapus.
            </div>
            ";

        } else {

            echo "
            <div class='mb-4 p-4 bg-red-100 text-red-700 rounded-xl font-bold'>
                Produk gagal dihapus.
            </div>
            ";
        }
    }
}

?>

<div class="space-y-6">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">

        <div>

            <h2 class="text-3xl font-black text-slate-800 tracking-tight">
                Manajemen Inventori
            </h2>

            <p class="text-slate-400 font-medium">
                Kelola stok barang dan harga produk Anda secara real-time.
            </p>

        </div>

        <!-- RESET DATA -->
        <form
            method="POST"
            onsubmit="return confirm('Yakin ingin reset semua produk?')">

            <button
                type="submit"
                name="generate_data"
                class="bg-amber-400 hover:bg-amber-500 text-amber-950 px-6 py-3 rounded-xl text-sm font-black shadow-lg transition">

                RESET & ISI 100 BARANG

            </button>

        </form>

    </div>

    <!-- GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <!-- FORM -->
        <section class="lg:col-span-1 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">

            <div class="flex items-center gap-2 mb-6">

                <div class="w-2 h-6 bg-indigo-600 rounded-full"></div>

                <h3 class="font-black text-lg">
                    Tambah Barang
                </h3>

            </div>

            <form method="POST" class="space-y-4">

                <!-- NAMA -->
                <div>

                    <label class="text-xs font-bold text-slate-500">
                        Nama Produk
                    </label>

                    <input
                        type="text"
                        name="nama_produk"
                        required
                        class="w-full p-3 rounded-xl border border-slate-200">

                </div>

                <!-- HARGA -->
                <div>

                    <label class="text-xs font-bold text-slate-500">
                        Harga
                    </label>

                    <input
                        type="number"
                        name="harga"
                        required
                        class="w-full p-3 rounded-xl border border-slate-200">

                </div>

                <!-- STOK -->
                <div>

                    <label class="text-xs font-bold text-slate-500">
                        Stok
                    </label>

                    <input
                        type="number"
                        name="stok"
                        required
                        class="w-full p-3 rounded-xl border border-slate-200">

                </div>

                <!-- BUTTON -->
                <button
                    type="submit"
                    name="add_product"
                    class="w-full bg-slate-900 text-white py-4 rounded-xl font-black">

                    SIMPAN BARANG

                </button>

            </form>

        </section>

        <!-- TABEL -->
        <section class="lg:col-span-3 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

            <div class="overflow-x-auto">

                <table class="w-full text-left">

                    <thead class="bg-slate-50 text-slate-400 text-xs uppercase">

                        <tr>

                            <th class="p-6">
                                Produk
                            </th>

                            <th class="p-6 text-right">
                                Harga
                            </th>

                            <th class="p-6 text-center">
                                Stok
                            </th>

                            <th class="p-6 text-center">
                                Aksi
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        <?php if ($produk_list && $produk_list->num_rows > 0): ?>

                            <?php while ($row = $produk_list->fetch_assoc()): ?>

                                <tr class="hover:bg-slate-50 transition">

                                    <!-- PRODUK -->
                                    <td class="p-6">

                                        <div class="font-bold text-slate-800">

                                            <?= htmlspecialchars($row['nama_produk']) ?>

                                        </div>

                                        <div class="text-xs text-slate-400">

                                            ID: #<?= $row['id'] ?>

                                        </div>

                                    </td>

                                    <!-- HARGA -->
                                    <td class="p-6 text-right font-black text-indigo-600">

                                        <?= formatRupiah($row['harga']) ?>

                                    </td>

                                    <!-- STOK -->
                                    <td class="p-6 text-center">

                                        <span class="bg-slate-100 px-4 py-1 rounded-full text-xs font-bold">

                                            <?= $row['stok'] ?>

                                        </span>

                                    </td>

                                    <!-- HAPUS -->
                                    <td class="p-6 text-center">

                                        <form
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus produk ini?')">

                                            <input
                                                type="hidden"
                                                name="hapus_id"
                                                value="<?= $row['id'] ?>">

                                            <button
                                                type="submit"
                                                name="hapus_produk"
                                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl text-xs font-bold">

                                                Hapus

                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>

                                <td colspan="4" class="p-20 text-center text-slate-400">

                                    Belum ada data barang.

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>

    </div>

</div>