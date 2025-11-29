<?php
$data_transaksi = [
    [
        "no_invoice" => "TRX-20231025-001",
        "anggota" => "Ahmad Dhani",
        "tanggal" => "2025-10-25 08:30:00",
        "total" => 15000,
        "items" => 3,
        "status" => "Lunas"
    ],
    [
        "no_invoice" => "TRX-20231025-002",
        "anggota" => "Maya Estianty",
        "tanggal" => "2025-10-25 09:15:00",
        "total" => 8500,
        "items" => 2,
        "status" => "Lunas"
    ],
    [
        "no_invoice" => "TRX-20231025-003",
        "anggota" => "Mulan Jameela",
        "tanggal" => "2025-10-25 10:00:00",
        "total" => 50000,
        "items" => 5,
        "status" => "Pending"
    ],
];

$invoice = $_GET['invoice'] ?? $data_transaksi[0]['no_invoice'];
$selected = array_values(array_filter($data_transaksi, fn($row) => $row['no_invoice'] === $invoice))[0] ?? $data_transaksi[0];

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
    <title>Hapus Transaksi</title>
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
            <div class="flex items-center justify-between mb-6">
                <a href="../../views/admin/transaksi.php" class="bg-white/20 p-2 rounded-xl hover:bg-white/40 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-xl font-bold text-gray-900">Hapus Transaksi</h1>
                <div class="w-10"></div>
            </div>
            <p class="text-sm text-gray-700">Belum benar-benar menghapus dari database.</p>
        </div>

        <div class="flex-1 px-6 pt-6 pb-12 space-y-6 overflow-y-auto">
            <?php if ($delete_success): ?>
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded-2xl shadow-sm">
                <p class="font-semibold">Transaksi dummy dihapus!</p>
                <p class="text-sm mt-1">Invoice: <?= htmlspecialchars($selected['no_invoice']) ?></p>
                <a href="../../views/admin/transaksi.php" class="inline-flex items-center gap-2 text-sm text-red-800 underline mt-2">Kembali ke daftar transaksi</a>
            </div>
            <?php else: ?>
            <div class="bg-white p-5 rounded-3xl shadow-sm border border-red-200 space-y-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center text-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-800 font-semibold">Konfirmasi hapus transaksi?</p>
                        <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan (dummy).</p>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                    <p class="text-xs text-gray-400 uppercase">Invoice</p>
                    <h3 class="font-bold text-gray-800"><?= htmlspecialchars($selected['no_invoice']) ?></h3>
                    <p class="text-xs text-gray-500"><?= htmlspecialchars($selected['anggota']) ?> • <?= htmlspecialchars($selected['items']) ?> barang</p>
                    <p class="text-sm font-semibold text-gray-900 mt-2">Rp <?= number_format($selected['total'], 0, ',', '.') ?></p>
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-1 rounded-lg mt-2 border <?= $selected['status'] === 'Lunas' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-yellow-100 text-yellow-700 border-yellow-200' ?>">
                        <?= $selected['status'] ?>
                    </span>
                </div>

                <form method="POST" class="space-y-3">
                    <button type="submit" class="w-full bg-red-600 text-white py-3 rounded-2xl shadow-lg hover:bg-red-500 transition font-semibold">Hapus (Dummy)</button>
                    <a href="../../views/admin/transaksi.php" class="w-full inline-flex justify-center py-3 rounded-2xl border border-gray-200 text-gray-700 hover:bg-gray-50 transition font-semibold">Batal</a>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

