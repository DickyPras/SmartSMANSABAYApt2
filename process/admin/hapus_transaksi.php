<?php
require_once '../../config/koneksi.php';

// 1. Ambil ID Transaksi dari parameter URL
$id = (int) ($_GET['id'] ?? 0);
$error = '';

if ($id <= 0) {
    header('Location: ../../views/admin/transaksi.php');
    exit;
}

// 2. Ambil data transaksi dengan JOIN ke tabel users untuk preview yang informatif
$sql_info = "SELECT t.*, u.nama as nama_siswa 
             FROM transaksi t 
             JOIN users u ON t.id_user = u.id_user 
             WHERE t.id_transaksi = $id LIMIT 1";
$res = mysqli_query($koneksi, $sql_info);
$selected = ($res && mysqli_num_rows($res)) ? mysqli_fetch_assoc($res) : null;

// Jika data transaksi tidak ditemukan
if (!$selected) {
    header('Location: ../../views/admin/transaksi.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 3. Proses hapus permanen dari database
    // Karena detail_transaksi menggunakan ON DELETE CASCADE, rincian item akan otomatis terhapus
    $sql_delete = "DELETE FROM transaksi WHERE id_transaksi = $id";
    
    if (mysqli_query($koneksi, $sql_delete)) {
        // Redirect kembali ke daftar transaksi dengan pesan sukses
        header('Location: ../../views/admin/transaksi.php?deleted=1');
        exit;
    } else {
        $error = "Gagal menghapus transaksi: " . mysqli_error($koneksi);
    }
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
            <p class="text-sm text-gray-700">Tindakan ini akan menghapus seluruh data transaksi permanen.</p>
        </div>

        <div class="flex-1 px-6 pt-6 pb-12 space-y-6 overflow-y-auto">
            
            <?php if ($error !== ''): ?>
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded-2xl shadow-sm text-sm">
                <p class="font-bold">Gagal!</p>
                <p><?= $error ?></p>
            </div>
            <?php endif; ?>

            <div class="bg-white p-5 rounded-3xl shadow-sm border border-red-200 space-y-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center text-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-800 font-semibold">Konfirmasi hapus?</p>
                        <p class="text-sm text-gray-500 font-medium italic">Data ini akan hilang dari laporan.</p>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 space-y-3">
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">ID Transaksi</p>
                        <h3 class="font-bold text-gray-800">#<?= $selected['id_transaksi'] ?></h3>
                    </div>
                    
                    <div class="flex justify-between">
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Anggota</p>
                            <p class="text-sm font-semibold text-gray-700"><?= htmlspecialchars($selected['nama_siswa']) ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Tanggal</p>
                            <p class="text-[11px] font-medium text-gray-600"><?= date('d M Y, H:i', strtotime($selected['tanggal'])) ?></p>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-dashed border-gray-200">
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Total Bayar</p>
                        <p class="text-lg font-bold text-red-600">Rp <?= number_format($selected['total_harga'], 0, ',', '.') ?></p>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold border <?= $selected['status'] === 'dibayar' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-yellow-100 text-yellow-700 border-yellow-200' ?>">
                            <?= strtoupper($selected['status']) ?>
                        </span>
                    </div>
                </div>

                <form method="POST" class="space-y-3 pt-2">
                    <button type="submit" class="w-full bg-red-600 text-white py-4 rounded-2xl shadow-lg hover:bg-red-700 transition font-bold">Ya, Hapus Permanen</button>
                    <a href="../../views/admin/transaksi.php" class="w-full inline-flex justify-center py-4 rounded-2xl border border-gray-200 text-gray-700 hover:bg-gray-50 transition font-bold text-sm">Batal</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>