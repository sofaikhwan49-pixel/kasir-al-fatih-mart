<div class="min-h-screen flex items-center justify-center bg-slate-50 px-4">
    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center font-black text-white text-2xl mx-auto mb-4 shadow-lg shadow-indigo-200">
                K
            </div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">KASIR AL-FATIH MART</h1>
            <p class="text-slate-400 font-medium text-sm mt-1">Silakan login untuk melanjutkan</p>
        </div>

        <?php if ($msg): ?>
            <div class="mb-6 p-4 bg-indigo-600 text-white rounded-xl shadow-lg text-center font-bold text-sm">
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
            <form method="POST" class="space-y-5">

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Username</label>
                    <input
                        type="text"
                        name="username"
                        required
                        autofocus
                        autocomplete="username"
                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="Masukkan username">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Password</label>
                    <input
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="Masukkan password">
                </div>

                <button
                    type="submit"
                    name="login"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3.5 rounded-xl font-black shadow-lg shadow-indigo-100 transition-all active:scale-95">
                    MASUK
                </button>

            </form>
        </div>

        <p class="text-center text-slate-300 text-xs mt-6 font-medium">
            &copy; <?= date('Y') ?> Kasir Al-Fatih Mart
        </p>

    </div>
</div>
