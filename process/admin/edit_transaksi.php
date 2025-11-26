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

$update_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected['anggota'] = $_POST['anggota'] ?? $selected['anggota'];
    $selected['tanggal'] = $_POST['tanggal'] ?? date('Y-m-d H:i:s');
    $selected['total'] = (int) ($_POST['total'] ?? $selected['total']);
    $selected['items'] = (int) ($_POST['items'] ?? $selected['items']);
    $selected['status'] = $_POST['status'] ?? $selected['status'];
    $update_success = true;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Transaksi</title>
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
                <h1 class="text-xl font-bold text-gray-900">Edit Transaksi</h1>
                <div class="w-10"></div>
            </div>
            <p class="text-sm text-gray-700">Perubahan hanya simulasi, belum update database.</p>
        </div>

        <div class="flex-1 px-6 pt-6 pb-12 space-y-6 overflow-y-auto">
            <?php if ($update_success): ?>
            <div class="bg-blue-100 text-blue-700 px-4 py-3 rounded-2xl shadow-sm">
                <p class="font-semibold">Transaksi dummy diperbarui!</p>
                <p class="text-sm mt-1">Invoice: <?= htmlspecialchars($selected['no_invoice']) ?></p>
            </div>
            <?php endif; ?>

            <div class="bg-white rounded-2xl p-4 shadow-sm">
                <p class="text-xs text-gray-400 uppercase tracking-wide">No. Invoice</p>
                <h3 class="font-bold text-gray-800"><?= htmlspecialchars($selected['no_invoice']) ?></h3>
                <p class="text-xs text-gray-500 mt-1"><?= date('d M Y H:i', strtotime($selected['tanggal'])) ?> WIB</p>
            </div>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Nama Anggota</label>
                    <input type="text" name="anggota" required value="<?= htmlspecialchars($selected['anggota']) ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Tanggal & Jam</label>
                    <input type="datetime-local" name="tanggal" required value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($selected['tanggal']))) ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Total (Rp)</label>
                        <input type="number" name="total" min="0" required value="<?= htmlspecialchars($selected['total']) ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Jumlah Item</label>
                        <input type="number" name="items" min="1" required value="<?= htmlspecialchars($selected['items']) ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                    </div>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-2">Status</label>
                    <div class="flex gap-3">
                        <?php foreach (['Lunas', 'Pending'] as $status): ?>
                        <label class="flex-1 border rounded-2xl px-4 py-3 text-center cursor-pointer <?= ($selected['status'] === $status) ? 'border-gray-900 text-gray-900 font-semibold' : 'border-gray-200 text-gray-500' ?>">
                            <input type="radio" name="status" value="<?= $status ?>" class="hidden" <?= $selected['status'] === $status ? 'checked' : '' ?>>
                            <?= $status ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="submit" class="w-full bg-gray-900 text-white py-3 rounded-2xl shadow-lg hover:bg-gray-800 transition font-semibold">Update (Dummy)</button>
            </form>
        </div>
    </div>
</body>
</html>

