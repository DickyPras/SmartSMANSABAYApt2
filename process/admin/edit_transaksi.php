<?php
require_once '../../config/koneksi.php';

// 1. Ambil ID Transaksi dari parameter URL
$id = (int) ($_GET['id'] ?? 0);
$selected = null;
$error = '';

if ($id > 0) {
    // Ambil data transaksi dan gabungkan dengan nama user (siswa)
    $sql_get = "SELECT t.*, u.nama as nama_siswa 
                FROM transaksi t 
                JOIN users u ON t.id_user = u.id_user 
                WHERE t.id_transaksi = $id LIMIT 1";
    $res = mysqli_query($koneksi, $sql_get);
    if ($res && mysqli_num_rows($res) > 0) {
        $selected = mysqli_fetch_assoc($res);
    }
}

// Redirect jika ID tidak ditemukan di database
if (!$selected) {
    header('Location: ../../views/admin/transaksi.php');
    exit;
}

// 2. Proses Update Data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal     = $_POST['tanggal'] ?? date('Y-m-d H:i:s');
    $total_harga = (int) ($_POST['total_harga'] ?? 0);
    $status      = mysqli_real_escape_string($koneksi, $_POST['status'] ?? 'pending');

    if ($total_harga >= 0) {
        // Query UPDATE sesuai skema tabel transaksi
        $sql_update = "UPDATE transaksi SET 
                        tanggal = '$tanggal', 
                        total_harga = '$total_harga', 
                        status = '$status' 
                       WHERE id_transaksi = $id";
        
        if (mysqli_query($koneksi, $sql_update)) {
            header('Location: ../../views/admin/transaksi.php?updated=1');
            exit;
        } else {
            $error = "Gagal memperbarui database: " . mysqli_error($koneksi);
        }
    } else {
        $error = "Total harga tidak valid.";
    }
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
            <p class="text-sm text-gray-700">Perbarui rincian transaksi di database sistem.</p>
        </div>

        <div class="flex-1 px-6 pt-6 pb-12 space-y-6 overflow-y-auto">
            
            <?php if ($error !== ''): ?>
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded-2xl shadow-sm text-xs italic">
                <?= $error ?>
            </div>
            <?php endif; ?>

            <div class="bg-white rounded-3xl p-5 shadow-sm border border-gray-100">
                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Detail Siswa</p>
                <h3 class="font-bold text-gray-800 text-lg"><?= htmlspecialchars($selected['nama_siswa']) ?></h3>
                <div class="mt-2 pt-2 border-t border-dashed border-gray-100">
                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">ID Transaksi</p>
                    <p class="font-mono text-sm text-gray-600">#<?= $selected['id_transaksi'] ?></p>
                </div>
            </div>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Tanggal & Jam</label>
                    <input type="datetime-local" name="tanggal" required 
                           value="<?= date('Y-m-d\TH:i', strtotime($selected['tanggal'])) ?>" 
                           class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Total Harga (Rp)</label>
                    <input type="number" name="total_harga" min="0" required 
                           value="<?= $selected['total_harga'] ?>" 
                           class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white font-bold text-gray-800">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Status Pembayaran</label>
                    <div class="grid grid-cols-2 gap-3">
                        <?php 
                        // Menyesuaikan status dengan tipe ENUM di database
                        $status_options = [
                            'dibayar' => 'LUNAS',
                            'pending' => 'PENDING'
                        ];
                        foreach ($status_options as $val => $label): 
                            $is_active = ($selected['status'] === $val);
                        ?>
                        <label class="border rounded-2xl px-4 py-3 text-center cursor-pointer transition-all 
                                    <?= $is_active ? 'bg-gray-900 border-gray-900 text-white font-bold' : 'bg-white border-gray-200 text-gray-500' ?>">
                            <input type="radio" name="status" value="<?= $val ?>" class="hidden" <?= $is_active ? 'checked' : '' ?>>
                            <?= $label ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full bg-gray-900 text-white py-4 rounded-3xl shadow-xl hover:bg-gray-800 transition font-bold text-lg">
                        Simpan Perubahan
                    </button>
                    <a href="../../views/admin/transaksi.php" class="block text-center mt-4 text-sm text-gray-400 font-medium hover:text-gray-700">
                        Batalkan
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>