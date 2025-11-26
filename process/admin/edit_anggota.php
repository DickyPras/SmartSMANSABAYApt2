<?php
$data_anggota = [
    [
        "id" => 1,
        "nama" => "Ahmad Dhani",
        "nis" => "12345678",
        "kelas" => "XII RPL 1",
        "status" => "Aktif"
    ],
    [
        "id" => 2,
        "nama" => "Maya Estianty",
        "nis" => "87654321",
        "kelas" => "XI TKJ 2",
        "status" => "Aktif"
    ],
    [
        "id" => 3,
        "nama" => "Mulan Jameela",
        "nis" => "11223344",
        "kelas" => "X AKL 1",
        "status" => "Non-Aktif"
    ],
];

$id = (int) ($_GET['id'] ?? 1);
$selected = array_values(array_filter($data_anggota, fn($row) => $row['id'] === $id))[0] ?? $data_anggota[0];

$update_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected['nama'] = $_POST['nama'] ?? $selected['nama'];
    $selected['nis'] = $_POST['nis'] ?? $selected['nis'];
    $selected['kelas'] = $_POST['kelas'] ?? $selected['kelas'];
    $selected['status'] = $_POST['status'] ?? $selected['status'];
    $update_success = true;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Anggota</title>
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
                <a href="../../views/admin/anggota.php" class="bg-white/20 p-2 rounded-xl hover:bg-white/40 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-xl font-bold text-gray-900">Edit Anggota</h1>
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

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Nama Lengkap</label>
                    <input type="text" name="nama" required value="<?= htmlspecialchars($selected['nama']) ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">NIS</label>
                    <input type="text" name="nis" required value="<?= htmlspecialchars($selected['nis']) ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Kelas</label>
                    <input type="text" name="kelas" required value="<?= htmlspecialchars($selected['kelas']) ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                        <option value="Aktif" <?= $selected['status'] === 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="Non-Aktif" <?= $selected['status'] === 'Non-Aktif' ? 'selected' : '' ?>>Non-Aktif</option>
                    </select>
                </div>

                <button type="submit" class="w-full bg-gray-900 text-white py-3 rounded-2xl shadow-lg hover:bg-gray-800 transition font-semibold">Update (Dummy)</button>
            </form>
        </div>
    </div>
</body>
</html>

