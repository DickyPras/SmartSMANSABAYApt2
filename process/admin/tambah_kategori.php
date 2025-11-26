<?php
$form_success = false;
$submitted_data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted_data = [
        'nama' => trim($_POST['nama'] ?? ''),
        'jumlah_produk' => (int) ($_POST['jumlah_produk'] ?? 0),
        'icon' => $_POST['icon'] ?? '📦',
        'warna' => $_POST['warna'] ?? 'bg-gray-100 text-gray-600',
    ];
    $form_success = true;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kategori</title>
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
                <h1 class="text-xl font-bold text-gray-900">Tambah Kategori</h1>
                <div class="w-10"></div>
            </div>
            <p class="text-sm text-gray-700">Masih dummy, belum tersambung database.</p>
        </div>

        <div class="flex-1 px-6 pt-6 pb-12 space-y-6 overflow-y-auto">
            <?php if ($form_success): ?>
            <div class="bg-green-100 text-green-700 px-4 py-3 rounded-2xl shadow-sm">
                <p class="font-semibold">Kategori dummy tersimpan!</p>
                <p class="text-sm mt-1">Nama: <span class="font-medium"><?= htmlspecialchars($submitted_data['nama']) ?></span></p>
                <p class="text-sm">Jumlah Produk: <span class="font-medium"><?= htmlspecialchars($submitted_data['jumlah_produk']) ?></span></p>
                <p class="text-sm">Icon: <span class="font-medium text-lg"><?= htmlspecialchars($submitted_data['icon']) ?></span></p>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Nama Kategori</label>
                    <input type="text" name="nama" required value="<?= htmlspecialchars($submitted_data['nama'] ?? '') ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" placeholder="contoh: Minuman Dingin">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Jumlah Produk</label>
                    <input type="number" name="jumlah_produk" min="0" required value="<?= htmlspecialchars($submitted_data['jumlah_produk'] ?? 0) ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Icon (Emoji)</label>
                    <input type="text" name="icon" maxlength="4" value="<?= htmlspecialchars($submitted_data['icon'] ?? '📦') ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" placeholder="contoh: 🥤">
                    <p class="text-xs text-gray-500 mt-1">Gunakan emoji agar konsisten dengan tampilan kartu kategori.</p>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Skema Warna</label>
                    <select name="warna" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                        <?php
                        $options = [
                            'bg-orange-100 text-orange-600' => 'Oranye (cocok untuk makanan)',
                            'bg-blue-100 text-blue-600' => 'Biru (cocok untuk minuman)',
                            'bg-purple-100 text-purple-600' => 'Ungu (alat tulis)',
                            'bg-pink-100 text-pink-600' => 'Pink (fashion)',
                            'bg-gray-100 text-gray-600' => 'Abu (umum)',
                        ];
                        $selected_color = $submitted_data['warna'] ?? 'bg-gray-100 text-gray-600';
                        foreach ($options as $value => $label):
                        ?>
                            <option value="<?= $value ?>" <?= $selected_color === $value ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="w-full bg-gray-900 text-white py-3 rounded-2xl shadow-lg hover:bg-gray-800 transition font-semibold">Simpan (Dummy)</button>
            </form>
        </div>
    </div>
</body>
</html>

