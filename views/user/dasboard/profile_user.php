<?php
session_start();
require_once __DIR__ . '/../../../config/koneksi.php';

// Cek Koneksi
$db_conn = isset($conn) ? $conn : (isset($koneksi) ? $koneksi : null);
if (!$db_conn) { die("Error: Koneksi database gagal."); }

// Cek Login (Jika belum login, lempar ke halaman login)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['user_id'];

// --- AMBIL DATA USER DARI DATABASE ---
$sql = "SELECT * FROM users WHERE id_user = ?";
$stmt = $db_conn->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    // Jika user tidak ditemukan (misal dihapus admin saat login), logout paksa
    session_destroy();
    header("Location: ../auth/login.php");
    exit;
}

// Default Avatar jika tidak ada foto
$avatar_url = "https://ui-avatars.com/api/?name=" . urlencode($user['nama']) . "&background=00A859&color=fff&size=128";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Biodata Diri</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-green': '#00A859', // Hijau SMANSABAYA
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

        @keyframes slideUpFade {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes zoomIn {
            from { opacity: 0; transform: scale(0.5); }
            to { opacity: 1; transform: scale(1); }
        }

        .animate-card { animation: slideUpFade 0.6s ease-out forwards; }
        .animate-avatar { animation: zoomIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }

        .delay-100 { animation-delay: 0.1s; opacity: 0; animation-fill-mode: forwards; }
        .delay-200 { animation-delay: 0.2s; opacity: 0; animation-fill-mode: forwards; }
        .delay-300 { animation-delay: 0.3s; opacity: 0; animation-fill-mode: forwards; }
        .delay-400 { animation-delay: 0.4s; opacity: 0; animation-fill-mode: forwards; }
        .delay-500 { animation-delay: 0.5s; opacity: 0; animation-fill-mode: forwards; }
        
        .item-animate { animation: slideUpFade 0.5s ease-out forwards; opacity: 0; }
    </style>
</head>
<body class="bg-light-bg min-h-screen pb-10">

<div class="container mx-auto max-w-md relative">

    <div class="bg-primary-green h-96 rounded-b-[50px] absolute top-0 left-0 w-full z-0 shadow-lg"></div>

    <div class="relative z-10 px-6 pt-12">
        
        <header class="flex justify-between items-center text-white animate-card relative z-20">
            <a href="home.php" class="flex items-center justify-center w-11 h-11 rounded-2xl bg-white/20 backdrop-blur-md hover:bg-white/30 transition shadow-sm border border-white/10 hover:scale-105 active:scale-95 duration-200">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            
            <h1 class="text-xl font-bold tracking-wide drop-shadow-md">Profil Saya</h1>
            
            <a href="edit_profile.php" class="flex items-center justify-center w-11 h-11 rounded-2xl bg-white/20 backdrop-blur-md hover:bg-white/30 transition font-semibold text-xs shadow-sm border border-white/10 hover:scale-105 active:scale-95 duration-200">
                Edit
            </a>
        </header>
        
        <div class="bg-white p-6 rounded-[35px] shadow-xl animate-card delay-100 mt-28 relative z-10 mb-10">
            
            <div class="flex flex-col items-center -mt-24 mb-5">
                <div class="p-2 bg-white rounded-full shadow-xl animate-avatar group cursor-pointer">
                    <img src="<?= $avatar_url ?>" 
                         alt="Profile Avatar" 
                         class="w-28 h-28 rounded-full object-cover border-4 border-primary-green/20 group-hover:scale-105 transition-transform duration-300">
                </div>
                
                <h2 class="text-xl font-bold text-gray-800 mt-3 item-animate delay-200"><?= htmlspecialchars($user['nama']) ?></h2>
                <span class="text-sm text-primary-green font-bold bg-green-50 px-4 py-1.5 rounded-full mt-2 item-animate delay-200 border border-green-100 shadow-sm">
                    <?= ucfirst($user['role']) ?> Aktif
                </span>
            </div>

            <div class="space-y-4">
                
                <div class="item-animate delay-300">
                    <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-3 ml-1">Info Akademik</h3>
                    
                    <?php
                    function displayField($icon, $label, $value, $delay, $isPassword = false) {
                        $type = $isPassword ? 'password' : 'text';
                        $masking = $isPassword ? 'text-2xl tracking-widest' : 'text-sm';
                        // Jika nilai kosong, tampilkan strip
                        $displayValue = !empty($value) ? htmlspecialchars($value) : '-';
                        
                        echo "
                        <div class='flex items-center bg-gray-50 p-3.5 rounded-2xl mb-3 border border-gray-100 item-animate $delay hover:border-primary-green/50 hover:bg-green-50/30 transition duration-300 shadow-sm group'>
                            <div class='w-10 h-10 rounded-xl bg-white flex items-center justify-center text-primary-green shadow-sm mr-4 shrink-0 group-hover:scale-110 transition-transform duration-300'>
                                <i class='fas $icon text-lg'></i>
                            </div>
                            <div class='flex-1 overflow-hidden'>
                                <label class='block text-[10px] font-bold text-gray-400 uppercase mb-0.5'>$label</label>
                                <input class='w-full bg-transparent border-none text-gray-700 font-semibold focus:outline-none p-0 $masking truncate' 
                                       type='$type' value='$displayValue' readonly>
                            </div>
                        </div>";
                    }

                    // Tampilkan Data dari Database
                    displayField('fa-graduation-cap', 'Kelas', $user['kelas'], 'delay-300');
                    // Bio tidak ada di struktur tabel Anda, jadi saya pakai statis atau bisa dihapus
                    // displayField('fa-quote-left', 'Bio', 'Siswa Rajin', 'delay-300'); 
                    ?>
                </div>

                <div class="item-animate delay-400">
                    <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-3 ml-1 pt-2 border-t border-dashed border-gray-200">Data Pribadi</h3>
                    
                    <?php 
                        displayField('fa-id-card', 'NIS', $user['nis'], 'delay-400');
                        // No HP tidak ada di tabel users (berdasarkan SQL Anda), jadi saya hapus atau bisa ditambah manual di DB
                        // displayField('fa-phone', 'No HP/WA', $user['no_hp'], 'delay-400'); 
                        displayField('fa-envelope', 'Email', $user['email'], 'delay-400');
                        // Password tidak ditampilkan demi keamanan, tapi jika ingin menampilkan placeholder bintang:
                        displayField('fa-lock', 'Password', '********', 'delay-400', true);
                    ?>
                </div>

                <div class="item-animate delay-500">
                    <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-3 ml-1 pt-2 border-t border-dashed border-gray-200">Pengaturan Akun</h3>
                    
                    <div class="grid grid-cols-2 gap-3 mt-2">
                        <a href="../../user/auth/login.php" onclick="return confirmLogout(event)" 
                           class="group flex flex-col items-center justify-center bg-gray-50 border border-gray-200 
                                  hover:bg-gray-100 hover:border-gray-300 hover:shadow-lg hover:-translate-y-1 
                                  active:scale-95 active:translate-y-0
                                  text-gray-600 p-3 rounded-2xl transition-all duration-300 cursor-pointer">
                            <i class="fas fa-sign-out-alt text-xl mb-1 text-gray-500 group-hover:text-gray-700 group-hover:scale-110 transition-transform duration-300"></i>
                            <span class="text-xs font-bold">Logout</span>
                        </a>

                        <button onclick="confirmDelete()" 
                                class="group flex flex-col items-center justify-center bg-red-50 border border-red-100 
                                       hover:bg-red-500 hover:border-red-600 hover:text-white hover:shadow-lg hover:-translate-y-1 
                                       active:scale-95 active:translate-y-0
                                       text-red-500 p-3 rounded-2xl transition-all duration-300">
                            <i class="fas fa-trash-alt text-xl mb-1 group-hover:scale-110 group-hover:animate-pulse transition-transform duration-300"></i>
                            <span class="text-xs font-bold">Hapus Akun</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Fungsi Logout
    function confirmLogout(e) {
        e.preventDefault(); 
        const href = e.currentTarget.getAttribute('href');

        Swal.fire({
            title: 'Yakin ingin keluar?',
            text: "Sesi Anda akan diakhiri.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#00A859',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Logout',
            cancelButtonText: 'Batal',
            borderRadius: '20px'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = href;
            }
        });
    }

    // 2. Fungsi Hapus Akun (Hanya simulasi UI, perlu backend real untuk menghapus)
    function confirmDelete() {
        Swal.fire({
            title: 'HAPUS AKUN?',
            text: "Data Anda akan dihapus permanen dari database.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Hapus Sekarang',
            cancelButtonText: 'Batalkan'
        }).then((result) => {
            if (result.isConfirmed) {
                // Arahkan ke file proses delete (Anda harus buat file ini)
                // window.location.href = "../../../process/user/auth/delete_account.php";
                
                Swal.fire({
                    title: 'Info',
                    text: 'Fitur hapus akun belum diaktifkan demi keamanan.',
                    icon: 'info'
                });
            }
        })
    }
</script>

</body>
</html>