<?php
require_once '../../config/koneksi.php';

// 1. Ambil ID dari URL dan validasi data kategori
$id = (int) ($_GET['id'] ?? 0);
$selected = null;
$error = '';

if ($id > 0) {
    // Ambil data kategori berdasarkan id_kategori dan hitung jumlah produk aslinya
    $sql_get = "SELECT k.*, (SELECT COUNT(*) FROM barang WHERE id_kategori = k.id_kategori) as total_barang 
                FROM kategori_barang k WHERE k.id_kategori = $id LIMIT 1";
    $res = mysqli_query($koneksi, $sql_get);
    if ($res && mysqli_num_rows($res) > 0) {
        $selected = mysqli_fetch_assoc($res);
    }
}

// Redirect jika kategori tidak ditemukan
if (!$selected) {
    header('Location: ../../views/admin/kategori.php');
    exit;
}

// 2. Proses Update Data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim(mysqli_real_escape_string($koneksi, $_POST['nama'] ?? ''));
    $deskripsi = trim(mysqli_real_escape_string($koneksi, $_POST['deskripsi'] ?? ''));

    if ($nama !== '') {
        $sql_update = "UPDATE kategori_barang SET 
                        nama_kategori = '$nama', 
                        deskripsi = '$deskripsi' 
                       WHERE id_kategori = $id";
        
        if (mysqli_query($koneksi, $sql_update)) {
            header('Location: ../../views/admin/kategori.php?updated=1');
            exit;
        } else {
            $error = "Gagal memperbarui database: " . mysqli_error($koneksi);
        }
    } else {
        $error = "Nama kategori tidak boleh kosong.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kategori</title>
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
                <h1 class="text-xl font-bold text-gray-900">Edit Kategori</h1>
                <div class="w-10"></div>
            </div>
            <p class="text-sm text-gray-700">Perbarui informasi kategori barang di database.</p>
        </div>

        <div class="flex-1 px-6 pt-6 pb-12 space-y-6 overflow-y-auto">
            
            <?php if ($error !== ''): ?>
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded-2xl shadow-sm text-xs italic">
                <?= $error ?>
            </div>
            <?php endif; ?>

            <div class="bg-white rounded-3xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-yellow-100 flex items-center justify-center text-2xl text-yellow-600">
                    📦
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Kategori ID: #<?= $selected['id_kategori'] ?></p>
                    <h3 class="font-bold text-gray-800"><?= htmlspecialchars($selected['nama_kategori']) ?></h3>
                    <p class="text-xs text-gray-500 font-medium"><?= $selected['total_barang'] ?> produk terdaftar</p>
                </div>
            </div>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Nama Kategori</label>
                    <input type="text" name="nama" required value="<?= htmlspecialchars($selected['nama_kategori']) ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" placeholder="Contoh: Minuman Dingin">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Deskripsi / Catatan</label>
                    <textarea name="deskripsi" rows="4" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" placeholder="Masukkan deskripsi kategori..."><?= htmlspecialchars($selected['deskripsi']) ?></textarea>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-gray-900 text-white py-4 rounded-2xl shadow-lg hover:bg-gray-800 transition font-bold text-lg">
                        Simpan Perubahan
                    </button>
                    <a href="../../views/admin/kategori.php" class="block text-center mt-4 text-sm text-gray-500 font-medium hover:text-gray-800 transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>