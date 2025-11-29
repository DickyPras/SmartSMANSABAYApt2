<?php
$form_success = false;
$submitted_data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted_data = [
        'nama' => $_POST['nama'] ?? '',
        'nis' => $_POST['nis'] ?? '',
        'kelas' => $_POST['kelas'] ?? '',
        'status' => $_POST['status'] ?? 'Aktif',
    ];
    $form_success = true;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Anggota</title>
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
                <h1 class="text-xl font-bold text-gray-900">Tambah Anggota</h1>
                <div class="w-10"></div>
            </div>
            <p class="mt-2 text-sm text-gray-700">Gunakan data dummy sebelum database siap.</p>
        </div>

        <div class="flex-1 px-6 pt-6 pb-12 space-y-6 overflow-y-auto">
            <?php if ($form_success): ?>
            <div class="bg-green-100 text-green-700 px-4 py-3 rounded-2xl shadow-sm">
                <p class="font-semibold">Data dummy tersimpan!</p>
                <p class="text-sm mt-1">Nama: <span class="font-medium"><?= htmlspecialchars($submitted_data['nama']) ?></span></p>
                <p class="text-sm">NIS: <span class="font-medium"><?= htmlspecialchars($submitted_data['nis']) ?></span></p>
                <p class="text-sm">Kelas: <span class="font-medium"><?= htmlspecialchars($submitted_data['kelas']) ?></span></p>
                <p class="text-sm">Status: <span class="font-medium"><?= htmlspecialchars($submitted_data['status']) ?></span></p>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Nama Lengkap</label>
                    <input type="text" name="nama" required value="<?= htmlspecialchars($submitted_data['nama'] ?? '') ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" placeholder="Masukkan nama siswa">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">NIS</label>
                    <input type="text" name="nis" required value="<?= htmlspecialchars($submitted_data['nis'] ?? '') ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" placeholder="Nomor Induk Siswa">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Kelas</label>
                    <input type="text" name="kelas" required value="<?= htmlspecialchars($submitted_data['kelas'] ?? '') ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" placeholder="contoh: XII RPL 1">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                        <option value="Aktif" <?= (($submitted_data['status'] ?? '') === 'Aktif') ? 'selected' : '' ?>>Aktif</option>
                        <option value="Non-Aktif" <?= (($submitted_data['status'] ?? '') === 'Non-Aktif') ? 'selected' : '' ?>>Non-Aktif</option>
                    </select>
                </div>

                <button type="submit" class="w-full bg-gray-900 text-white py-3 rounded-2xl shadow-lg hover:bg-gray-800 transition font-semibold">Simpan (Dummy)</button>
            </form>
        </div>
    </div>
</body>
</html>

