<?php
require_once '../../config/koneksi.php';

// 1. Ambil ID dari URL dan validasi data produk
$id = (int) ($_GET['id'] ?? 0);
$selected = null;
$error = '';

if ($id > 0) {
    // Ambil data produk berdasarkan id_barang
    $res = mysqli_query($koneksi, "SELECT * FROM barang WHERE id_barang = $id LIMIT 1");
    if ($res && mysqli_num_rows($res) > 0) {
        $selected = mysqli_fetch_assoc($res);
    }
}

// Redirect jika produk tidak ditemukan
if (!$selected) {
    header('Location: ../../views/admin/produk.php');
    exit;
}

// 2. Ambil daftar kategori untuk dropdown
$list_kategori = mysqli_query($koneksi, "SELECT * FROM kategori_barang ORDER BY nama_kategori ASC");

// 3. Proses Update Data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama        = trim(mysqli_real_escape_string($koneksi, $_POST['nama'] ?? ''));
    $id_kategori = (int)($_POST['id_kategori'] ?? 0);
    $harga       = (int)($_POST['harga'] ?? 0);
    $stok        = (int)($_POST['stok'] ?? 0);
    $satuan      = trim(mysqli_real_escape_string($koneksi, $_POST['satuan'] ?? ''));
    $gambar      = trim(mysqli_real_escape_string($koneksi, $_POST['gambar'] ?? ''));

    if ($nama !== '' && $id_kategori > 0 && $harga >= 0) {
        $sql_update = "UPDATE barang SET 
                        id_kategori = '$id_kategori',
                        nama_barang = '$nama',
                        harga = '$harga',
                        stok = '$stok',
                        satuan = '$satuan',
                        gambar = '$gambar'
                       WHERE id_barang = $id";

        if (mysqli_query($koneksi, $sql_update)) {
            header('Location: ../../views/admin/produk.php?updated=1');
            exit;
        } else {
            $error = "Gagal memperbarui data: " . mysqli_error($koneksi);
        }
    } else {
        $error = "Nama, Kategori, dan Harga wajib diisi dengan benar.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
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
                <h1 class="text-xl font-bold text-gray-900">Edit Produk</h1>
                <div class="w-10"></div>
            </div>
            <p class="mt-2 text-sm text-gray-700">Perbarui informasi produk di sistem.</p>
        </div>

        <div class="flex-1 px-6 pt-6 pb-12 space-y-6 overflow-y-auto">
            <?php if ($error): ?>
                <div class="bg-red-100 text-red-700 px-4 py-3 rounded-2xl text-xs"><?= $error ?></div>
            <?php endif; ?>

            <div class="bg-white p-4 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-20 h-20 bg-gray-50 rounded-2xl overflow-hidden border border-gray-100">
                    <?php if (!empty($selected['gambar'])): ?>
                        <img src="<?= htmlspecialchars($selected['gambar']) ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-[10px] text-gray-400">No Image</div>
                    <?php endif; ?>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase">Produk ID: #<?= $selected['id_barang'] ?></p>
                    <p class="font-bold text-gray-800 leading-tight"><?= htmlspecialchars($selected['nama_barang']) ?></p>
                </div>
            </div>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nama Produk</label>
                    <input type="text" name="nama" required value="<?= htmlspecialchars($selected['nama_barang']) ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Kategori</label>
                    <select name="id_kategori" required class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                        <?php while($kat = mysqli_fetch_assoc($list_kategori)): ?>
                            <option value="<?= $kat['id_kategori'] ?>" <?= $selected['id_kategori'] == $kat['id_kategori'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($kat['nama_kategori']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Harga (Rp)</label>
                        <input type="number" name="harga" required value="<?= $selected['harga'] ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" min="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Satuan</label>
                        <input type="text" name="satuan" value="<?= htmlspecialchars($selected['satuan']) ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Stok Tersedia</label>
                    <input type="number" name="stok" required value="<?= $selected['stok'] ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" min="0">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">URL Gambar Baru</label>
                    <input type="url" name="gambar" value="<?= htmlspecialchars($selected['gambar']) ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" placeholder="https://...">
                </div>

                <button type="submit" class="w-full bg-gray-900 text-white py-4 rounded-2xl shadow-lg hover:bg-gray-800 transition font-bold mt-4">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</body>
</html>