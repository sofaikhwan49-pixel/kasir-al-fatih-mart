<?php

if (!isset($db)) {
    die("Database tidak ditemukan.");
}

?>

<div class="space-y-6">

    <h2 class="text-2xl font-black text-slate-800">
        Riwayat Penjualan
    </h2>

    <?php

    // HAPUS TRANSAKSI
    if (isset($_POST['hapus_riwayat'])) {

        $hapus_id = (int) $_POST['hapus_id'];

        $stmtDelDetail = $db->prepare("DELETE FROM detail_transaksi WHERE transaksi_id = ?");
        $stmtDelDetail->bind_param("i", $hapus_id);
        $stmtDelDetail->execute();

        $stmtDelTrx = $db->prepare("DELETE FROM transaksi WHERE id = ?");
        $stmtDelTrx->bind_param("i", $hapus_id);
        $stmtDelTrx->execute();

        echo "
        <div class='p-4 bg-emerald-100 text-emerald-700 rounded-xl font-bold'>
            Riwayat berhasil dihapus
        </div>
        ";
    }

    ?>

    <section class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

        <table class="w-full text-left">

            <thead class="bg-indigo-600 text-white text-xs uppercase">

                <tr>

                    <th class="p-6">
                        Transaksi
                    </th>

                    <th class="p-6">
                        Waktu
                    </th>

                    <th class="p-6 text-right">
                        Total
                    </th>

                    <th class="p-6 text-center">
                        Aksi
                    </th>

                </tr>

            </thead>

            <tbody class="divide-y divide-slate-100">

                <?php

                $riwayat = $db->query("
                    SELECT *
                    FROM transaksi
                    ORDER BY id DESC
                ");

                if ($riwayat && $riwayat->num_rows > 0):

                    while ($tr = $riwayat->fetch_assoc()):

                        $t_id = (int)$tr['id'];

                        $stmtDetail = $db->prepare("
                            SELECT
                                dt.*,
                                p.nama_produk
                            FROM detail_transaksi dt
                            LEFT JOIN produk p
                            ON dt.produk_id = p.id
                            WHERE dt.transaksi_id = ?
                        ");
                        $stmtDetail->bind_param("i", $t_id);
                        $stmtDetail->execute();
                        $detail = $stmtDetail->get_result();

                ?>

                        <tr class="hover:bg-slate-50">

                            <!-- DETAIL -->
                            <td class="p-6">

                                <div class="font-black text-indigo-700 mb-3">
                                    Transaksi #<?= $t_id ?>
                                </div>

                                <div class="space-y-2">

                                    <?php if ($detail && $detail->num_rows > 0): ?>

                                        <?php while ($d = $detail->fetch_assoc()): ?>

                                            <div class="flex justify-between text-sm">

                                                <span>
                                                    <?= $d['jumlah'] ?>x
                                                    <?= htmlspecialchars($d['nama_produk']) ?>
                                                </span>

                                                <span class="font-bold">
                                                    <?= formatRupiah($d['subtotal']) ?>
                                                </span>

                                            </div>

                                        <?php endwhile; ?>

                                    <?php else: ?>

                                        <div class="text-slate-400 italic">
                                            Tidak ada detail
                                        </div>

                                    <?php endif; ?>

                                </div>

                            </td>

                            <!-- TANGGAL -->
                            <td class="p-6">

                                <?= isset($tr['tanggal'])
                                    ? date('d M Y H:i', strtotime($tr['tanggal']))
                                    : '-' ?>

                            </td>

                            <!-- TOTAL -->
                            <td class="p-6 text-right font-black text-indigo-600">

                                <?= formatRupiah($tr['total_bayar']) ?>

                            </td>

                            <!-- HAPUS -->
                            <td class="p-6 text-center">

                                <form
                                    method="POST"
                                    onsubmit="return confirm('Yakin hapus transaksi?')">

                                    <input
                                        type="hidden"
                                        name="hapus_id"
                                        value="<?= $t_id ?>">

                                    <button
                                        type="submit"
                                        name="hapus_riwayat"
                                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl text-xs font-bold">

                                        Hapus

                                    </button>

                                </form>

                            </td>

                        </tr>

                <?php

                    endwhile;

                else:

                ?>

                    <tr>

                        <td colspan="4" class="p-10 text-center text-slate-400">

                            Belum ada riwayat transaksi

                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </section>

</div>