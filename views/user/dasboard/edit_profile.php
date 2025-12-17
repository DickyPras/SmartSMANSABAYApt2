<?php
session_start();
require_once __DIR__ . '/../../../config/koneksi.php';

// Cek Koneksi & Login
$db_conn = isset($conn) ? $conn : (isset($koneksi) ? $koneksi : null);
if (!$db_conn) { die("Error: Koneksi database gagal."); }

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['user_id'];
$updateSuccess = false;
$errorMessage = "";

// --- 1. PROSES UPDATE DATA ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama  = $_POST['nama'];
    $kelas = $_POST['kelas'];
    $email = $_POST['email'];
    // $bio   = $_POST['bio']; // (Opsional jika ada kolom bio di DB)
    // $hp    = $_POST['hp'];  // (Opsional jika ada kolom no_hp di DB)
    
    // Cek Password Baru
    $password_baru = $_POST['password'];

    if (!empty($password_baru)) {
        // Jika user mengisi password baru -> Update password juga
        // Pastikan di database password disimpan hash (password_hash) atau plain (sesuai sistem Anda)
        // Di sini saya asumsikan plain text sesuai kode register sebelumnya, 
        // TAPI SANGAT DISARANKAN PAKAI password_hash()
        
        $sql_update = "UPDATE users SET nama=?, kelas=?, email=?, password=? WHERE id_user=?";
        $stmt = $db_conn->prepare($sql_update);
        $stmt->bind_param("ssssi", $nama, $kelas, $email, $password_baru, $id_user);
    } else {
        // Jika password kosong -> Update data lain saja
        $sql_update = "UPDATE users SET nama=?, kelas=?, email=? WHERE id_user=?";
        $stmt = $db_conn->prepare($sql_update);
        $stmt->bind_param("sssi", $nama, $kelas, $email, $id_user);
    }

    if ($stmt->execute()) {
        $updateSuccess = true;
        // Update nama di session juga biar langsung berubah di header jika ada
        $_SESSION['nama_user'] = $nama; 
    } else {
        $errorMessage = "Gagal mengupdate data: " . $stmt->error;
    }
}

// --- 2. AMBIL DATA USER UNTUK DITAMPILKAN DI FORM ---
$sql_get = "SELECT * FROM users WHERE id_user = ?";
$stmt_get = $db_conn->prepare($sql_get);
$stmt_get->bind_param("i", $id_user);
$stmt_get->execute();
$result_get = $stmt_get->get_result();
$user = $result_get->fetch_assoc();

// Default Avatar
$avatar_url = "https://ui-avatars.com/api/?name=" . urlencode($user['nama']) . "&background=00A859&color=fff&size=128";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil</title>
    
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
                        'primary-green': '#00A859',
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

        @keyframes fadeInUpSoft {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-enter { 
            opacity: 0; 
            animation: fadeInUpSoft 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; 
        }

        .delay-0 { animation-delay: 0ms; }
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }

        .input-group:focus-within .input-icon {
            color: #00A859;
            transform: scale(1.1);
        }
        
        .input-field { transition: all 0.3s ease; }
        .input-field:focus {
            border-color: #00A859;
            box-shadow: 0 4px 20px rgba(0, 168, 89, 0.1);
            transform: translateY(-2px);
            background-color: #fff;
        }
    </style>
</head>
<body class="bg-light-bg min-h-screen pb-10">

