<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>

    <!-- Load Tailwind CSS dengan fallback -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Cek apakah Tailwind berhasil dimuat
        setTimeout(() => {
            if (!window.tailwind) {
                console.warn('Tailwind CSS failed to load from CDN');
            }
        }, 2000);
    </script>

    <style>
        .gradient-purple {
            background: linear-gradient(135deg, #a855f7 0%, #d946ef 100%);
        }
        .animate-bounce.delay-150 {
            animation-delay: 150ms;
        }
        
        /* Auto responsive untuk semua ukuran layar */
        .responsive-container {
            min-height: 100vh;
            min-height: 100dvh; /* Dynamic viewport height untuk mobile */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 1rem;
        }
        
        /* Breakpoints untuk berbagai ukuran HP */
        @media (max-width: 320px) {
            /* HP kecil (iPhone SE, dll) */
            .responsive-container {
                padding: 0.5rem;
            }
        }
        
        @media (min-width: 321px) and (max-width: 480px) {
            /* HP sedang */
            .responsive-container {
                padding: 1rem;
            }
        }
        
        @media (min-width: 481px) and (max-width: 768px) {
            /* HP besar / tablet kecil */
            .responsive-container {
                padding: 1.5rem;
            }
        }
        
        @media (min-width: 769px) {
            /* Desktop */
            .responsive-container {
                padding: 2rem;
            }
        }
        
        /* Untuk HP dengan aspect ratio tinggi */
        @media (max-aspect-ratio: 9/16) {
            .responsive-container {
                justify-content: flex-start;
                padding-top: 15vh;
            }
        }
    </style>    

</head>

<body class="bg-white">
    <?php include_once '../components/colaborator.php'; ?>
    
    <div class="responsive-container">
        <div class="w-full max-w-sm sm:max-w-md">
            <?php include_once '../components/animation-area.php'; ?>

            <!-- Login Form -->
            <form action="login_process.php" method="POST" class="space-y-4 mt-8">
                <!-- CSRF Protection (you'll need to implement this) -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                
                <!-- Email Input -->
                <div>
                    <input 
                        type="email" 
                        name="email" 
                        placeholder="Email" 
                        class="w-full px-4 py-3 bg-gray-100 rounded-xl border-0 focus:outline-none focus:ring-2 focus:ring-purple-500 text-gray-700 text-sm sm:text-base"
                        required
                    >
                </div>

                <!-- Password Input -->
                <div>
                    <input 
                        type="password" 
                        name="password" 
                        placeholder="Password" 
                        class="w-full px-4 py-3 bg-gray-100 rounded-xl border-0 focus:outline-none focus:ring-2 focus:ring-purple-500 text-gray-700 text-sm sm:text-base"
                        required
                    >
                </div>

                <!-- Forgot Password & Reset Link -->
                <div class="text-center space-y-1">
                    <p class="text-gray-400 text-xs sm:text-sm">atau</p>
                    <a href="password_reset.php" class="text-blue-500 text-xs sm:text-sm">Reset akun lain?</a>
                </div>

                <!-- Login Button -->
                <button 
                    type="submit" 
                    class="w-full gradient-purple text-white font-semibold py-3 rounded-full hover:opacity-90 transition-opacity mt-6 text-sm sm:text-base"
                >
                    Masuk
                </button>

                <!-- Register Link -->
                <button 
                    type="button"
                    onclick="window.location.href='register.php'"
                    class="w-full bg-white border-2 border-purple-500 text-purple-500 font-semibold py-3 rounded-full hover:bg-purple-50 transition-colors text-sm sm:text-base"
                >
                    Belum ada akun? Daftar dulu
                </button>
            </form>

        </div>
    </div> 
</body>

</html>