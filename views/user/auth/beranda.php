<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Animasi fade masuk */
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }
    </style>
</head>
<body class="bg-white h-screen flex flex-col justify-between items-center">

    <!-- Spacer Atas -->
    <div></div>

    <!-- Teks -->
    <div class="text-center fade-in-up">
        <h1 class="text-xl font-bold text-black">
            Selamat Datang <?= $_SESSION['nama']; ?>!
        </h1>
        <p class="text-gray-500 mt-1 text-sm">
            Akun anda sudah terdaftar
        </p>
    </div>

    <!-- Tombol -->
    <div class="w-full flex justify-center mb-10 fade-in-up">
        <a href="dashboard.php"
           class="px-6 py-2 rounded-full text-white font-semibold
                  bg-gradient-to-r from-pink-500 to-purple-500
                  shadow-lg shadow-purple-300 hover:opacity-90 transition">
            Lanjut
        </a>
    </div>

</body>
</html>
