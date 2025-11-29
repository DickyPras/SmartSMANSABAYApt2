<?php
$data_produk = [
    [
        "id" => 1,
        "nama" => "Teh Pucuk Harum 350ml",
        "kategori" => "Minuman",
        "harga" => 4000,
        "stok" => 24,
        "gambar" => "https://assets.klikindomaret.com/products/20042075/20042075_1.jpg" 
    ],
    [
        "id" => 2,
        "nama" => "Roti Oksis Coklat",
        "kategori" => "Makanan",
        "harga" => 2500,
        "stok" => 5,
        "gambar" => "https://images.tokopedia.net/img/cache/700/VqbcmM/2022/6/15/8d10350d-6e83-490b-ba65-8cc080214c77.jpg"
    ],
    [
        "id" => 3,
        "nama" => "Pulpen Standard AE7",
        "kategori" => "Alat Tulis",
        "harga" => 3000,
        "stok" => 0,
        "gambar" => "https://down-id.img.susercontent.com/file/id-11134207-7r98o-lsmg3e6z63n2eb"
    ],
    [
        "id" => 4,
        "nama" => "Buku Tulis Sidu 38",
        "kategori" => "Alat Tulis",
        "harga" => 5000,
        "stok" => 50,
        "gambar" => "https://static.bmdstatic.com/pk/product/medium/5c6b6d5186088.jpg"
    ]
];

$id = (int) ($_GET['id'] ?? 1);
$selected = array_values(array_filter($data_produk, fn($row) => $row['id'] === $id))[0] ?? $data_produk[0];

$update_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected['nama'] = $_POST['nama'] ?? $selected['nama'];
    $selected['kategori'] = $_POST['kategori'] ?? $selected['kategori'];
    $selected['harga'] = (int) ($_POST['harga'] ?? $selected['harga']);
    $selected['stok'] = (int) ($_POST['stok'] ?? $selected['stok']);
    $selected['gambar'] = $_POST['gambar'] ?? $selected['gambar'];
    $update_success = true;
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
                <h1 class="text-xl font-bold text-gray-900">Edit Produk</h1>
                <div class="w-10"></div>
            </div>
            <p class="mt-2 text-sm text-gray-700">Perubahan hanya bersifat dummy.</p>
        </div>

        <div class="flex-1 px-6 pt-6 pb-12 space-y-6 overflow-y-auto">
            <?php if ($update_success): ?>
            <div class="bg-blue-100 text-blue-700 px-4 py-3 rounded-2xl shadow-sm">
                <p class="font-semibold">Data dummy diperbarui!</p>
                <p class="text-sm mt-1">Terakhir diubah: <?= date('d/m/Y H:i') ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($selected['gambar'])): ?>
            <div class="bg-white p-4 rounded-2xl shadow-sm">
                <p class="text-xs text-gray-500 mb-2">Gambar Produk</p>
                <div class="w-32 h-32 bg-gray-100 rounded-xl overflow-hidden">
                    <img src="<?= htmlspecialchars($selected['gambar']) ?>" alt="<?= htmlspecialchars($selected['nama']) ?>" class="w-full h-full object-cover">
                </div>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Nama Produk</label>
                    <input type="text" name="nama" required value="<?= htmlspecialchars($selected['nama']) ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Kategori</label>
                    <select name="kategori" required class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                        <option value="Makanan" <?= $selected['kategori'] === 'Makanan' ? 'selected' : '' ?>>Makanan</option>
                        <option value="Minuman" <?= $selected['kategori'] === 'Minuman' ? 'selected' : '' ?>>Minuman</option>
                        <option value="Alat Tulis" <?= $selected['kategori'] === 'Alat Tulis' ? 'selected' : '' ?>>Alat Tulis</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Harga (Rp)</label>
                    <input type="number" name="harga" required value="<?= htmlspecialchars($selected['harga']) ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" min="0">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Stok</label>
                    <input type="number" name="stok" required value="<?= htmlspecialchars($selected['stok']) ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" min="0">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">URL Gambar</label>
                    <input type="url" name="gambar" value="<?= htmlspecialchars($selected['gambar']) ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" placeholder="https://example.com/image.jpg">
                </div>

                <button type="submit" class="w-full bg-gray-900 text-white py-3 rounded-2xl shadow-lg hover:bg-gray-800 transition font-semibold">Update (Dummy)</button>
            </form>
        </div>
    </div>
</body>
</html>

