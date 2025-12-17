<?php
require_once '../../config/koneksi.php';

$id = (int) ($_GET['id'] ?? 0);
$error = '';

if ($id <= 0) {
    header('Location: ../../views/admin/produk.php');
    exit;
}

// 1. Ambil data produk dengan JOIN ke kategori untuk preview yang informatif
$sql_select = "SELECT b.*, k.nama_kategori 
               FROM barang b 
               JOIN kategori_barang k ON b.id_kategori = k.id_kategori 
               WHERE b.id_barang = $id LIMIT 1";
$res = mysqli_query($koneksi, $sql_select);
$selected = ($res && mysqli_num_rows($res)) ? mysqli_fetch_assoc($res) : null;

// Jika produk tidak ditemukan di database
if (!$selected) {
    header('Location: ../../views/admin/produk.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mulai Transaksi SQL
    mysqli_begin_transaction($koneksi);

    try {
        // 2. Hapus dulu riwayat stok di tabel stok_barang (karena ada Foreign Key)
        $delete_stok = "DELETE FROM stok_barang WHERE id_barang = $id";
        mysqli_query($koneksi, $delete_stok);

        // 3. Hapus produk dari tabel barang
        $delete_barang = "DELETE FROM barang WHERE id_barang = $id";
        
        if (mysqli_query($koneksi, $delete_barang)) {
            mysqli_commit($koneksi);
            header('Location: ../../views/admin/produk.php?deleted=1');
            exit;
        } else {
            throw new Exception(mysqli_error($koneksi));
        }
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        // Cek jika error disebabkan karena produk sudah pernah terjual (ada di detail_transaksi)
        if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
            $error = 'Produk tidak bisa dihapus karena sudah memiliki riwayat transaksi/penjualan.';
        } else {
            $error = 'Gagal menghapus produk: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Produk</title>
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
                <h1 class="text-xl font-bold text-gray-900">Hapus Produk</h1>
                <div class="w-10"></div>
            </div>
            <p class="mt-2 text-sm text-gray-700">Penghapusan data bersifat permanen.</p>
        </div>

        <div class="flex-1 px-6 pt-6 pb-12 space-y-6 overflow-y-auto">
            <?php if ($error): ?>
                <div class="bg-red-100 text-red-700 px-4 py-3 rounded-2xl text-sm font-medium">
                    ⚠️ <?= $error ?>
                </div>
            <?php endif; ?>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-red-100">
                <div class="flex flex-col items-center text-center mb-6">
                    <div class="w-20 h-20 rounded-full bg-red-50 flex items-center justify-center text-red-500 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-gray-800">Hapus Produk Ini?</h2>
                </div>

                <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-2xl mb-6">
                    <div class="w-16 h-16 bg-white rounded-xl overflow-hidden border border-gray-100 flex-shrink-0">
                        <?php if (!empty($selected['gambar'])): ?>
                            <img src="<?= htmlspecialchars($selected['gambar']) ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-400 text-xs">No Img</div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800 leading-tight"><?= htmlspecialchars($selected['nama_barang']) ?></p>
                        <p class="text-xs text-gray-500"><?= htmlspecialchars($selected['nama_kategori']) ?> • Rp <?= number_format($selected['harga'], 0, ',', '.') ?></p>
                        <p class="text-xs font-medium text-red-600 mt-1">Stok: <?= $selected['stok'] ?> <?= $selected['satuan'] ?></p>
                    </div>
                </div>

                <form method="POST" class="space-y-3">
                    <button type="submit" class="w-full bg-red-600 text-white py-4 rounded-2xl shadow-lg hover:bg-red-700 transition font-bold">
                        Ya, Hapus Produk
                    </button>
                    <a href="../../views/admin/produk.php" class="w-full inline-flex justify-center py-4 rounded-2xl border border-gray-200 text-gray-700 hover:bg-gray-50 transition font-bold text-sm">
                        Batalkan
                    </a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>