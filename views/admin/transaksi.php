<?php
// --- [1] DATA DUMMY TRANSAKSI ---
// Dalam aplikasi nyata, ini diambil dari tabel `transaksi` JOIN `anggota`
$data_transaksi = [
    [
        "no_invoice" => "TRX-20231025-001",
        "anggota" => "Ahmad Dhani",
        "tanggal" => "2025-10-25 08:30:00",
        "total" => 15000,
        "items" => 3, // Jumlah barang yang dibeli
        "status" => "Lunas"
    ],
    [
        "no_invoice" => "TRX-20231025-002",
        "anggota" => "Maya Estianty",
        "tanggal" => "2025-10-25 09:15:00",
        "total" => 8500,
        "items" => 2,
        "status" => "Lunas"
    ],
    [
        "no_invoice" => "TRX-20231025-003",
        "anggota" => "Mulan Jameela",
        "tanggal" => "2025-10-25 10:00:00",
        "total" => 50000,
        "items" => 5,
        "status" => "Pending" // Belum bayar (misal: kasbon)
    ],
];

// Hitung Omzet Hari Ini (Simulasi)
$omzet_hari_ini = 0;
foreach($data_transaksi as $t) {
    if($t['status'] == 'Lunas') {
        $omzet_hari_ini += $t['total'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi</title>
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
        
        <div class="bg-primary px-6 pt-8 pb-16 rounded-b-[2.5rem] shadow-sm z-10 relative">
            <div class="flex items-center justify-between mb-6">
                <a href="dasboard.php" class="bg-white/20 p-2 rounded-xl hover:bg-white/40 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-xl font-bold text-gray-900">Riwayat Transaksi</h1>
                <div class="w-10"></div> </div>

            <div class="bg-white/30 backdrop-blur-md border border-white/40 p-4 rounded-2xl text-gray-900">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider opacity-70">Omzet Hari Ini</p>
                        <h2 class="text-2xl font-bold">Rp <?= number_format($omzet_hari_ini, 0, ',', '.') ?></h2>
                    </div>
                    <div class="bg-white/50 p-2 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-1 px-6 -mt-8 pb-24 overflow-y-auto z-20">
            
            <div class="space-y-4">
                <?php foreach($data_transaksi as $row): ?>
                
                <?php 
                    $statusClass = $row['status'] == 'Lunas' 
                        ? 'bg-green-100 text-green-700 border-green-200' 
                        : 'bg-yellow-100 text-yellow-700 border-yellow-200';
                    
                    $iconStatus = $row['status'] == 'Lunas'
                        ? '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'
                        : '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                ?>

                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-center gap-3">
                            <div class="bg-gray-100 p-2.5 rounded-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 text-sm"><?= $row['anggota'] ?></h3>
                                <p class="text-[10px] text-gray-400"><?= $row['no_invoice'] ?></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="block font-bold text-gray-900">Rp <?= number_format($row['total'], 0, ',', '.') ?></span>
                            <span class="text-[10px] text-gray-400"><?= date('H:i', strtotime($row['tanggal'])) ?> WIB</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between border-t border-gray-100 pt-3">
                        <span class="text-xs text-gray-500 font-medium"><?= $row['items'] ?> Barang</span>
                        
                        <div class="flex items-center gap-2">
                            <span class="flex items-center gap-1 text-[10px] font-bold px-2 py-1 rounded-lg border <?= $statusClass ?>">
                                <?= $iconStatus ?>
                                <?= $row['status'] ?>
                            </span>
                            <div class="flex gap-1">
                                <a href="../../process/admin/edit_transaksi.php?invoice=<?= urlencode($row['no_invoice']) ?>" class="bg-yellow-50 text-yellow-600 p-1.5 rounded-lg hover:bg-yellow-100 transition" title="Edit transaksi">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>
                                <a href="../../process/admin/hapus_transaksi.php?invoice=<?= urlencode($row['no_invoice']) ?>" class="bg-red-50 text-red-600 p-1.5 rounded-lg hover:bg-red-100 transition" title="Hapus transaksi" onclick="return confirm('Hapus transaksi ini?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="absolute bottom-6 left-0 right-0 px-6 z-30">
            <a href="../../process/admin/tambah_transaksi.php" class="bg-gray-900 text-white w-full py-4 rounded-2xl shadow-xl flex items-center justify-center gap-3 hover:bg-gray-800 transition active:scale-95 group">
                <div class="bg-white/20 p-1 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <span class="font-bold text-lg">Transaksi Baru (Kasir)</span>
            </a>
        </div>

    </div>
</body>
</html>