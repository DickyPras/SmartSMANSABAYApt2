<?php
$data_kategori = [
    [
        "id" => 1,
        "nama" => "Makanan Ringan",
        "jumlah_produk" => 45,
        "icon" => "🍔",
        "warna" => "bg-orange-100 text-orange-600"
    ],
    [
        "id" => 2,
        "nama" => "Minuman Dingin",
        "jumlah_produk" => 23,
        "icon" => "🥤",
        "warna" => "bg-blue-100 text-blue-600"
    ],
    [
        "id" => 3,
        "nama" => "Alat Tulis (ATK)",
        "jumlah_produk" => 150,
        "icon" => "✏️",
        "warna" => "bg-purple-100 text-purple-600"
    ],
    [
        "id" => 4,
        "nama" => "Seragam & Atribut",
        "jumlah_produk" => 12,
        "icon" => "👕",
        "warna" => "bg-pink-100 text-pink-600"
    ],
    [
        "id" => 5,
        "nama" => "Jasa Print/Fotokopi",
        "jumlah_produk" => 5,
        "icon" => "🖨️",
        "warna" => "bg-gray-100 text-gray-600"
    ],
];

$id = (int) ($_GET['id'] ?? 1);
$selected = array_values(array_filter($data_kategori, fn($row) => $row['id'] === $id))[0] ?? $data_kategori[0];

$update_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected['nama'] = trim($_POST['nama'] ?? $selected['nama']);
    $selected['jumlah_produk'] = (int) ($_POST['jumlah_produk'] ?? $selected['jumlah_produk']);
    $selected['icon'] = $_POST['icon'] ?? $selected['icon'];
    $selected['warna'] = $_POST['warna'] ?? $selected['warna'];
    $update_success = true;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kategori</title>
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
            <div class="flex items-center justify-between mb-4">
                <a href="../../views/admin/kategori.php" class="bg-white/20 p-2 rounded-xl hover:bg-white/40 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-xl font-bold text-gray-900">Edit Kategori</h1>
                <div class="w-10"></div>
            </div>
            <p class="text-sm text-gray-700">Semua perubahan masih dummy (belum simpan DB).</p>
        </div>

        <div class="flex-1 px-6 pt-6 pb-12 space-y-6 overflow-y-auto">
            <?php if ($update_success): ?>
            <div class="bg-blue-100 text-blue-700 px-4 py-3 rounded-2xl shadow-sm">
                <p class="font-semibold">Kategori dummy diperbarui!</p>
                <p class="text-sm mt-1">Terakhir diubah: <?= date('d/m/Y H:i') ?></p>
            </div>
            <?php endif; ?>

            <div class="bg-white rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="w-14 h-14 rounded-full <?= htmlspecialchars($selected['warna']) ?> flex items-center justify-center text-2xl">
                    <?= htmlspecialchars($selected['icon']) ?>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase">Preview</p>
                    <h3 class="font-semibold text-gray-800"><?= htmlspecialchars($selected['nama']) ?></h3>
                    <p class="text-xs text-gray-500"><?= htmlspecialchars($selected['jumlah_produk']) ?> produk</p>
                </div>
            </div>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Nama Kategori</label>
                    <input type="text" name="nama" required value="<?= htmlspecialchars($selected['nama']) ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Jumlah Produk</label>
                    <input type="number" name="jumlah_produk" min="0" required value="<?= htmlspecialchars($selected['jumlah_produk']) ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Icon (Emoji)</label>
                    <input type="text" name="icon" maxlength="4" value="<?= htmlspecialchars($selected['icon']) ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Skema Warna</label>
                    <?php
                    $options = [
                        'bg-orange-100 text-orange-600' => 'Oranye - ceria',
                        'bg-blue-100 text-blue-600' => 'Biru - segar',
                        'bg-purple-100 text-purple-600' => 'Ungu - premium',
                        'bg-pink-100 text-pink-600' => 'Pink - stylish',
                        'bg-gray-100 text-gray-600' => 'Abu - netral',
                    ];
                    ?>
                    <select name="warna" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                        <?php foreach ($options as $value => $label): ?>
                            <option value="<?= $value ?>" <?= $selected['warna'] === $value ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="w-full bg-gray-900 text-white py-3 rounded-2xl shadow-lg hover:bg-gray-800 transition font-semibold">Update (Dummy)</button>
            </form>
        </div>
    </div>
</body>
</html>

