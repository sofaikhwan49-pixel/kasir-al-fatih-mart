<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <div class="lg:col-span-2 space-y-6">

        <!-- INPUT PENJUALAN -->
        <section class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">

            <h2 class="text-xl font-bold mb-6 flex items-center">
                <span class="w-2 h-6 bg-indigo-600 rounded-full mr-3"></span>
                Input Penjualan
            </h2>

            <?php if ($produk_count == 0): ?>

                <div class="bg-amber-50 border border-amber-200 p-8 rounded-xl text-center">

                    <div class="text-5xl mb-4">📦</div>

                    <p class="text-amber-700 font-bold mb-4">
                        Database Barang Masih Kosong!
                    </p>

                    <form
                        method="POST"
                        onsubmit="return confirm('Yakin ingin mengosongkan semua data?')"
                    >

                        <button
                            type="submit"
                            name="generate_data"
                            class="bg-amber-500 hover:bg-amber-600 text-white px-8 py-3 rounded-xl font-bold shadow-lg transition-all transform hover:scale-105">

                            KOSONGKAN SEMUA DATA

                        </button>

                    </form>

                </div>

            <?php else: ?>

                <!-- FORM TAMBAH KE CART -->
                <form
                    method="POST"
                    id="formTambahProduk"
                    class="flex flex-col md:flex-row gap-4"
                >

                    <!-- PILIH PRODUK -->
                    <div class="flex-1">

                        <label class="block text-xs font-bold text-slate-400 mb-2 uppercase">
                            Pilih Produk
                        </label>

                        <select
                            id="pilihProduk"
                            name="produk_id"
                            class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500"
                            required
                        >

                            <option value="">
                                -- Pilih Barang --
                            </option>

                            <?php

                            /** @var mysqli_result $produk_list */

                            if ($produk_list) {

                                $produk_list->data_seek(0);

                                while ($row = $produk_list->fetch_assoc()):

                            ?>

                                    <option
                                        value="<?= isset($row['id']) ? $row['id'] : 0 ?>"
                                    >

                                        <?= isset($row['nama_produk']) ? $row['nama_produk'] : 'Produk Tidak Diketahui' ?>

                                        -

                                        <?= formatRupiah(isset($row['harga']) ? $row['harga'] : 0) ?>

                                        (Stok:
                                        <?= isset($row['stok']) ? $row['stok'] : 0 ?>)

                                    </option>

                            <?php

                                endwhile;
                            }

                            ?>

                        </select>

                    </div>

                    <!-- JUMLAH -->
                    <div class="w-full md:w-32">

                        <label class="block text-xs font-bold text-slate-400 mb-2 uppercase">
                            Jumlah
                        </label>

                        <input
                            type="number"
                            name="qty"
                            value="1"
                            min="1"
                            required
                            class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-center focus:ring-2 focus:ring-indigo-500"
                        >

                    </div>

                    <!-- BUTTON -->
                    <div class="flex items-end">

                        <button
                            type="submit"
                            name="add_to_cart"
                            class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl font-bold transition-all shadow-lg active:scale-95"
                        >

                            Tambah

                        </button>

                    </div>

                </form>

            <?php endif; ?>

        </section>

        <!-- KERANJANG -->
        <section class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">

            <h2 class="text-xl font-bold mb-4 flex items-center">

                <span class="w-2 h-6 bg-emerald-500 rounded-full mr-3"></span>

                Keranjang Belanja

                <span class="ml-2 text-xs bg-slate-100 text-slate-500 px-2 py-1 rounded-md font-normal">

                    (<?= !empty($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?> Jenis Barang)

                </span>

            </h2>

            <div class="overflow-x-auto">

                <table class="w-full text-left">

                    <thead class="text-slate-400 text-sm uppercase tracking-wider border-b border-slate-100">

                        <tr>
                            <th class="py-4">Produk</th>
                            <th class="text-right py-4">Harga</th>
                            <th class="text-center py-4">Qty</th>
                            <th class="text-right py-4">Subtotal</th>
                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-50">

                        <?php

                        $total_belanja = 0;

                        if (!empty($_SESSION['cart']) && count($_SESSION['cart']) > 0):

                            foreach ($_SESSION['cart'] as $id => $qty):

                                $p = $produkModel->getById($id);

                                if ($p):

                                    $harga = isset($p['harga']) ? $p['harga'] : 0;

                                    $sub = $harga * $qty;

                                    $total_belanja += $sub;

                        ?>

                                    <tr class="hover:bg-slate-50 transition">

                                        <td class="py-4">

                                            <div class="font-semibold text-slate-700">

                                                <?= isset($p['nama_produk']) ? $p['nama_produk'] : '-' ?>

                                            </div>

                                            <div class="text-[10px] text-slate-400 uppercase tracking-tighter">

                                                ID: #<?= isset($p['id']) ? $p['id'] : 0 ?>

                                            </div>

                                        </td>

                                        <td class="py-4 text-right text-slate-500">

                                            <?= formatRupiah($harga) ?>

                                        </td>

                                        <td class="py-4 text-center">

                                            <span class="bg-slate-100 px-3 py-1 rounded-full text-sm font-bold text-slate-600">

                                                <?= $qty ?>

                                            </span>

                                        </td>

                                        <td class="py-4 text-right font-bold text-indigo-600">

                                            <?= formatRupiah($sub) ?>

                                        </td>

                                    </tr>

                        <?php

                                endif;

                            endforeach;

                        else:

                        ?>

                            <tr>

                                <td colspan="4" class="py-12 text-center text-slate-300 italic">

                                    Belum ada barang di keranjang

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

            <!-- TOTAL -->
            <?php if ($total_belanja > 0): ?>

                <div class="mt-8 pt-6 border-t-2 border-dashed border-slate-100">

                    <div class="flex justify-between items-center mb-6">

                        <span class="text-slate-400 font-bold uppercase tracking-widest text-sm">

                            Total yang harus dibayar:

                        </span>

                        <span class="text-3xl font-black text-slate-900">

                            <?= formatRupiah($total_belanja) ?>

                        </span>

                    </div>

                    <div class="flex flex-col md:flex-row gap-3">

                        <!-- CHECKOUT -->
                        <form method="POST" class="flex-1">

                            <button
                                type="submit"
                                name="checkout"
                                class="w-full bg-emerald-500 hover:bg-emerald-600 text-white py-4 rounded-xl font-black text-lg shadow-xl shadow-emerald-100 transition-all transform hover:-translate-y-1"
                            >

                                BAYAR SEKARANG

                            </button>

                        </form>

                        <!-- RESET CART -->
                        <form method="POST">

                            <button
                                type="submit"
                                name="reset_cart"
                                onclick="return confirm('Yakin ingin mengosongkan keranjang?')"
                                class="w-full md:w-auto bg-slate-100 hover:bg-red-50 hover:text-red-600 text-slate-400 px-6 py-4 rounded-xl font-bold transition-all"
                            >

                                Kosongkan

                            </button>

                        </form>

                    </div>

                </div>

            <?php endif; ?>

        </section>

    </div>

    <!-- SIDEBAR -->
    <aside class="space-y-6">

        <section class="bg-indigo-900 text-white p-6 rounded-2xl shadow-xl">

            <h3 class="font-bold text-lg mb-2">
                Info Kasir
            </h3>

            <p class="text-indigo-300 text-sm leading-relaxed mb-4">
                Pastikan stok barang mencukupi sebelum melakukan transaksi.
            </p>

            <div class="flex justify-between text-xs font-mono bg-indigo-800/50 p-3 rounded-lg">

                <span>Status Server:</span>

                <span class="text-emerald-400 font-bold">
                    ONLINE
                </span>

            </div>

        </section>

    </aside>

</div>

<!-- VALIDASI JAVASCRIPT -->
<script>

document.addEventListener("DOMContentLoaded", function(){

    const form = document.getElementById("formTambahProduk");

    const produk = document.getElementById("pilihProduk");

    form.addEventListener("submit", function(e){

        // VALIDASI PRODUK
        if(produk.value === ""){

            e.preventDefault();

            alert("Oops! Barang tidak ada.");

            return;
        }

    });

});

</script>