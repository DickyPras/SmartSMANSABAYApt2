<?php
// --- [1] BAGIAN BACKEND (PHP NATIVE) ---
// Di sini kita simpan data dummy (ceritanya ambil dari database)
$adminName = "Raden Fahri";
$jabatan = "ADMINISTRATOR";
$totalPendapatan = "Rp.7,783.00";

// Array Menu agar kodingan HTML lebih rapi (Looping)
$menus = [
    [
        "judul" => "Manajemen anggota",
        "icon" => "M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z",
        "link" => "anggota.php"
    ],
    [
        "judul" => "Manajemen produk",
        "icon" => "M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4",
        "link" => "produk.php"
    ],
    [
        "judul" => "Manajemen kategori",
        "icon" => "M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01",
        "link" => "kategori.php"
    ],
    [
        "judul" => "Manajemen Transaksi",
        "icon" => "M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4",
        "link" => "transaksi.php"
    ],
    [
        "judul" => "Laporan Keuangan",
        "icon" => "M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z",
        "link" => "laporan.php"
    ]
];

//include '../confiq/koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin Koperasi</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        'primary': '#FACC15',    /* Kuning Header */
                        'card-bg': '#FBBF24',    /* Kuning Kartu Profil */
                        'body-bg': '#F0FDF4',    /* Hijau Mint Muda (Background Bawah) */
                        'btn-green': '#DCFCE7',  /* Hijau Tombol Menu */
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-200">

    <div class="max-w-md mx-auto bg-primary min-h-screen relative shadow-2xl overflow-hidden">
        
        <div class="pt-10 px-6 pb-24">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-lg font-bold text-gray-900">Hi, Welcome Back</h1>
                <div class="flex space-x-1">
                    <div class="w-4 h-4 bg-white/20 rounded-full"></div>
                </div>
            </div>

            <div class="text-center mt-2">
                <p class="text-xs text-gray-800 mb-1 flex justify-center items-center gap-1 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    Total Pendapatan
                </p>
                <h2 class="text-3xl font-extrabold text-white drop-shadow-sm"><?= $totalPendapatan ?></h2>
            </div>
        </div>

        <div class="bg-body-bg rounded-t-[3rem] min-h-[70vh] px-6 pt-16 relative -mt-10">
            
            <div class="absolute -top-16 left-6 right-6 bg-card-bg rounded-3xl p-5 shadow-lg flex items-center">
                
                <div class="w-1/3 flex justify-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full border-2 border-white/40 overflow-hidden shadow-sm">
                        <img src="https://img.freepik.com/free-vector/businessman-character-avatar-isolated_24877-60111.jpg" alt="Profile" class="w-full h-full object-cover">
                    </div>
                </div>

                <div class="w-[1px] h-12 bg-white/40 mx-2"></div>

                <div class="w-2/3 pl-1">
                    <p class="text-[10px] text-gray-800 uppercase tracking-wider mb-0.5">Jabatan</p>
                    <h3 class="text-lg font-black text-gray-900 leading-none mb-2"><?= $jabatan ?></h3>
                    
                    <div class="w-10 h-[2px] bg-white mb-2"></div> <p class="text-[10px] text-gray-800 mb-0.5">Name</p>
                    <h4 class="text-sm font-bold text-gray-900"><?= $adminName ?></h4>
                </div>
            </div>

            <div class="space-y-3 mt-4 pb-10">
                <?php foreach($menus as $menu): ?>
                
                <a href="<?= $menu['link'] ?>" class="flex items-center bg-btn-green px-4 py-4 rounded-2xl shadow-sm hover:shadow-md hover:bg-green-200 transition-all active:scale-95 group">
                    <div class="w-10 flex justify-center text-gray-700 group-hover:text-black">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="<?= $menu['icon'] ?>" />
                        </svg>
                    </div>
                    <span class="ml-3 font-bold text-gray-800 text-sm tracking-wide"><?= $menu['judul'] ?></span>
                </a>

                <?php endforeach; ?>
            </div>

            <div class="pt-2 pb-10">
                <a href="../logout.php" class="w-full inline-flex items-center justify-center gap-2 bg-white text-gray-900 font-semibold px-4 py-3 rounded-2xl shadow-sm hover:bg-gray-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 11-4 0v-1m0-8V7a2 2 0 114 0v1" />
                    </svg>
                    Logout
                </a>
            </div>

        </div>

    </div>

</body>
</html>