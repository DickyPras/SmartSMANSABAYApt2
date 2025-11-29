<?php
// --- [1] DATA DUMMY KATEGORI ---
// Nanti diganti query: SELECT * FROM kategori ...
// field 'jumlah_produk' biasanya didapat dari query COUNT() relasi ke tabel produk
$data_kategori = [
    [
        "id" => 1,
        "nama" => "Makanan Ringan",
        "jumlah_produk" => 45,
        "icon" => "🍔", 
        "warna" => "bg-orange-100 text-orange-600"
    ],
    [
        "id" => 2,
        "nama" => "Minuman Dingin",
        "jumlah_produk" => 23,
        "icon" => "🥤",
        "warna" => "bg-blue-100 text-blue-600"
    ],
    [
        "id" => 3,
        "nama" => "Alat Tulis (ATK)",
        "jumlah_produk" => 150,
        "icon" => "✏️",
        "warna" => "bg-purple-100 text-purple-600"
    ],
    [
        "id" => 4,
        "nama" => "Seragam & Atribut",
        "jumlah_produk" => 12,
        "icon" => "👕",
        "warna" => "bg-pink-100 text-pink-600"
    ],
    [
        "id" => 5,
        "nama" => "Jasa Print/Fotokopi",
        "jumlah_produk" => 5,
        "icon" => "🖨️",
        "warna" => "bg-gray-100 text-gray-600"
    ],
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kategori</title>
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
                <a href="dasboard.php" class="bg-white/20 p-2 rounded-xl hover:bg-white/40 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-xl font-bold text-gray-900">Kategori Produk</h1>
                <a href="../../process/admin/tambah_kategori.php" class="bg-gray-900 text-white p-2 rounded-xl shadow-lg hover:scale-105 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </a>
            </div>

            <div class="bg-white/30 p-4 rounded-2xl backdrop-blur-sm border border-white/20">
                <p class="text-sm font-medium text-gray-900">Total Kategori</p>
                <h2 class="text-3xl font-bold text-gray-900"><?= count($data_kategori) ?> <span class="text-sm font-normal text-gray-700">Jenis</span></h2>
            </div>
        </div>

        <div class="flex-1 px-6 pt-6 pb-20 overflow-y-auto">
            
            <div class="grid grid-cols-2 gap-4">
                <?php foreach($data_kategori as $row): ?>
                
                <div class="bg-white p-4 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition group h-40 relative overflow-hidden">
                    
                    <div class="absolute -right-4 -top-4 w-20 h-20 bg-gray-50 rounded-full group-hover:bg-yellow-50 transition"></div>

                    <div class="flex justify-between items-start z-10">
                        <div class="w-10 h-10 rounded-full <?= $row['warna'] ?> flex items-center justify-center text-xl shadow-inner">
                            <?= $row['icon'] ?>
                        </div>
                        
                        <div class="relative">
                            <button class="text-gray-400 hover:text-gray-600 p-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                </svg>
                            </button>
                            </div>
                    </div>

                    <div class="z-10 mt-2">
                        <h3 class="font-bold text-gray-800 text-md leading-tight mb-1"><?= $row['nama'] ?></h3>
                        <p class="text-xs text-gray-400"><?= $row['jumlah_produk'] ?> Produk</p>
                    </div>

                    <div class="absolute bottom-0 right-0 p-2 opacity-0 group-hover:opacity-100 transition-opacity flex gap-1">
                        <a href="../../process/admin/edit_kategori.php?id=<?= $row['id'] ?>" class="bg-yellow-100 p-1.5 rounded-lg text-yellow-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></a>
                        <a href="../../process/admin/hapus_kategori.php?id=<?= $row['id'] ?>" class="bg-red-100 p-1.5 rounded-lg text-red-600" onclick="return confirm('Hapus?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></a>
                    </div>

                </div>
                <?php endforeach; ?>

                <a href="../../process/admin/tambah_kategori.php" class="border-2 border-dashed border-gray-300 rounded-3xl flex flex-col items-center justify-center text-gray-400 hover:border-yellow-400 hover:text-yellow-500 hover:bg-yellow-50 transition h-40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="text-xs font-semibold">Tambah Baru</span>
                </a>
            </div>
            
        </div>
    </div>
</body>
</html>