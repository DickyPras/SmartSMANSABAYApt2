<?php
// Tentukan status pesanan dari URL. Default: Diterima (sesuai gambar)
$status = $_GET['status'] ?? 'diterima';

// URL gambar produk untuk setiap status
$image_urls=[
    'dikemas'=>'',
    'dikirim'=>'',
    'diterima'=>'',
    'dibatalkan'=>'',
];
// Data simulasi pesanan berdasarkan status
// Tambahkan path gambar produk yang berbeda untuk visual yang lebih baik
$pesanan_data = [
    'dikemas' => [
        ['nama' => 'Buku Catatan A5', 'harga' => 'Rp 15.000', 'qty' => 2, 'keterangan' => 'Menunggu pengambilan kurir', 'tag_warna' => 'bg-blue-600 text-white', 'ikon' => 'fa-box', 'image' => $image_urls['dikemas']],
    ],
    'dikirim' => [
        ['nama' => 'Pensil Mekanik', 'harga' => 'Rp 20.000', 'qty' => 1, 'keterangan' => 'Pesanan sedang dalam perjalanan', 'tag_warna' => 'bg-indigo-600 text-white', 'ikon' => 'fa-truck', 'image' => $image_urls['dikirim']],
    ],
    'diterima' => [
        ['nama' => 'Kaset lembut dari kain perca', 'harga' => 'Rp 35.000', 'qty' => 1, 'keterangan' => 'Pesanan telah diterima', 'tag_warna' => 'bg-primary-green text-white', 'ikon' => 'fa-check-circle', 'image' => $image_urls['diterima']],
    ],
    'dibatalkan' => [
        ['nama' => 'Penggaris Set', 'harga' => 'Rp 12.000', 'qty' => 3, 'keterangan' => 'Pesanan dibatalkan oleh pembeli', 'tag_warna' => 'bg-red-600 text-white', 'ikon' => 'fa-times-circle', 'image' => $image_urls['dibatalkan']],
    ],
];

// Ambil data yang sesuai
$items = $pesanan_data[$status] ?? [];
$active_tag_color = $items[0]['tag_warna'] ?? 'bg-yellow-500 text-white'; // Ambil warna tag untuk header

// Fungsi untuk membuat link tab
function get_tab_class($current_status, $target_status) {
    if ($current_status === $target_status) {
        // Gaya untuk tab yang sedang aktif
        return 'font-semibold text-white bg-primary-green shadow-lg';
    } else {
        // Gaya untuk tab yang tidak aktif
        return 'text-gray-600 hover:bg-gray-100';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - Status <?php echo ucfirst($status); ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-green': '#00A859', // Hijau Kustom
                        'light-bg': '#F8F9FD',
                    },
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        body { font-family: 'Poppins', sans-serif; }

        /* Animasi Card (sama seperti kode profil) */
        @keyframes slideUpFade {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-card-item { 
            animation: slideUpFade 0.6s ease-out forwards; 
            opacity: 0; 
            animation-fill-mode: forwards;
        }

        /* Shadow pada tab aktif */
        .tab-active-style {
            transition: all 0.3s ease;
            transform: scale(1.03);
        }
    </style>
</head>
<body class="bg-light-bg min-h-screen">

    <div class="max-w-md mx-auto bg-white min-h-screen relative shadow-lg">
        
        <div class="h-40 <?php echo $active_tag_color; ?> rounded-b-3xl absolute top-0 left-0 w-full z-0"></div>

        <header class="p-4 pt-8 flex items-center justify-between relative z-10 text-white">
            <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 transition-colors">
                <i class="fas fa-arrow-left text-lg"></i>
            </a> 
            <h1 class="text-xl font-bold drop-shadow-md">Pesanan Saya</h1>
            <div class="w-10 h-10"></div> </header>

        <nav class="flex justify-between p-2 mx-4 rounded-xl shadow-lg bg-white relative z-10 mt-4">
            <a href="?status=dikemas" class="flex-1 text-center py-2 rounded-xl text-sm transition <?php echo get_tab_class($status, 'dikemas'); ?> <?php echo ($status === 'dikemas' ? 'tab-active-style' : ''); ?>">Dikemas</a>
            <a href="?status=dikirim" class="flex-1 text-center py-2 rounded-xl text-sm transition <?php echo get_tab_class($status, 'dikirim'); ?> <?php echo ($status === 'dikirim' ? 'tab-active-style' : ''); ?>">Dikirim</a>
            <a href="?status=diterima" class="flex-1 text-center py-2 rounded-xl text-sm transition <?php echo get_tab_class($status, 'diterima'); ?> <?php echo ($status === 'diterima' ? 'tab-active-style' : ''); ?>">Diterima</a>
            <a href="?status=dibatalkan" class="flex-1 text-center py-2 rounded-xl text-sm transition <?php echo get_tab_class($status, 'dibatalkan'); ?> <?php echo ($status === 'dibatalkan' ? 'tab-active-style' : ''); ?>">Dibatalkan</a>
        </nav>

        <main class="p-4 space-y-4 pt-6">
            
            <?php if (empty($items)): ?>
                <div class="text-center text-gray-500 py-10 bg-white rounded-xl shadow-md border border-gray-100">
                    <i class="fas fa-box-open text-4xl mb-4 text-gray-300"></i>
                    <p class="font-semibold">Tidak ada pesanan dengan status **<?php echo ucfirst($status); ?>**.</p>
                    <p class="text-xs text-gray-400 mt-1">Cek tab lainnya atau mulai berbelanja.</p>
                </div>
            <?php else: ?>
                
                <?php $delay = 100; // Inisialisasi delay animasi ?>
                <?php foreach ($items as $item): ?>
                    <?php 
                        $current_delay = 'delay-' . $delay; 
                        $delay += 100;
                    ?>
                    <div class="bg-white rounded-3xl p-4 relative border border-gray-100 shadow-lg 
                                animate-card-item <?php echo $current_delay; ?>">
                        
                        <div class="flex justify-between items-center pb-3 mb-3 border-b border-gray-100">
                            <span class="text-xs font-bold px-3 py-1 rounded-full <?php echo $item['tag_warna']; ?>">
                                <i class="fas <?php echo $item['ikon']; ?> mr-1"></i> <?php echo strtoupper($status); ?>
                            </span>
                            <span class="text-xs text-gray-500">Order ID: #20251216</span>
                        </div>

                        <div class="flex space-x-4">
                            <div class="flex-shrink-0 w-20 h-20 rounded-xl overflow-hidden shadow-md">
                                <img src="<?php echo $item['image']; ?>" alt="Produk" class="w-full h-full object-cover">
                            </div>

                            <div class="flex-grow">
                                <h2 class="text-sm font-bold text-gray-800 line-clamp-2"><?php echo $item['nama']; ?></h2>
                                <p class="text-xs text-primary-green font-medium mt-1">x<?php echo $item['qty']; ?></p>
                                
                                <p class="text-sm font-extrabold text-red-500 mt-2"><?php echo $item['harga']; ?></p>
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-3 border-t border-dashed border-gray-200 flex justify-between items-center">
                            <div class="text-xs text-gray-600 font-medium">
                                <i class="fas fa-info-circle mr-1 text-primary-green"></i> <?php echo $item['keterangan']; ?>
                            </div>
                            <button class="text-xs font-semibold px-3 py-1 rounded-lg bg-primary-green text-white shadow-md hover:bg-green-700 transition">
                                Detail
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php endif; ?>

        </main>

    </div>

</body>
</html>