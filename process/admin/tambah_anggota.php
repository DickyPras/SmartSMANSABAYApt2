<?php
require_once '../../config/koneksi.php';

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil dan membersihkan input
    $nama     = trim(mysqli_real_escape_string($koneksi, $_POST['nama'] ?? ''));
    $nis      = trim(mysqli_real_escape_string($koneksi, $_POST['nis'] ?? ''));
    $kelas    = trim(mysqli_real_escape_string($koneksi, $_POST['kelas'] ?? ''));
    $email    = trim(mysqli_real_escape_string($koneksi, $_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    // Validasi input wajib
    if ($nama !== '' && $email !== '' && $password !== '' && $nis !== '') {
        
        // Cek apakah NIS atau Email sudah terdaftar (karena bersifat UNIQUE di database)
        $cek_query = "SELECT id_user FROM users WHERE nis = '$nis' OR email = '$email'";
        $cek_result = mysqli_query($koneksi, $cek_query);

        if (mysqli_num_rows($cek_result) > 0) {
            $error = 'NIS atau Email sudah terdaftar!';
        } else {
            // Hash password untuk keamanan
            $hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Query Insert sesuai struktur tabel users
            // Role otomatis 'siswa' sesuai kebutuhan manajemen anggota
            $sql = "INSERT INTO users (nis, nama, email, password, role, kelas) 
                    VALUES ('$nis', '$nama', '$email', '$hash', 'siswa', '$kelas')";

            if (mysqli_query($koneksi, $sql)) {
                // Redirect ke halaman daftar anggota dengan pesan sukses
                header('Location: ../../views/admin/anggota.php?success=1');
                exit;
            } else {
                $error = 'Gagal menyimpan data: ' . mysqli_error($koneksi);
            }
        }
    } else {
        $error = 'Semua kolom wajib diisi termasuk Email dan Password.';
    }
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
            <p class="mt-2 text-sm text-gray-700">Tambahkan data siswa ke sistem E-Smart.</p>
        </div>

        <div class="flex-1 px-6 pt-6 pb-12 space-y-6 overflow-y-auto">
            <?php if ($error !== ''): ?>
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded-2xl shadow-sm text-sm">
                <?= $error ?>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1 font-medium">Nama Lengkap</label>
                    <input type="text" name="nama" required class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" placeholder="Nama lengkap siswa">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1 font-medium">NIS</label>
                        <input type="text" name="nis" required class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" placeholder="NIS">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1 font-medium">Kelas</label>
                        <input type="text" name="kelas" required class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" placeholder="Contoh: XII RPL 1">
                    </div>
                </div>

                <hr class="border-gray-200 my-2">

                <div>
                    <label class="block text-sm text-gray-600 mb-1 font-medium">Email (untuk login)</label>
                    <input type="email" name="email" required class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" placeholder="email@siswa.com">
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1 font-medium">Password</label>
                    <input type="password" name="password" required class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white" placeholder="Min. 6 karakter">
                </div>

                <button type="submit" class="w-full bg-gray-900 text-white py-4 rounded-2xl shadow-lg hover:bg-gray-800 transition font-bold mt-4">
                    Simpan Anggota
                </button>
            </form>
        </div>
    </div>
</body>
</html>