<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Produk</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Animasi fade */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 0.8s ease-in-out forwards;
        }
    </style>
</head>

<body class="bg-gray-100">

    <!-- NOTIFIKASI SELAMAT DATANG -->
    <div id="welcomeBox" class="text-center p-4">
        <h2 class="text-xl font-semibold fade-in" id="welcomeText"></h2>
        <p class="fade-in delay-[300ms]" id="subText"></p>
    </div>

    <!-- Container -->
    <div class="max-w-md mx-auto p-4">

        <!-- Foto Produk -->
        <div class="bg-white rounded-xl p-3 shadow-md mb-4">
            <img src="https://via.placeholder.com/300"
                 class="rounded-lg w-full object-cover">
        </div>

        <!-- Nama Produk -->
        <h2 class="text-center text-gray-800 font-semibold mt-2">
            Keset lembut dari kain perca
        </h2>

        <!-- Nama Penjual -->
        <div class="flex justify-center mt-2">
            <span class="bg-yellow-400 text-white text-sm px-3 py-1 rounded-full">
                Seinal Arlfim XII MIPA 2
            </span>
        </div>

        <!-- Counter Jumlah -->
        <div class="flex justify-center items-center mt-3 gap-3">
            <button class="px-3 py-1 bg-gray-200 rounded">-</button>
            <span class="font-semibold text-lg">1</span>
            <button class="px-3 py-1 bg-gray-200 rounded">+</button>
        </div>

        <!-- Harga -->
        <h3 class="text-center text-yellow-500 font-bold text-2xl mt-3">
            Rp 25.000
        </h3>

        <!-- Tombol Beli -->
        <button class="w-full bg-yellow-400 text-white py-3 rounded-full mt-4 text-lg font-semibold">
            Beli
        </button>

        <!-- Tambahkan ke Keranjang -->
        <button class="w-full bg-yellow-500 text-white py-3 rounded-full mt-3 text-lg font-semibold">
            Tambahkan ke Keranjang
        </button>

    </div>

    <!-- Bottom Navbar -->
    <div class="fixed bottom-0 left-0 right-0 bg-white shadow-md py-3 flex justify-around text-gray-400">
        <button class="flex flex-col items-center">
            <span>🏠</span>
            <small>Home</small>
        </button>

        <button class="flex flex-col items-center text-yellow-500">
            <span>🔍</span>
            <small>Search</small>
        </button>

        <button class="flex flex-col items-center">
            <span>🛒</span>
            <small>Cart</small>
        </button>

        <button class="flex flex-col items-center">
            <span>⚙️</span>
            <small>Setting</small>
        </button>
    </div>

    <!-- Script ganti nama user -->
    <script>
        const userName = "Seinal"; // ← ganti dengan nama user dari login

        document.getElementById("welcomeText").innerText = "Selamat datang " + userName;
        document.getElementById("subText").innerText = "Akun anda telah terdaftar";
    </script>

</body>
</html>
