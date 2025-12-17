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
        
        /* Ringkasan Pesanan */
        .order-summary { background: #f9f9f9; border-radius: 15px; padding: 15px; margin-bottom: 20px; border: 1px solid #eee; }
        .summary-title { font-weight: 600; font-size: 14px; color: #555; margin-bottom: 10px; display: block; }
        .item-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 8px; color: #333; }
        .item-name { flex: 1; padding-right: 10px; }
        .item-qty { color: #888; margin-right: 10px; }
        .item-subtotal { font-weight: 600; }

        /* Metode Pembayaran */
        .method-item { display: flex; align-items: center; padding: 15px; border: 2px solid #f0f0f0; border-radius: 15px; margin-bottom: 10px; cursor: pointer; transition: 0.3s; }
        .method-item:hover { border-color: #00A859; background: #f6fff9; }
        .method-item input { margin-right: 15px; accent-color: #00A859; }
        
        .ewallet-input { display: none; margin-top: 10px; padding: 12px; border-radius: 12px; border: 1px solid #ddd; width: 100%; box-sizing: border-box; outline: none; }
        .ewallet-input:focus { border-color: #00A859; }
        
        .btn-pay { background: #00A859; color: white; border: none; width: 100%; padding: 15px; border-radius: 15px; font-weight: 600; margin-top: 20px; cursor: pointer; transition: 0.3s; }
        .btn-pay:hover { background: #008a49; transform: translateY(-2px); }
        
        .btn-back { display: block; text-align: center; margin-top: 15px; padding: 12px; color: #888; text-decoration: none; font-size: 14px; font-weight: 500; border: 1px solid #eee; border-radius: 15px; transition: 0.3s; }
        .btn-back:hover { background-color: #fff1f1; color: #e74c3c; border-color: #ffcccc; }
    </style>
</head>
<body>

<div class="card">
    <h3 style="margin-bottom: 5px;">Pilih Pembayaran</h3>
    <p style="color: #888; font-size: 13px; margin-bottom: 20px;">Selesaikan pesanan jajanan Anda</p>

    <div class="order-summary">
        <span class="summary-title"><i class="fas fa-shopping-cart"></i> Rincian Barang</span>
        <?php foreach ($_SESSION['keranjang'] as $item): ?>
            <div class="item-row">
                <span class="item-name"><?= $item['nama'] ?></span>
                <span class="item-qty">x<?= $item['qty'] ?></span>
                <span class="item-subtotal">Rp <?= number_format($item['harga'] * $item['qty'], 0, ',', '.') ?></span>
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
    function toggleEwallet(show) {
        const input = document.getElementById('nomor_hp');
        input.style.display = show ? 'block' : 'none';
        input.required = show;
        if(show) input.focus();
    }

    <?php if (isset($success)): ?>
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