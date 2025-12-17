<?php
require_once '../../config/koneksi.php';

$error = '';
$success = false;

// 1. Ambil daftar kategori dari database untuk ditampilkan di dropdown
$list_kategori = mysqli_query($koneksi, "SELECT id_kategori, nama_kategori FROM kategori_barang ORDER BY nama_kategori ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama        = trim(mysqli_real_escape_string($koneksi, $_POST['nama'] ?? ''));
    $id_kategori = (int)($_POST['id_kategori'] ?? 0);
    $harga       = (int)($_POST['harga'] ?? 0);
    $stok        = (int)($_POST['stok'] ?? 0);
    $satuan      = trim(mysqli_real_escape_string($koneksi, $_POST['satuan'] ?? 'pcs'));
    $gambar      = trim(mysqli_real_escape_string($koneksi, $_POST['gambar'] ?? ''));

    if ($nama !== '' && $id_kategori > 0 && $harga > 0) {
        // Mulai Transaksi SQL agar data tersimpan di kedua tabel atau tidak sama sekali
        mysqli_begin_transaction($koneksi);

        try {
            // 2. Insert ke tabel barang
            $sql_barang = "INSERT INTO barang (id_kategori, nama_barang, harga, stok, satuan, gambar) 
                           VALUES ('$id_kategori', '$nama', '$harga', '$stok', '$satuan', '$gambar')";
            
            if (!mysqli_query($koneksi, $sql_barang)) {
                throw new Exception("Gagal simpan data barang");
            }

            $id_barang_baru = mysqli_insert_id($koneksi);

            // 3. Insert ke tabel stok_barang sebagai riwayat stok awal
            $sql_stok = "INSERT INTO stok_barang (id_barang, jumlah, kondisi, keterangan) 
                         VALUES ('$id_barang_baru', '$stok', 'baik', 'Input produk baru')";
            
            if (!mysqli_query($koneksi, $sql_stok)) {
                throw new Exception("Gagal simpan riwayat stok");
            }

            // Jika semua berhasil
            mysqli_commit($koneksi);
            header('Location: ../../views/admin/produk.php?success=1');
            exit;

        } catch (Exception $e) {
            mysqli_rollback($koneksi);
            $error = "Terjadi kesalahan: " . $e->getMessage();
        }
    } else {
        $error = 'Nama produk, kategori, dan harga wajib diisi.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Poppins', 'sans-serif'] },
                    colors: { 'primary': '#FACC15', 'bg-soft': '#F0FDF4' }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto bg-bg-soft min-h-screen relative shadow-2xl overflow-hidden flex flex-col">
        <div class="bg-primary px-6 pt-8 pb-10 rounded-b-[2.5rem] shadow-sm z-10">
            <div class="flex items-center justify-between">
                <a href="../../views/admin/produk.php" class="bg-white/20 p-2 rounded-xl hover:bg-white/40 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-xl font-bold text-gray-900">Tambah Produk</h1>
                <div class="w-10"></div>
            </div>
            <p class="mt-2 text-sm text-gray-700">Masukkan detail produk baru ke database.</p>
        </div>

        <div class="flex-1 px-6 pt-6 pb-12 space-y-6 overflow-y-auto">
            <?php if ($error): ?>
                <div class="bg-red-100 text-red-700 px-4 py-3 rounded-2xl text-sm italic"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Nama Produk</label>
                    <input type="text" name="nama" required class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" placeholder="Contoh: Aqua 600ml">
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Kategori</label>
                    <select name="id_kategori" required class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                        <option value="">Pilih Kategori</option>
                        <?php while($kat = mysqli_fetch_assoc($list_kategori)): ?>
                            <option value="<?= $kat['id_kategori'] ?>"><?= $kat['nama_kategori'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Harga (Rp)</label>
                        <input type="number" name="harga" required class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" placeholder="5000">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Satuan</label>
                        <input type="text" name="satuan" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" placeholder="Botol/Pcs/Gelas" value="Pcs">
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Stok Awal</label>
                    <input type="number" name="stok" required class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" placeholder="Jumlah stok">
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">URL Gambar</label>
                    <input type="url" name="gambar" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" placeholder="https://...">
                </div>

                <button type="submit" class="w-full bg-gray-900 text-white py-4 rounded-2xl shadow-lg hover:bg-gray-800 transition font-bold mt-4">Simpan Produk</button>
            </form>
        </div>
    </div>
</body>
</html>