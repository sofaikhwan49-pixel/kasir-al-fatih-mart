<?php
// Memastikan session dimulai paling atas agar keranjang belanja berfungsi
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Memanggil file koneksi dan fungsi pembantu
require_once 'config/Database.php';
require_once 'config/HelperFunction.php';
require_once 'controller/AuthController.php';
require_once 'controller/KasirController.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // =========================
    // AUTENTIKASI (SESSION PROTECTION)
    // =========================
    $auth = new AuthController($db);
    $auth->handleLogout();
    $auth->handleLogin();

    if (!$auth->isLoggedIn()) {

        // Belum login -> tampilkan HALAMAN LOGIN SAJA, tidak render menu/konten/footer
        $msg = getMessage();
        ?>
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Login - Sistem Kasir AL-Fatih Mart</title>
            <script src="https://cdn.tailwindcss.com"></script>
            <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
            <style>body{font-family:'Plus Jakarta Sans', sans-serif;}</style>
        </head>
        <body class="bg-slate-50">
            <?php include 'view/login.php'; ?>
        </body>
        </html>
        <?php
        $db->close();
        exit;
    }

    // Inisialisasi controller (hanya dijalankan jika sudah login)
    $controller = new KasirController($db);
    $controller->handleRequest();

} catch (Exception $e) {
    die("Gagal memuat database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Kasir AL-Fatih Mart - MVC</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        body{
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Styling Select2 */
        .select2-container{
            width:100% !important;
        }

        .select2-container .select2-selection--single{
            height:48px !important;
            border-radius:12px !important;
            border:1px solid #e2e8f0 !important;
            background:#f8fafc !important;
            padding-left:10px !important;
        }

        .select2-container--default 
        .select2-selection--single 
        .select2-selection__rendered{
            line-height:48px !important;
            color:#334155 !important;
        }

        .select2-container--default 
        .select2-selection--single 
        .select2-selection__arrow{
            height:48px !important;
            right:10px !important;
        }

        .select2-dropdown{
            border-radius:12px !important;
            border:1px solid #e2e8f0 !important;
            overflow:hidden;
        }

        .select2-search__field{
            padding:10px !important;
            border-radius:8px !important;
            border:1px solid #cbd5e1 !important;
        }

        .select2-results__option{
            padding:10px !important;
        }

        .select2-results__option--highlighted{
            background:#4f46e5 !important;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 flex flex-col min-h-screen">

    <!-- MENU -->
    <?php 
    if(file_exists('menu.php')) {
        include 'menu.php'; 
    }
    ?>

    <!-- CONTENT -->
    <div class="flex-grow">

        <?php 
        if(file_exists('content.php')) {
            include 'content.php'; 
        } else {
            echo "
            <div class='p-10 text-center text-red-500 font-bold'>
                Error: File content.php tidak ditemukan!
            </div>";
        }
        ?>

    </div>

    <!-- FOOTER -->
    <footer class="mt-auto py-10 bg-white text-center text-slate-400 border-t border-slate-100">
        <p class="text-sm font-bold">
            &copy; <?= date('Y') ?> Kasir Al-Fatih Mart - Belanja Aman Harga Pas.
        </p>
    </footer>

    <!-- AKTIFKAN SELECT2 -->
    <script>
        $(document).ready(function() {

            $('#pilihProduk').select2({
                placeholder: "Cari nama barang...",
                allowClear: true,
                width: '100%'
            });

        });
    </script>

</body>
</html>

<?php
if(isset($db)) {
    $db->close();
}
?>
<?