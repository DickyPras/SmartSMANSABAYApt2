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

$delete_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delete_success = true;
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
                <h1 class="text-xl font-bold text-gray-900">Hapus Kategori</h1>
                <div class="w-10"></div>
            </div>
            <p class="text-sm text-gray-700">Belum terhubung database, hanya simulasi.</p>
        </div>

        <div class="flex-1 px-6 pt-6 pb-12 space-y-6 overflow-y-auto">
            <?php if ($delete_success): ?>
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded-2xl shadow-sm">
                <p class="font-semibold">Kategori dummy dihapus!</p>
                <p class="text-sm mt-1">ID: <?= $selected['id'] ?></p>
                <a href="../../views/admin/kategori.php" class="inline-flex items-center gap-2 text-sm text-red-800 underline mt-2">Kembali ke daftar kategori</a>
            </div>
            <?php else: ?>
            <div class="bg-white p-5 rounded-3xl shadow-sm border border-red-200">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center text-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-800 font-semibold">Konfirmasi hapus kategori?</p>
                        <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan (dummy).</p>
                    </div>
                </div>

                <div class="mt-5 space-y-2">
                    <p class="text-sm text-gray-500">Nama Kategori</p>
                    <p class="font-semibold text-gray-800"><?= htmlspecialchars($selected['nama']) ?></p>
                    <p class="text-sm text-gray-500 mt-3">Jumlah Produk</p>
                    <p class="font-semibold text-gray-800"><?= htmlspecialchars($selected['jumlah_produk']) ?> produk terkait</p>
                    <p class="text-sm text-gray-500 mt-3">Icon & Warna</p>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-semibold <?= htmlspecialchars($selected['warna']) ?>">
                        <span><?= htmlspecialchars($selected['icon']) ?></span>
                        <span>Badge</span>
                    </div>
                </div>

                <form method="POST" class="mt-6 space-y-3">
                    <button type="submit" class="w-full bg-red-600 text-white py-3 rounded-2xl shadow-lg hover:bg-red-500 transition font-semibold">Hapus (Dummy)</button>
                    <a href="../../views/admin/kategori.php" class="w-full inline-flex justify-center py-3 rounded-2xl border border-gray-200 text-gray-700 hover:bg-gray-50 transition font-semibold">Batal</a>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

