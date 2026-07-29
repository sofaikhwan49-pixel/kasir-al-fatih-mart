<?php $user = isset($auth) ? $auth->currentUser() : null; ?>
<nav class="bg-slate-900 text-white shadow-xl p-4 sticky top-0 z-50">
    <div class="container mx-auto flex justify-between items-center flex-wrap gap-3">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center font-bold">K</div>
            <h1 class="text-xl font-bold tracking-tighter">KASIR<span class="text-indigo-400"> AL-FATIH MART </span></h1>
        </div>

        <div class="flex items-center gap-1">
            <a href="index.php" class="px-4 py-2 rounded-lg transition <?= !isset($_GET['page']) ? 'bg-indigo-600' : 'hover:bg-slate-800' ?>">Beranda</a>

            <?php if ($user && $user['role'] === 'admin'): ?>
                <a href="index.php?page=inventori" class="px-4 py-2 rounded-lg transition <?= isset($_GET['page']) && $_GET['page'] == 'inventori' ? 'bg-indigo-600' : 'hover:bg-slate-800' ?>">Inventori</a>
                <a href="index.php?page=riwayat" class="px-4 py-2 rounded-lg transition <?= isset($_GET['page']) && $_GET['page'] == 'riwayat' ? 'bg-indigo-600' : 'hover:bg-slate-800' ?>">Riwayat</a>
            <?php endif; ?>
        </div>

        <?php if ($user): ?>
            <div class="flex items-center gap-3">
                <div class="text-right leading-tight">
                    <div class="text-[10px] uppercase tracking-wider text-indigo-400 font-bold"><?= htmlspecialchars($user['role']) ?></div>
                </div>
                <a
                    href="index.php?logout=1"
                    onclick="return confirm('Yakin ingin logout?')"
                    class="bg-slate-800 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-bold transition">
                    Logout
                </a>
            </div>
        <?php endif; ?>
    </div>
</nav>