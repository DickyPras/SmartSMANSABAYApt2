<?php
require_once '../../config/koneksi.php';

$id = (int) ($_GET['id'] ?? 0);
$error = '';

if ($id <= 0) {
    header('Location: ../../views/admin/kategori.php');
    exit;
}

// 1. Ambil data kategori untuk preview dan hitung jumlah produk terkait
$sql_info = "SELECT k.*, (SELECT COUNT(*) FROM barang WHERE id_kategori = k.id_kategori) as total_barang 
             FROM kategori_barang k WHERE k.id_kategori = $id LIMIT 1";
$res = mysqli_query($koneksi, $sql_info);
$selected = ($res && mysqli_num_rows($res)) ? mysqli_fetch_assoc($res) : null;

if (!$selected) {
    header('Location: ../../views/admin/kategori.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 2. Cek apakah masih ada barang di kategori ini
    if ($selected['total_barang'] > 0) {
        $error = "Kategori tidak bisa dihapus karena masih memiliki " . $selected['total_barang'] . " produk terkait.";
    } else {
        // 3. Proses hapus permanen dari database
        $sql_delete = "DELETE FROM kategori_barang WHERE id_kategori = $id";
        if (mysqli_query($koneksi, $sql_delete)) {
            header('Location: ../../views/admin/kategori.php?deleted=1');
            exit;
        } else {
            $error = "Gagal menghapus kategori: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Kategori</title>
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
            <div class="flex items-center justify-between mb-4">
                <a href="../../views/admin/kategori.php" class="bg-white/20 p-2 rounded-xl hover:bg-white/40 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-xl font-bold text-gray-900">Hapus Kategori</h1>
                <div class="w-10"></div>
            </div>
            <p class="text-sm text-gray-700">Penghapusan kategori bersifat permanen di database.</p>
        </div>

        <div class="flex-1 px-6 pt-6 pb-12 space-y-6 overflow-y-auto">
            
            <?php if ($error !== ''): ?>
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded-2xl shadow-sm text-sm">
                <p class="font-bold">Gagal Menghapus!</p>
                <p><?= $error ?></p>
            </div>
            <?php endif; ?>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-red-200">
                <div class="flex flex-col items-center text-center mb-6">
                    <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center text-red-500 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-gray-800">Yakin ingin menghapus?</h2>
                    <p class="text-xs text-gray-500 mt-1">Data kategori "<?= htmlspecialchars($selected['nama_kategori']) ?>" akan hilang selamanya.</p>
                </div>

                <div class="bg-gray-50 p-4 rounded-2xl space-y-3">
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400">Nama Kategori</p>
                        <p class="font-bold text-gray-800"><?= htmlspecialchars($selected['nama_kategori']) ?></p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400">Status Produk</p>
                        <p class="font-bold <?= $selected['total_barang'] > 0 ? 'text-red-600' : 'text-green-600' ?>">
                            <?= $selected['total_barang'] ?> Produk Terkait
                        </p>
                    </div>
                    <?php if(!empty($selected['deskripsi'])): ?>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400">Deskripsi</p>
                        <p class="text-xs text-gray-600"><?= htmlspecialchars($selected['deskripsi']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <form method="POST" class="mt-8 space-y-3">
                    <button type="submit" 
                            class="w-full <?= $selected['total_barang'] > 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700 shadow-lg' ?> text-white py-4 rounded-2xl transition font-bold"
                            <?= $selected['total_barang'] > 0 ? 'disabled' : '' ?>>
                        Ya, Hapus Kategori
                    </button>
                    <a href="../../views/admin/kategori.php" class="w-full inline-flex justify-center py-4 rounded-2xl border border-gray-200 text-gray-700 hover:bg-gray-50 transition font-bold text-sm">
                        Batal
                    </a>
                </form>

                <?php if($selected['total_barang'] > 0): ?>
                    <p class="text-[10px] text-center text-red-500 mt-4 italic">
                        *Anda harus memindahkan atau menghapus produk di kategori ini terlebih dahulu.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>