<div class="container mx-auto max-w-md relative">

    <div class="bg-primary-green h-64 rounded-b-[40px] absolute top-0 left-0 w-full z-0 shadow-lg"></div>

    <div class="relative z-10 px-6 pt-12">
        
        <header class="flex justify-between items-center text-white mb-8 animate-enter delay-0">
            <a href="profile_user.php" class="flex items-center justify-center w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md hover:bg-white/30 transition text-sm font-semibold hover:scale-110 active:scale-95 duration-200">
                <i class="fas fa-times"></i>
            </a>
            
            <h1 class="text-lg font-bold tracking-wide drop-shadow-md">Edit Profil</h1>
            
            <button type="button" onclick="confirmSave()" class="flex items-center justify-center w-10 h-10 rounded-xl bg-white text-primary-green hover:bg-green-50 transition shadow-lg hover:scale-110 active:scale-95 duration-200">
                <i class="fas fa-check"></i>
            </button>
        </header>
        
        <div class="bg-white p-6 rounded-[30px] shadow-xl animate-enter delay-100 mt-4 pb-10">
            
            <div class="flex justify-center -mt-16 mb-6 relative animate-enter delay-100">
                <div class="p-1.5 bg-white rounded-full shadow-md relative group cursor-pointer transition-transform hover:scale-105 duration-300">
                    <img src="<?= $avatar_url ?>" 
                         alt="Profile Avatar" 
                         class="w-24 h-24 rounded-full object-cover border-4 border-gray-100 group-hover:border-green-100 transition-colors">
                    
                    <div class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300 backdrop-blur-[1px]">
                        <i class="fas fa-camera text-white text-xl animate-bounce"></i>
                    </div>
                    
                    <div class="absolute bottom-1 right-1 bg-primary-green text-white w-8 h-8 rounded-full flex items-center justify-center border-2 border-white shadow-sm group-hover:rotate-12 transition-transform">
                        <i class="fas fa-pen text-xs"></i>
                    </div>
                </div>
            </div>

            <form id="editForm" method="POST" action="" class="space-y-6">

                <div class="animate-enter delay-200">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 pl-1 border-l-4 border-primary-green/50 ml-1">&nbsp;Info Umum</h3>
                    
                    <div class="space-y-4">
                        <div class="group input-group">
                            <label class="block text-[11px] font-medium text-gray-500 mb-1 ml-1 group-hover:text-primary-green transition-colors">Nama Lengkap</label>
                            <div class="relative">
                                <span class="absolute left-4 top-3.5 text-gray-400 input-icon transition-all duration-300"><i class="fas fa-user"></i></span>
                                <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" 
                                       class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 focus:outline-none input-field" required>
                            </div>
                        </div>

                        <div class="group input-group">
                            <label class="block text-[11px] font-medium text-gray-500 mb-1 ml-1 group-hover:text-primary-green transition-colors">Kelas</label>
                            <div class="relative">
                                <span class="absolute left-4 top-3.5 text-gray-400 input-icon transition-all duration-300"><i class="fas fa-graduation-cap"></i></span>
                                <select name="kelas" class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 focus:outline-none input-field appearance-none">
                                    <option value="<?= $user['kelas'] ?>" selected><?= $user['kelas'] ?> (Saat ini)</option>
                                    <option value="10 IPA 1">10 IPA 1</option>
                                    <option value="10 IPA 2">10 IPA 2</option>
                                    <option value="10 IPS 1">10 IPS 1</option>
                                    <option value="11 IPA 1">11 IPA 1</option>
                                    <option value="12 IPA 1">12 IPA 1</option>
                                    </select>
                            </div>
                        </div>

                        </div>
                </div>

                <hr class="border-dashed border-gray-200 my-2">

                <div class="animate-enter delay-300">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 pl-1 border-l-4 border-primary-green/50 ml-1">&nbsp;Data Akun</h3>
                    
                    <div class="space-y-4">
                        <div class="group input-group opacity-70">
                            <label class="block text-[11px] font-medium text-gray-500 mb-1 ml-1">NIS <span class="text-[9px] text-gray-400">(Tidak dapat diubah)</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-3.5 text-gray-400 input-icon"><i class="fas fa-id-card"></i></span>
                                <input type="text" value="<?= $user['nis'] ?>" 
                                       class="w-full pl-10 pr-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-sm font-medium text-gray-500 focus:outline-none cursor-not-allowed" readonly>
                            </div>
                        </div>

                        <div class="group input-group">
                            <label class="block text-[11px] font-medium text-gray-500 mb-1 ml-1 group-hover:text-primary-green transition-colors">Email</label>
                            <div class="relative">
                                <span class="absolute left-4 top-3.5 text-gray-400 input-icon transition-all duration-300"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" 
                                       class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 focus:outline-none input-field" required>
                            </div>
                        </div>

                        <div class="group input-group">
                            <label class="block text-[11px] font-medium text-gray-500 mb-1 ml-1 group-hover:text-primary-green transition-colors">Password Baru</label>
                            <div class="relative">
                                <span class="absolute left-4 top-3.5 text-gray-400 input-icon transition-all duration-300"><i class="fas fa-lock"></i></span>
                                <input type="password" name="password" placeholder="Biarkan kosong jika tidak ingin mengubah" 
                                       class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 focus:outline-none input-field placeholder:text-gray-300 transition-all">
                            </div>
                        </div>
                    </div>
                </div>

            </form>

        </div>
        
        <div class="h-10"></div>

    </div>

</div>

<script>
    function confirmSave() {
        const form = document.getElementById('editForm');
        // Bisa tambah validasi JS di sini jika perlu
        form.submit();
    }

    // Notifikasi Sukses
    <?php if ($updateSuccess): ?>
        Swal.fire({
            title: 'Berhasil Disimpan!',
            text: 'Data profil Anda telah diperbarui.',
            icon: 'success',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            showClass: { popup: 'animate__animated animate__zoomIn' },
            hideClass: { popup: 'animate__animated animate__fadeOutUp' }
        }).then(() => {
            window.location.href = 'profile_user.php';
        });
    <?php endif; ?>

    // Notifikasi Gagal
    <?php if (!empty($errorMessage)): ?>
        Swal.fire({
            title: 'Gagal!',
            text: '<?= $errorMessage ?>',
            icon: 'error'
        });
    <?php endif; ?>
</script>

</body>
</html>