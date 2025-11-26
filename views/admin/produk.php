<?php
// Nanti diganti query: SELECT * FROM produk JOIN kategori ...
$data_produk = [
    [
        "id" => 1,
        "nama" => "Teh Pucuk Harum 350ml",
        "kategori" => "Minuman",
        "harga" => 4000,
        "stok" => 24,
        "gambar" => "https://assets.klikindomaret.com/products/20042075/20042075_1.jpg" 
    ],
    [
        "id" => 2,
        "nama" => "Roti Oksis Coklat",
        "kategori" => "Makanan",
        "harga" => 2500,
        "stok" => 5, // Stok Menipis
        "gambar" => "https://images.tokopedia.net/img/cache/700/VqbcmM/2022/6/15/8d10350d-6e83-490b-ba65-8cc080214c77.jpg"
    ],
    [
        "id" => 3,
        "nama" => "Pulpen Standard AE7",
        "kategori" => "Alat Tulis",
        "harga" => 3000,
        "stok" => 0, // Habis
        "gambar" => "https://down-id.img.susercontent.com/file/id-11134207-7r98o-lsmg3e6z63n2eb"
    ],
    [
        "id" => 4,
        "nama" => "Buku Tulis Sidu 38",
        "kategori" => "Alat Tulis",
        "harga" => 5000,
        "stok" => 50,
        "gambar" => "https://static.bmdstatic.com/pk/product/medium/5c6b6d5186088.jpg"
    ]
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk</title>
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
        
        <div class="bg-primary px-6 pt-8 pb-6 rounded-b-[2rem] shadow-sm z-10 sticky top-0">
            <div class="flex items-center justify-between mb-4">
                <a href="dasboard.php" class="bg-white/20 p-2 rounded-xl hover:bg-white/40 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-xl font-bold text-gray-900">List Produk</h1>
                <a href="../../process/admin/tambah_produk.php" class="bg-gray-900 text-white p-2 rounded-xl shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </a>
            </div>

            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" placeholder="Cari produk..." class="w-full py-3 pl-10 pr-4 rounded-2xl border-none focus:ring-2 focus:ring-yellow-600 shadow-sm text-sm">
            </div>

            <div class="flex gap-2 mt-4 overflow-x-auto pb-2 no-scrollbar">
                <button class="bg-gray-900 text-white px-4 py-1.5 rounded-full text-xs font-medium whitespace-nowrap">Semua</button>
                <button class="bg-white/40 hover:bg-white text-gray-800 px-4 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition">Makanan</button>
                <button class="bg-white/40 hover:bg-white text-gray-800 px-4 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition">Minuman</button>
                <button class="bg-white/40 hover:bg-white text-gray-800 px-4 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition">Alat Tulis</button>
            </div>
        </div>

        <div class="flex-1 px-5 pt-4 pb-20 overflow-y-auto space-y-4">
            
            <?php foreach($data_produk as $row): ?>
            
            <?php 
                $stokColor = 'bg-green-100 text-green-700'; // Aman
                if($row['stok'] == 0) $stokColor = 'bg-red-100 text-red-700'; // Habis
                else if($row['stok'] <= 5) $stokColor = 'bg-orange-100 text-orange-700'; // Kritis
            ?>

            <div class="bg-white p-3 rounded-2xl shadow-sm border border-gray-100 flex gap-3 items-center group">
                
                <div class="w-20 h-20 flex-shrink-0 bg-gray-100 rounded-xl overflow-hidden">
                    <img src="<?= $row['gambar'] ?>" alt="<?= $row['nama'] ?>" class="w-full h-full object-cover">
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-[10px] text-gray-400 uppercase font-medium tracking-wide"><?= $row['kategori'] ?></p>
                    <h3 class="text-sm font-bold text-gray-800 truncate leading-tight mb-1"><?= $row['nama'] ?></h3>
                    <p class="text-green-600 font-bold text-sm">Rp <?= number_format($row['harga'], 0, ',', '.') ?></p>
                    
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-[10px] px-2 py-0.5 rounded-md font-bold <?= $stokColor ?>">
                            Stok: <?= $row['stok'] ?>
                        </span>
                    </div>
                </div>

                <div class="flex flex-col gap-2 pl-2 border-l border-gray-100">
                    <a href="../../process/admin/edit_produk.php?id=<?= $row['id'] ?>" class="p-1.5 rounded-lg text-gray-400 hover:bg-yellow-50 hover:text-yellow-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </a>
                    <a href="../../process/admin/hapus_produk.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus produk ini?')" class="p-1.5 rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="h-10"></div>
        </div>
    </div>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</body>
</html>