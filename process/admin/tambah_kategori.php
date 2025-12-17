<?php
require_once '../../config/koneksi.php';

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitasi input sesuai tabel kategori_barang
    $nama = trim(mysqli_real_escape_string($koneksi, $_POST['nama'] ?? ''));
    $icon = trim(mysqli_real_escape_string($koneksi, $_POST['icon'] ?? ''));
    $deskripsi_input = trim(mysqli_real_escape_string($koneksi, $_POST['deskripsi'] ?? ''));
    
    // Menggabungkan icon ke dalam deskripsi agar tersimpan di database yang ada
    $deskripsi_final = "Icon: " . $icon . " | " . $deskripsi_input;

    if ($nama !== '') {
        // Query INSERT sesuai struktur esmart_db
        $sql = "INSERT INTO kategori_barang (nama_kategori, deskripsi) VALUES ('$nama', '$deskripsi_final')";
        
        if (mysqli_query($koneksi, $sql)) {
            // Redirect kembali ke daftar kategori dengan pesan sukses
            header('Location: ../../views/admin/kategori.php?success=1');
            exit;
        } else {
            $error = "Gagal menyimpan: " . mysqli_error($koneksi);
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
    <title>Tambah Kategori</title>
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
                <h1 class="text-xl font-bold text-gray-900">Tambah Kategori</h1>
                <div class="w-10"></div>
            </div>
            <p class="text-sm text-gray-700">Tambahkan kategori barang baru ke dalam sistem.</p>
        </div>

        <div class="flex-1 px-6 pt-6 pb-12 space-y-6 overflow-y-auto">
            
            <?php if ($error !== ''): ?>
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded-2xl shadow-sm text-xs italic">
                <?= $error ?>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nama Kategori</label>
                    <input type="text" name="nama" required class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" placeholder="Contoh: Snack & Keripik">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Icon (Emoji)</label>
                    <input type="text" name="icon" maxlength="4" value="📦" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" placeholder="contoh: 🥤">
                    <p class="text-[10px] text-gray-400 mt-1">*Emoji akan disimpan dalam kolom deskripsi.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Deskripsi Singkat</label>
                    <textarea name="deskripsi" rows="3" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" placeholder="Jelaskan jenis barang dalam kategori ini..."></textarea>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-gray-900 text-white py-4 rounded-2xl shadow-lg hover:bg-gray-800 transition font-bold">
                        Simpan Kategori
                    </button>
                    <a href="../../views/admin/kategori.php" class="block text-center mt-4 text-sm text-gray-500 hover:text-gray-800 transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>