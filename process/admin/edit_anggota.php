<?php
require_once '../../config/koneksi.php';

// Pastikan ID valid dari parameter URL
$id = (int) ($_GET['id'] ?? 0);
$selected = null;
$error = '';

if ($id > 0) {
    // Mengambil data user berdasarkan id_user sesuai database
    $res = mysqli_query($koneksi, "SELECT * FROM users WHERE id_user = " . $id . " LIMIT 1");
    if ($res && mysqli_num_rows($res) > 0) {
        $selected = mysqli_fetch_assoc($res);
    }
}

// Proteksi jika data tidak ditemukan
if (!$selected) {
    header('Location: ../../views/admin/anggota.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitasi input sesuai kolom tabel users
    $nama = trim(mysqli_real_escape_string($koneksi, $_POST['nama'] ?? ''));
    $nis = trim(mysqli_real_escape_string($koneksi, $_POST['nis'] ?? ''));
    $kelas = trim(mysqli_real_escape_string($koneksi, $_POST['kelas'] ?? ''));
    $email = trim(mysqli_real_escape_string($koneksi, $_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if ($nama !== '' && $email !== '' && $nis !== '') {
        $parts = [];
        $parts[] = "nama = '" . $nama . "'";
        $parts[] = "nis = '" . $nis . "'";
        $parts[] = "kelas = '" . $kelas . "'";
        $parts[] = "email = '" . $email . "'";
        
        // Update password hanya jika diisi oleh admin
        if ($password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $parts[] = "password = '" . $hash . "'";
        }

        $sql = "UPDATE users SET " . implode(', ', $parts) . " WHERE id_user = " . $id;
        
        if (mysqli_query($koneksi, $sql)) {
            // Redirect dengan notifikasi sukses
            header('Location: ../../views/admin/anggota.php?updated=1');
            exit;
        } else {
            $error = 'Gagal memperbarui data: ' . mysqli_error($koneksi);
        }
    } else {
        $error = 'Nama, NIS, dan Email wajib diisi.';
    }
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
            <p class="mt-2 text-sm text-gray-700">Perbarui informasi profil siswa di sistem.</p>
        </div>

        <div class="flex-1 px-6 pt-6 pb-12 space-y-6 overflow-y-auto">
            <?php if ($error !== ''): ?>
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded-2xl shadow-sm text-xs">
                <?= $error ?>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1 font-medium">Nama Lengkap</label>
                    <input type="text" name="nama" required value="<?= htmlspecialchars($selected['nama']) ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1 font-medium">NIS</label>
                        <input type="text" name="nis" required value="<?= htmlspecialchars($selected['nis']) ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1 font-medium">Kelas</label>
                        <input type="text" name="kelas" required value="<?= htmlspecialchars($selected['kelas']) ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                    </div>
                </div>

                <hr class="border-gray-200">

                <div>
                    <label class="block text-sm text-gray-600 mb-1 font-medium">Email</label>
                    <input type="email" name="email" required value="<?= htmlspecialchars($selected['email']) ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1 font-medium">Ganti Password</label>
                    <input type="password" name="password" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" placeholder="Kosongkan jika tidak ingin diubah">
                    <p class="text-[10px] text-gray-400 mt-1">*Admin bisa mereset password siswa di sini.</p>
                </div>

                <button type="submit" class="w-full bg-gray-900 text-white py-4 rounded-2xl shadow-lg hover:bg-gray-800 transition font-bold mt-4">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</body>
</html>