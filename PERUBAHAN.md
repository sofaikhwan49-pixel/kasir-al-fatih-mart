# Perubahan Tahap 1.1 — Keamanan & Login/Logout

## 1. Perbaikan Keamanan (SQL Injection)
Semua query yang sebelumnya menyisipkan variabel langsung ke dalam string SQL
(`WHERE id = $id`, dst) sudah diganti menggunakan **prepared statement**
(`bind_param`). Ini menutup celah SQL Injection di:
- `model/ProdukModel.php` (getById, updateStok)
- `controller/KasirController.php` (proses checkout)
- `view/riwayat.php` (hapus riwayat, ambil detail transaksi)
- `view/inventori.php` (hapus produk)

## 2. Login, Logout, Session Protection
File baru:
- `model/UserModel.php` — cek login (password di-hash pakai bcrypt via `password_hash`/`password_verify`)
- `controller/AuthController.php` — proses login, logout, cek status login, cek role admin, proteksi brute-force sederhana (max 5x gagal → lock 60 detik)
- `view/login.php` — halaman form login

File yang diubah:
- `index.php` — sebelum menampilkan apa pun, sistem cek login dulu. Kalau belum login, **hanya halaman login** yang ditampilkan (menu/konten/footer tidak dirender).
- `menu.php` — menampilkan nama & role user yang login + tombol Logout. Menu "Inventori" & "Riwayat" hanya muncul untuk role **admin**.
- `content.php` — proteksi di level server: kalau kasir mencoba akses `?page=inventori` atau `?page=riwayat` langsung lewat URL, otomatis dialihkan ke Beranda + pesan "akses ditolak".
- `controller/KasirController.php` — aksi "Tambah Produk" dan "Kosongkan Semua Data" sekarang dicek ulang di server, hanya bisa dijalankan oleh admin (jaga-jaga kalau ada yang coba kirim request langsung tanpa lewat tampilan).
- `config/Database.php` — menambahkan tabel `users` (username, password terenkripsi, nama, role, status aktif) dan otomatis membuat 2 akun default saat pertama kali dijalankan (hanya jika tabel users masih kosong).

## Akun Default (WAJIB DIGANTI setelah instalasi pertama)
| Username | Password  | Role  |
|----------|-----------|-------|
| admin    | admin123  | admin |
| kasir    | kasir123  | kasir |

> Fitur ganti password/kelola user belum ada di tahap ini — itu masuk ke
> pekerjaan berikutnya (Manajemen Produk/Pengaturan). Untuk sementara ganti
> password langsung lewat database jika perlu:
> `UPDATE users SET password = '<hash_baru>' WHERE username = 'admin';`
> (gunakan `password_hash('password_baru', PASSWORD_DEFAULT)` di PHP untuk generate hash-nya)

## Yang Kasir BISA lakukan
- Login/logout
- Beranda: cari produk, tambah ke keranjang, checkout

## Yang Kasir TIDAK BISA lakukan (khusus admin)
- Buka halaman Inventori (tambah/hapus produk)
- Buka halaman Riwayat (lihat/hapus riwayat transaksi)
- Tambah produk / kosongkan semua data lewat request langsung

## Belum dikerjakan (menyusul di tahap berikutnya)
- Dashboard (total produk, penjualan hari ini, grafik, dll)
- Kategori, upload gambar, barcode, harga modal
- Diskon, pajak di kasir
- Laporan harian/bulanan/tahunan + export PDF/Excel
- Halaman Pengaturan (nama toko, logo, dll)
- CSRF token pada form (disarankan ditambahkan sebelum go-live produksi)
