<?php
require_once '../../config/koneksi.php';

$id = (int) ($_GET['id'] ?? 0);
$error = '';

if ($id <= 0) {
    header('Location: ../../views/admin/anggota.php');
    exit;
}

// Ambil data untuk preview konfirmasi penghapusan
$res = mysqli_query($koneksi, "SELECT * FROM users WHERE id_user = " . $id . " LIMIT 1");
$selected = ($res && mysqli_num_rows($res)) ? mysqli_fetch_assoc($res) : null;

// Jika data tidak ditemukan
if (!$selected) {
    header('Location: ../../views/admin/anggota.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Proses penghapusan permanen dari database
    $sql = "DELETE FROM users WHERE id_user = " . $id;
    
    try {
        if (mysqli_query($koneksi, $sql)) {
            header('Location: ../../views/admin/anggota.php?deleted=1');
            exit;
        } else {
            throw new Exception(mysqli_error($koneksi));
        }
    } catch (Exception $e) {
        // Menangani error Foreign Key (jika siswa sudah punya transaksi)
        if (str_contains($e->getMessage(), 'foreign key constraint')) {
            $error = 'Anggota tidak bisa dihapus karena memiliki riwayat transaksi.';
        } else {
            $error = 'Gagal menghapus: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Anggota</title>
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
                <h1 class="text-xl font-bold text-gray-900">Hapus Anggota</h1>
                <div class="w-10"></div>
            </div>
            <p class="mt-2 text-sm text-gray-700">Penghapusan data akan bersifat permanen di database.</p>
        </div>

        <div class="flex-1 px-6 pt-6 pb-12 space-y-6 overflow-y-auto">
            
            <?php if ($error !== ''): ?>
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded-2xl shadow-sm text-sm">
                <p class="font-bold">Gagal!</p>
                <p><?= $error ?></p>
            </div>
            <?php endif; ?>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-red-100">
                <div class="flex flex-col items-center text-center mb-6">
                    <div class="w-20 h-20 rounded-full bg-red-50 flex items-center justify-center text-red-500 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-gray-800">Konfirmasi Hapus</h2>
                    <p class="text-sm text-gray-500">Apakah Anda yakin ingin menghapus anggota ini secara permanen?</p>
                </div>

                <div class="bg-gray-50 p-4 rounded-2xl space-y-3">
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">Nama Anggota</p>
                        <p class="font-semibold text-gray-800"><?= htmlspecialchars($selected['nama']) ?></p>
                    </div>
                    <div class="flex justify-between">
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">NIS</p>
                            <p class="font-semibold text-gray-800"><?= htmlspecialchars($selected['nis'] ?? '-') ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">Kelas</p>
                            <p class="font-semibold text-gray-800"><?= htmlspecialchars($selected['kelas'] ?? '-') ?></p>
                        </div>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">Email</p>
                        <p class="font-semibold text-gray-800 text-sm"><?= htmlspecialchars($selected['email']) ?></p>
                    </div>
                </div>

                <form method="POST" class="mt-8 space-y-3">
                    <button type="submit" class="w-full bg-red-600 text-white py-4 rounded-2xl shadow-lg hover:bg-red-700 transition font-bold">
                        Ya, Hapus Sekarang
                    </button>
                    <a href="../../views/admin/anggota.php" class="w-full inline-flex justify-center py-4 rounded-2xl border border-gray-200 text-gray-700 hover:bg-gray-50 transition font-bold">
                        Batal
                    </a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>