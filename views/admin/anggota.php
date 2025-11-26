<?php
// query: mysqli_query($koneksi, "SELECT * FROM anggota");
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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Anggota</title>
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
                <a href="dasboard.php" class="bg-white/20 p-2 rounded-xl hover:bg-white/40 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-xl font-bold text-gray-900">Data Anggota</h1>
                <div class="w-10"></div>
            </div>

            <div class="mt-6 flex gap-3">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" placeholder="Cari Nama / NIS..." class="w-full py-3 pl-10 pr-4 rounded-2xl border-none focus:ring-2 focus:ring-yellow-600 shadow-sm text-sm">
                </div>
                
                <a href="../../process/admin/tambah_anggota.php" class="bg-gray-900 text-white p-3 rounded-2xl shadow-lg hover:bg-gray-800 transition flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </a>
            </div>
        </div>

        <div class="flex-1 px-6 pt-4 pb-20 overflow-y-auto space-y-4">
            
            <?php foreach($data_anggota as $row): ?>
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between group hover:border-yellow-400 transition">
                
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-700 font-bold text-lg">
                        <?= substr($row['nama'], 0, 1) ?>
                    </div>
                    
                    <div>
                        <h3 class="font-bold text-gray-800"><?= $row['nama'] ?></h3>
                        <p class="text-xs text-gray-500">NIS: <?= $row['nis'] ?> • <?= $row['kelas'] ?></p>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-medium <?= $row['status'] == 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                            <?= $row['status'] ?>
                        </span>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <a href="../../process/admin/edit_anggota.php?id=<?= $row['id'] ?>" class="text-gray-400 hover:text-blue-500 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>
                    <a href="../../process/admin/hapus_anggota.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus siswa ini?')" class="text-gray-400 hover:text-red-500 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if(empty($data_anggota)): ?>
            <div class="text-center py-10 opacity-50">
                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" class="w-20 mx-auto mb-3" alt="Empty">
                <p>Belum ada data anggota.</p>
            </div>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>