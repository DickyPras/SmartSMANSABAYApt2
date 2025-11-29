<?php
$form_success = false;
$submitted_data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted_data = [
        'nama' => $_POST['nama'] ?? '',
        'kategori' => $_POST['kategori'] ?? '',
        'harga' => $_POST['harga'] ?? '',
        'stok' => $_POST['stok'] ?? '',
        'gambar' => $_POST['gambar'] ?? '',
    ];
    $form_success = true;
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
                    colors: {
                        'primary': '#FACC15',
                        'bg-soft': '#F0FDF4',
                    }
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
            <p class="mt-2 text-sm text-gray-700">Gunakan data dummy sebelum database siap.</p>
        </div>

        <div class="flex-1 px-6 pt-6 pb-12 space-y-6 overflow-y-auto">
            <?php if ($form_success): ?>
            <div class="bg-green-100 text-green-700 px-4 py-3 rounded-2xl shadow-sm">
                <p class="font-semibold">Data dummy tersimpan!</p>
                <p class="text-sm mt-1">Nama: <span class="font-medium"><?= htmlspecialchars($submitted_data['nama']) ?></span></p>
                <p class="text-sm">Kategori: <span class="font-medium"><?= htmlspecialchars($submitted_data['kategori']) ?></span></p>
                <p class="text-sm">Harga: <span class="font-medium">Rp <?= number_format((int)$submitted_data['harga'], 0, ',', '.') ?></span></p>
                <p class="text-sm">Stok: <span class="font-medium"><?= htmlspecialchars($submitted_data['stok']) ?></span></p>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Nama Produk</label>
                    <input type="text" name="nama" required value="<?= htmlspecialchars($submitted_data['nama'] ?? '') ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" placeholder="Masukkan nama produk">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Kategori</label>
                    <select name="kategori" required class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                        <option value="">Pilih Kategori</option>
                        <option value="Makanan" <?= (($submitted_data['kategori'] ?? '') === 'Makanan') ? 'selected' : '' ?>>Makanan</option>
                        <option value="Minuman" <?= (($submitted_data['kategori'] ?? '') === 'Minuman') ? 'selected' : '' ?>>Minuman</option>
                        <option value="Alat Tulis" <?= (($submitted_data['kategori'] ?? '') === 'Alat Tulis') ? 'selected' : '' ?>>Alat Tulis</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Harga (Rp)</label>
                    <input type="number" name="harga" required value="<?= htmlspecialchars($submitted_data['harga'] ?? '') ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" placeholder="contoh: 5000" min="0">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Stok</label>
                    <input type="number" name="stok" required value="<?= htmlspecialchars($submitted_data['stok'] ?? '') ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" placeholder="Jumlah stok" min="0">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">URL Gambar</label>
                    <input type="url" name="gambar" value="<?= htmlspecialchars($submitted_data['gambar'] ?? '') ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" placeholder="https://example.com/image.jpg">
                    <p class="text-xs text-gray-500 mt-1">Opsional: Masukkan URL gambar produk</p>
                </div>

                <button type="submit" class="w-full bg-gray-900 text-white py-3 rounded-2xl shadow-lg hover:bg-gray-800 transition font-semibold">Simpan (Dummy)</button>
            </form>
        </div>
    </div>
</body>
</html>

