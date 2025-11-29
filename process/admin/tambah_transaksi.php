<?php
$form_success = false;
$submitted_data = [
    'no_invoice' => 'TRX-' . date('Ymd-His'),
    'anggota' => '',
    'tanggal' => date('Y-m-d\TH:i'),
    'total' => '',
    'items' => 1,
    'status' => 'Lunas',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted_data = [
        'no_invoice' => $_POST['no_invoice'] ?? $submitted_data['no_invoice'],
        'anggota' => $_POST['anggota'] ?? '',
        'tanggal' => $_POST['tanggal'] ?? date('Y-m-d\TH:i'),
        'total' => (int) ($_POST['total'] ?? 0),
        'items' => (int) ($_POST['items'] ?? 1),
        'status' => $_POST['status'] ?? 'Lunas',
    ];
    $form_success = true;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Transaksi</title>
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
                <h1 class="text-xl font-bold text-gray-900">Transaksi Baru</h1>
                <div class="w-10"></div>
            </div>
            <p class="text-sm text-gray-700">Masih dummy, belum mencatat ke database.</p>
        </div>

        <div class="flex-1 px-6 pt-6 pb-12 space-y-6 overflow-y-auto">
            <?php if ($form_success): ?>
            <div class="bg-green-100 text-green-700 px-4 py-3 rounded-2xl shadow-sm">
                <p class="font-semibold">Transaksi dummy tersimpan!</p>
                <p class="text-sm mt-1">Invoice: <span class="font-medium"><?= htmlspecialchars($submitted_data['no_invoice']) ?></span></p>
                <p class="text-sm">Total: <span class="font-medium">Rp <?= number_format($submitted_data['total'], 0, ',', '.') ?></span></p>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">No. Invoice</label>
                    <input type="text" name="no_invoice" required value="<?= htmlspecialchars($submitted_data['no_invoice']) ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Nama Anggota</label>
                    <input type="text" name="anggota" required value="<?= htmlspecialchars($submitted_data['anggota']) ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" placeholder="contoh: Ahmad Dhani">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Tanggal & Jam</label>
                    <input type="datetime-local" name="tanggal" required value="<?= htmlspecialchars($submitted_data['tanggal']) ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Total (Rp)</label>
                        <input type="number" name="total" min="0" required value="<?= htmlspecialchars($submitted_data['total']) ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Jumlah Item</label>
                        <input type="number" name="items" min="1" required value="<?= htmlspecialchars($submitted_data['items']) ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                    </div>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-2">Status</label>
                    <div class="flex gap-3">
                        <?php foreach (['Lunas', 'Pending'] as $status): ?>
                        <label class="flex-1 border rounded-2xl px-4 py-3 text-center cursor-pointer <?= ($submitted_data['status'] === $status) ? 'border-gray-900 text-gray-900 font-semibold' : 'border-gray-200 text-gray-500' ?>">
                            <input type="radio" name="status" value="<?= $status ?>" class="hidden" <?= $submitted_data['status'] === $status ? 'checked' : '' ?>>
                            <?= $status ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="submit" class="w-full bg-gray-900 text-white py-3 rounded-2xl shadow-lg hover:bg-gray-800 transition font-semibold">Simpan (Dummy)</button>
            </form>
        </div>
    </div>
</body>
</html>

