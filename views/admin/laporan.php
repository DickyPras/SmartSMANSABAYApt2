<?php
// --- [1] DATA DUMMY LAPORAN (Simulasi Database) ---
// Ceritanya ini data penjualan 7 hari terakhir data sementara 
$data_harian = [
    ["tanggal" => "20 Okt", "pendapatan" => 150000, "transaksi" => 15],
    ["tanggal" => "21 Okt", "pendapatan" => 230000, "transaksi" => 20],
    ["tanggal" => "22 Okt", "pendapatan" => 180000, "transaksi" => 18],
    ["tanggal" => "23 Okt", "pendapatan" => 350000, "transaksi" => 35],
    ["tanggal" => "24 Okt", "pendapatan" => 210000, "transaksi" => 22],
    ["tanggal" => "25 Okt", "pendapatan" => 420000, "transaksi" => 40],
    ["tanggal" => "26 Okt", "pendapatan" => 310000, "transaksi" => 28],
];

// Hitung Total untuk Card Ringkasan
$total_omzet = 0;
$total_transaksi = 0;
$total_laba = 0; // Asumsi margin keuntungan 20%

// Siapkan Array untuk Chart.js (PHP to JS)
$chart_labels = [];
$chart_data = [];

foreach($data_harian as $row) {
    $total_omzet += $row['pendapatan'];
    $total_transaksi += $row['transaksi'];
    $total_laba += ($row['pendapatan'] * 0.2); // Simulasi laba 20%
    
    // Masukkan ke array chart
    $chart_labels[] = $row['tanggal'];
    $chart_data[] = $row['pendapatan'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <a href="dasboard.php" class="bg-white/20 p-2 rounded-xl hover:bg-white/40 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-xl font-bold text-gray-900">Laporan Keuangan</h1>
                
                <button onclick="window.print()" class="bg-gray-900 text-white p-2 rounded-xl shadow-lg hover:bg-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                </button>
            </div>

            <div class="flex justify-center mb-2">
                <div class="bg-white/30 backdrop-blur-sm rounded-full px-4 py-1 flex items-center gap-2 cursor-pointer hover:bg-white/50 transition">
                    <span class="text-sm font-semibold text-gray-900">Oktober 2025</span>
                    <svg class="w-4 h-4 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
        </div>

        <div class="flex-1 px-6 -mt-8 pb-10 overflow-y-auto space-y-6">
            
            <div class="grid grid-cols-2 gap-3">
                <div class="col-span-2 bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-400 font-medium uppercase">Total Pendapatan</p>
                        <h2 class="text-2xl font-bold text-gray-800 mt-1">Rp <?= number_format($total_omzet, 0, ',', '.') ?></h2>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 font-medium uppercase">Laba Bersih</p>
                    <h3 class="text-lg font-bold text-blue-600 mt-1">Rp <?= number_format($total_laba, 0, ',', '.') ?></h3>
                </div>

                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 font-medium uppercase">Transaksi</p>
                    <h3 class="text-lg font-bold text-orange-600 mt-1"><?= $total_transaksi ?> <span class="text-xs font-normal text-gray-400 text-black">Nota</span></h3>
                </div>
            </div>

            <div class="bg-white p-4 rounded-3xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-gray-800">Grafik Penjualan</h3>
                    <span class="text-[10px] bg-gray-100 px-2 py-1 rounded text-gray-500">7 Hari Terakhir</span>
                </div>
                <div class="h-48 w-full">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <div>
                <h3 class="font-bold text-gray-800 mb-3 px-1">Rincian Harian</h3>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3 text-right">Omzet</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach(array_reverse($data_harian) as $row): ?>
                            <tr>
                                <td class="px-4 py-3 text-gray-700"><?= $row['tanggal'] ?></td>
                                <td class="px-4 py-3 text-right font-bold text-gray-900">Rp <?= number_format($row['pendapatan'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Mengambil data dari PHP ke JavaScript
        const labels = <?= json_encode($chart_labels) ?>;
        const dataValues = <?= json_encode($chart_data) ?>;

        const ctx = document.getElementById('salesChart').getContext('2d');
        
        // Membuat Gradient warna kuning pudar
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(250, 204, 21, 0.5)'); // Kuning Atas
        gradient.addColorStop(1, 'rgba(250, 204, 21, 0.0)'); // Transparan Bawah

        const myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Omzet',
                    data: dataValues,
                    backgroundColor: gradient,
                    borderColor: '#EAB308', // Yellow-600
                    borderWidth: 2,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#EAB308',
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4 // Membuat garis melengkung (curved)
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false } // Sembunyikan label legend di atas
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    },
                    y: {
                        border: { display: false },
                        grid: { color: '#f3f4f6' }, // Garis grid tipis
                        ticks: { display: false } // Sembunyikan angka di sumbu Y agar bersih
                    }
                }
            }
        });
    </script>
</body>
</html>