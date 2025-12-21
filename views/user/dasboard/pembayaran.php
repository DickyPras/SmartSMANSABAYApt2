<?php
session_start();
// Menghilangkan pesan error agar tidak muncul di belakang notif SweetAlert
error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/../../../config/koneksi.php';

$db_conn = isset($conn) ? $conn : (isset($koneksi) ? $koneksi : null);
if (!$db_conn) { die("Koneksi database gagal."); }

$id_user = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

// Jika keranjang kosong, balikkan ke keranjang
if (!isset($_SESSION['keranjang']) || empty($_SESSION['keranjang'])) {
    header("Location: keranjang_belanja.php");
    exit;
}

$grand_total = 0;
foreach ($_SESSION['keranjang'] as $item) {
    $grand_total += ($item['harga'] * $item['qty']);
}

// Logika Simpan Transaksi
if (isset($_POST['bayar'])) {
    $metode = $_POST['metode_pembayaran'];
    $nomor_ewallet = isset($_POST['nomor_hp']) ? $_POST['nomor_hp'] : '-';
    
    // 1. Generate Kode Transaksi
    $kode_unik = "TRX-" . date('Ymd') . "-" . strtoupper(substr(md5(time() . rand()), 0, 4));

    // 2. Insert ke Tabel Transaksi
    $sql_trx = "INSERT INTO transaksi (id_user, kode_transaksi, total_harga, status, tanggal, metode_pembayaran, nomor_ewallet) 
                VALUES (?, ?, ?, 'pending', NOW(), ?, ?)";
    $stmt_trx = $db_conn->prepare($sql_trx);
    $stmt_trx->bind_param("isiss", $id_user, $kode_unik, $grand_total, $metode, $nomor_ewallet);
    
    if ($stmt_trx->execute()) {
        $id_trx = $db_conn->insert_id;

        // 3. Insert Detail Barang
        foreach ($_SESSION['keranjang'] as $id_brg => $item) {
            $subtotal = $item['harga'] * $item['qty'];
            $sql_det = "INSERT INTO detail_transaksi (id_transaksi, id_barang, jumlah, harga_satuan, subtotal) VALUES (?, ?, ?, ?, ?)";
            $stmt_det = $db_conn->prepare($sql_det);
            $stmt_det->bind_param("iiiii", $id_trx, $id_brg, $item['qty'], $item['harga'], $subtotal);
            $stmt_det->execute();
        }

        // 4. Bersihkan Keranjang Database & Session
        $stmt_del = $db_conn->prepare("DELETE FROM keranjang WHERE id_user = ?");
        $stmt_del->bind_param("i", $id_user);
        $stmt_del->execute();
        
        unset($_SESSION['keranjang']);
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Smart SMANSABAYA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #F8F9FD; padding: 20px; }
        .card { background: white; border-radius: 20px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); max-width: 500px; margin: auto; }
        
        /* Timer Styles */
        .timer-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .timer-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }
        .timer-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            opacity: 0.9;
            margin-bottom: 5px;
            position: relative;
            z-index: 1;
        }
        .timer-display {
            font-size: 48px;
            font-weight: 700;
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .timer-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .timer-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            animation: shake 0.5s ease-in-out infinite;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-2px); }
            75% { transform: translateX(2px); }
        }
        .timer-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 4px;
            background: rgba(255,255,255,0.5);
            transition: width 1s linear;
            z-index: 1;
        }

        /* Ringkasan Pesanan */
        .order-summary { background: #f9f9f9; border-radius: 15px; padding: 15px; margin-bottom: 20px; border: 1px solid #eee; }
        .summary-title { font-weight: 600; font-size: 14px; color: #555; margin-bottom: 10px; display: block; }
        .item-row { display: flex; align-items: center; gap: 12px; font-size: 13px; margin-bottom: 12px; color: #333; padding: 10px; background: white; border-radius: 12px; }
        .item-img { width: 50px; height: 50px; border-radius: 8px; object-fit: cover; background-color: #e0e0e0; flex-shrink: 0; }
        .item-details { flex: 1; display: flex; flex-direction: column; gap: 2px; min-width: 0; }
        .item-name { font-weight: 600; font-size: 13px; line-height: 1.3; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .item-meta { display: flex; justify-content: space-between; align-items: center; font-size: 12px; }
        .item-qty { color: #888; }
        .item-subtotal { font-weight: 700; color: #00A859; font-size: 13px; flex-shrink: 0; }

        /* Metode Pembayaran */
        .method-item { display: flex; align-items: center; padding: 15px; border: 2px solid #f0f0f0; border-radius: 15px; margin-bottom: 10px; cursor: pointer; transition: 0.3s; }
        .method-item:hover { border-color: #00A859; background: #f6fff9; }
        .method-item input { margin-right: 15px; accent-color: #00A859; }
        
        .ewallet-input { display: none; margin-top: 10px; padding: 12px; border-radius: 12px; border: 1px solid #ddd; width: 100%; box-sizing: border-box; outline: none; }
        .ewallet-input:focus { border-color: #00A859; }
        
        .btn-pay { background: #00A859; color: white; border: none; width: 100%; padding: 15px; border-radius: 15px; font-weight: 600; margin-top: 20px; cursor: pointer; transition: 0.3s; }
        .btn-pay:hover { background: #008a49; transform: translateY(-2px); }
        .btn-pay:disabled { background: #ccc; cursor: not-allowed; transform: none; }
        
        .btn-back { display: block; text-align: center; margin-top: 15px; padding: 12px; color: #888; text-decoration: none; font-size: 14px; font-weight: 500; border: 1px solid #eee; border-radius: 15px; transition: 0.3s; }
        .btn-back:hover { background-color: #fff1f1; color: #e74c3c; border-color: #ffcccc; }
    </style>
</head>
<body>

<div class="card">
    <h3 style="margin-bottom: 5px;">Pilih Pembayaran</h3>
    <p style="color: #888; font-size: 13px; margin-bottom: 20px;">Selesaikan pesanan jajanan Anda</p>

    <!-- Timer Countdown -->
    <div class="timer-container" id="timerContainer">
        <div class="timer-label"><i class="fas fa-clock"></i> Batas Waktu Pembayaran</div>
        <div class="timer-display" id="timerDisplay">00:30</div>
        <div class="timer-progress" id="timerProgress" style="width: 100%;"></div>
    </div>

    <div class="order-summary">
        <span class="summary-title"><i class="fas fa-shopping-cart"></i> Rincian Barang</span>
        <?php foreach ($_SESSION['keranjang'] as $item): ?>
            <div class="item-row">
                <img src="<?= !empty($item['gambar']) ? '../../../public/images/product/img_produk/' . $item['gambar'] : 'https://placehold.co/100x100?text=Produk' ?>" 
                     alt="<?= htmlspecialchars($item['nama']) ?>" 
                     class="item-img"
                     onerror="this.src='https://placehold.co/100x100?text=Produk'">
                <div class="item-details">
                    <span class="item-name"><?= htmlspecialchars($item['nama']) ?></span>
                    <div class="item-meta">
                        <span class="item-qty">Qty: <?= $item['qty'] ?> × Rp <?= number_format($item['harga'], 0, ',', '.') ?></span>
                        <span class="item-subtotal">Rp <?= number_format($item['harga'] * $item['qty'], 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <hr style="border: 0; border-top: 1px dashed #ddd; margin: 10px 0;">
        <div class="item-row" style="font-size: 15px; font-weight: 700;">
            <span>Total Tagihan</span>
            <span style="color: #00A859;">Rp <?= number_format($grand_total, 0, ',', '.') ?></span>
        </div>
    </div>

    <form method="POST" id="formBayar">
        <span class="summary-title" style="margin-bottom: 10px;">Metode Pembayaran</span>
        
        <label class="method-item">
            <input type="radio" name="metode_pembayaran" value="COD" required onclick="toggleEwallet(false)">
            <span><i class="fas fa-hand-holding-usd" style="width: 25px; color: #00A859;"></i> Bayar di Tempat (COD)</span>
        </label>

        <label class="method-item">
            <input type="radio" name="metode_pembayaran" value="DANA" onclick="toggleEwallet(true)">
            <span><i class="fas fa-wallet" style="width: 25px; color: #00A859;"></i> DANA</span>
        </label>

        <label class="method-item">
            <input type="radio" name="metode_pembayaran" value="GOPAY" onclick="toggleEwallet(true)">
            <span><i class="fas fa-mobile-alt" style="width: 25px; color: #00A859;"></i> GoPay</span>
        </label>

        <input type="text" name="nomor_hp" id="nomor_hp" class="ewallet-input" placeholder="Masukkan Nomor HP Aktif">

        <button type="submit" name="bayar" class="btn-pay">Konfirmasi Pembayaran</button>
        
        <a href="keranjang_belanja.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Kembali ke Keranjang
        </a>
    </form>
</div>

<script>
    // Timer Countdown Configuration
    const TIMER_SECONDS = 30;
    let timeLeft = TIMER_SECONDS;
    let timerInterval = null;
    let paymentExpired = false;

    function startPaymentTimer() {
        const timerDisplay = document.getElementById('timerDisplay');
        const timerProgress = document.getElementById('timerProgress');
        const timerContainer = document.getElementById('timerContainer');
        const btnPay = document.querySelector('.btn-pay');

        timerInterval = setInterval(() => {
            timeLeft--;
            
            // Update display
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            // Update progress bar
            const progressPercent = (timeLeft / TIMER_SECONDS) * 100;
            timerProgress.style.width = progressPercent + '%';
            
            // Change color based on time left
            if (timeLeft <= 10) {
                timerContainer.className = 'timer-container timer-danger';
            } else if (timeLeft <= 15) {
                timerContainer.className = 'timer-container timer-warning';
            }
            
            // Timer expired
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                paymentExpired = true;
                btnPay.disabled = true;
                timerDisplay.textContent = '00:00';
                
                Swal.fire({
                    title: 'Waktu Habis!',
                    text: 'Batas waktu pembayaran telah berakhir. Silakan coba lagi.',
                    icon: 'error',
                    confirmButtonColor: '#e74c3c',
                    confirmButtonText: 'Kembali ke Keranjang',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then(() => {
                    window.location.href = 'keranjang_belanja.php';
                });
            }
        }, 1000);
    }

    // Start timer when page loads
    document.addEventListener('DOMContentLoaded', startPaymentTimer);

    // Prevent form submission if expired
    document.getElementById('formBayar').addEventListener('submit', function(e) {
        if (paymentExpired) {
            e.preventDefault();
            Swal.fire({
                title: 'Pembayaran Gagal!',
                text: 'Waktu pembayaran telah habis.',
                icon: 'error',
                confirmButtonColor: '#e74c3c'
            });
            return false;
        }
    });

    function toggleEwallet(show) {
        const input = document.getElementById('nomor_hp');
        input.style.display = show ? 'block' : 'none';
        input.required = show;
        if(show) input.focus();
    }

    <?php if (isset($success)): ?>
    // Stop timer if payment successful
    clearInterval(timerInterval);
    Swal.fire({
        title: 'Berhasil!',
        text: 'Pesanan Anda telah diterima. Silahkan cek menu Pesanan untuk mengambil barang.',
        icon: 'success',
        confirmButtonColor: '#00A859'
    }).then(() => {
        window.location.href = 'pesanan.php';
    });
    <?php endif; ?>
</script>

</body>
</html>