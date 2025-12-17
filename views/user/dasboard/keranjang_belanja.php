<?php
session_start();
// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../config/koneksi.php';

// Cek Koneksi & User
$db_conn = isset($conn) ? $conn : (isset($koneksi) ? $koneksi : null);
if (!$db_conn) { die("Error: Koneksi database gagal."); }
$id_user = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

// Variabel untuk menampung pesan sukses agar bisa ditampilkan SweetAlert nanti
$success_message = "";
$new_trx_code = "";

// --- 1. SINKRONISASI SESSION KE DATABASE ---
if (isset($_SESSION['keranjang']) && !empty($_SESSION['keranjang'])) {
    $db_conn->query("DELETE FROM keranjang WHERE id_user = $id_user");
    $stmt_sync = $db_conn->prepare("INSERT INTO keranjang (id_user, id_barang, jumlah) VALUES (?, ?, ?)");
    foreach ($_SESSION['keranjang'] as $id_brg => $item) {
        $qty = isset($item['qty']) ? $item['qty'] : (isset($item['jumlah']) ? $item['jumlah'] : 1);
        $stmt_sync->bind_param("iii", $id_user, $id_brg, $qty);
        $stmt_sync->execute();
    }
}

// --- 2. LOGIKA CHECKOUT (PESAN SEKARANG) ---
if (isset($_POST['checkout'])) {
    // Ambil Data Keranjang
    $sql_cart = "SELECT k.id_barang, k.jumlah, b.harga FROM keranjang k JOIN barang b ON k.id_barang = b.id_barang WHERE k.id_user = ?";
    $stmt = $db_conn->prepare($sql_cart);
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $total_bayar = 0;
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $subtotal = $row['harga'] * $row['jumlah'];
            $total_bayar += $subtotal;
            $items[] = ['id' => $row['id_barang'], 'qty' => $row['jumlah'], 'price' => $row['harga'], 'sub' => $subtotal];
        }

        // GENERATE KODE UNIK
        $kode_unik = "TRX-" . date('Ymd') . "-" . strtoupper(substr(md5(time() . rand()), 0, 4));

        // INSERT Transaksi (Status Pending)
        $sql_trx = "INSERT INTO transaksi (id_user, kode_transaksi, total_harga, status, tanggal) VALUES (?, ?, ?, 'pending', NOW())";
        $stmt_trx = $db_conn->prepare($sql_trx);
        $stmt_trx->bind_param("isi", $id_user, $kode_unik, $total_bayar);
        
        if ($stmt_trx->execute()) {
            $id_trx = $db_conn->insert_id;

            // INSERT Detail
            $sql_det = "INSERT INTO detail_transaksi (id_transaksi, id_barang, jumlah, harga_satuan, subtotal) VALUES (?, ?, ?, ?, ?)";
            $stmt_det = $db_conn->prepare($sql_det);
            foreach ($items as $item) {
                $stmt_det->bind_param("iiiii", $id_trx, $item['id'], $item['qty'], $item['price'], $item['sub']);
                $stmt_det->execute();
            }

            // Hapus Keranjang
            $db_conn->query("DELETE FROM keranjang WHERE id_user = $id_user");
            unset($_SESSION['keranjang']);
            
            // SIMPAN PESAN SUKSES KE VARIABEL (JANGAN REDIRECT DULU DI SINI)
            $success_message = "Pesanan berhasil dibuat!";
            $new_trx_code = $kode_unik;
        }
    }
}

// --- 3. LOGIKA UPDATE / HAPUS (GET) ---
if (isset($_GET['aksi']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $aksi = $_GET['aksi'];
    
    if (isset($_SESSION['keranjang'][$id])) {
        if ($aksi == 'tambah') { $_SESSION['keranjang'][$id]['qty'] += 1; } 
        elseif ($aksi == 'kurang') { 
            $_SESSION['keranjang'][$id]['qty'] -= 1;
            if ($_SESSION['keranjang'][$id]['qty'] <= 0) unset($_SESSION['keranjang'][$id]);
        } 
        elseif ($aksi == 'hapus') { unset($_SESSION['keranjang'][$id]); }
    }
    if ($aksi == 'hapus') { $db_conn->query("DELETE FROM keranjang WHERE id_user = $id_user AND id_barang = $id"); }
    
    header("Location: keranjang_belanja.php");
    exit;
}

// --- 4. TAMPILKAN DATA ---
$sql_view = "SELECT k.*, b.nama_barang, b.gambar, b.harga FROM keranjang k JOIN barang b ON k.id_barang = b.id_barang WHERE k.id_user = ?";
$stmt_view = $db_conn->prepare($sql_view);
$stmt_view->bind_param("i", $id_user);
$stmt_view->execute();
$res_view = $stmt_view->get_result();

$list_keranjang = [];
$grand_total = 0;
while($row = $res_view->fetch_assoc()){
    $list_keranjang[$row['id_barang']] = [
        'nama' => $row['nama_barang'], 'harga' => $row['harga'], 'qty' => $row['jumlah'],
        'gambar' => !empty($row['gambar']) ? '../../../public/images/product/img_produk/'.$row['gambar'] : 'https://placehold.co/100'
    ];
    $grand_total += ($row['harga'] * $row['jumlah']);
}
$_SESSION['keranjang'] = $list_keranjang;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Keranjang - Smart SMANSABAYA</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; -webkit-tap-highlight-color: transparent; }
        body { background-color: #F8F9FD; padding-bottom: 120px; }
        a { text-decoration: none; }
        
        .header-simple { background-color: white; padding: 20px; display: flex; align-items: center; box-shadow: 0 4px 15px rgba(0,0,0,0.03); position: sticky; top: 0; z-index: 100; }
        .btn-back { font-size: 18px; color: #333; margin-right: 15px; width: 30px; height: 30px; display: flex; align-items: center; }
        .page-title { font-size: 18px; font-weight: 600; color: #333; }
        
        .container { padding: 20px; }
        
        .cart-item { background-color: white; border-radius: 15px; padding: 15px; display: flex; align-items: center; margin-bottom: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.02); position: relative; }
        .item-img { width: 70px; height: 70px; border-radius: 12px; object-fit: cover; background-color: #f0f0f0; margin-right: 15px; }
        .item-details { flex: 1; }
        .item-name { font-size: 14px; font-weight: 600; color: #333; margin-bottom: 5px; line-height: 1.2; }
        .item-price { font-size: 14px; font-weight: 700; color: #00A859; }
        
        .qty-control { display: flex; align-items: center; background-color: #f5f5f5; border-radius: 8px; padding: 2px; }
        .btn-qty { width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; background-color: white; border-radius: 6px; color: #00A859; font-size: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); font-weight: bold; }
        .btn-qty.min { color: #FF4757; }
        .qty-val { width: 30px; text-align: center; font-size: 12px; font-weight: 600; }
        
        .btn-delete { position: absolute; top: 10px; right: 10px; color: #ddd; font-size: 14px; }
        
        .checkout-footer { position: fixed; bottom: 75px; left: 0; width: 100%; background-color: white; padding: 20px; border-top-left-radius: 25px; border-top-right-radius: 25px; box-shadow: 0 -5px 20px rgba(0,0,0,0.05); z-index: 100; }
        .total-info { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .label-total { font-size: 14px; color: #888; }
        .price-total { font-size: 20px; font-weight: 700; color: #00A859; }
        
        .btn-checkout { background: linear-gradient(to right, #00c870, #00A859); color: white; width: 100%; padding: 15px; border-radius: 15px; border: none; font-size: 16px; font-weight: 600; display: flex; justify-content: center; align-items: center; box-shadow: 0 8px 20px rgba(0, 168, 89, 0.3); cursor: pointer; }
        
        .empty-cart { text-align: center; margin-top: 50px; color: #bbb; }
        .empty-cart i { font-size: 60px; margin-bottom: 15px; color: #eee; }
        
        .bottom-navbar { position: fixed; bottom: 0; left: 0; width: 100%; background-color: white; height: 75px; display: flex; justify-content: space-between; padding: 0 20px; border-top-left-radius: 25px; border-top-right-radius: 25px; box-shadow: 0 -5px 30px rgba(0,0,0,0.08); z-index: 100; }
        .nav-link { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #C4C4C4; font-size: 11px; font-weight: 500; }
        .nav-link i { font-size: 22px; margin-bottom: 6px; }
        .nav-link.active { color: #00A859; font-weight: 700; }
        .nav-center-wrapper { position: relative; width: 60px; display: flex; justify-content: center; }
        .nav-fab { position: absolute; top: -30px; width: 64px; height: 64px; background: linear-gradient(135deg, #00C870, #00A859); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 26px; box-shadow: 0 10px 20px rgba(0, 168, 89, 0.4); border: 5px solid #F8F9FD; }
    </style>
</head>
<body>

    <div class="header-simple">
        <a href="home.php" class="btn-back"><i class="fas fa-arrow-left"></i></a>
        <div class="page-title">Keranjang Saya</div>
    </div>

    <div class="container">
        <?php if (empty($list_keranjang)): ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-basket"></i>
                <h3>Wah, keranjang kosong</h3>
                <p style="font-size: 12px;">Yuk, isi dengan jajanan kesukaanmu!</p><br>
                <a href="home.php" style="color: #00A859; font-weight: 600;">Kembali Belanja</a>
            </div>
        <?php else: ?>
            <?php foreach ($list_keranjang as $id => $item): ?>
            <div class="cart-item">
                <a href="?aksi=hapus&id=<?= $id ?>" class="btn-delete" onclick="return confirm('Hapus item ini?')"><i class="fas fa-times"></i></a>
                <img src="<?= $item['gambar'] ?>" class="item-img">
                <div class="item-details">
                    <div class="item-name"><?= $item['nama'] ?></div>
                    <div class="item-price">Rp <?= number_format($item['harga'], 0, ',', '.') ?></div>
                </div>
                <div class="qty-control">
                    <a href="?aksi=kurang&id=<?= $id ?>" class="btn-qty min"><i class="fas fa-minus"></i></a>
                    <div class="qty-val"><?= $item['qty'] ?></div>
                    <a href="?aksi=tambah&id=<?= $id ?>" class="btn-qty plus"><i class="fas fa-plus"></i></a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if (!empty($list_keranjang)): ?>
    <div class="checkout-footer">
        <div class="total-info">
            <div class="label-total">Total Pembayaran</div>
            <div class="price-total">Rp <?= number_format($grand_total, 0, ',', '.') ?></div>
        </div>
        
        <form id="formCheckout" method="POST">
            <input type="hidden" name="checkout" value="1">
            <button type="button" class="btn-checkout" onclick="konfirmasiPesanan()">
                Pesan Sekarang <i class="fas fa-arrow-right" style="margin-left: 10px;"></i>
            </button>
        </form>
    </div>
    <?php endif; ?>

    <script>
        // 1. FUNGSI KONFIRMASI SAAT KLIK TOMBOL
        function konfirmasiPesanan() {
            Swal.fire({
                title: 'Konfirmasi Pesanan',
                text: "Apakah Anda yakin ingin memproses pesanan ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#00A859',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Pesan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika user klik Ya, submit form secara manual via JS
                    document.getElementById('formCheckout').submit();
                }
            })
        }

        // 2. FUNGSI MENANGKAP SUKSES DARI PHP
        // Jika PHP di atas mengisi variabel $success_message, maka jalankan ini
        <?php if (!empty($success_message)): ?>
            Swal.fire({
                title: 'Berhasil!',
                text: '<?= $success_message ?>\nKode: <?= $new_trx_code ?>',
                icon: 'success',
                confirmButtonColor: '#00A859',
                confirmButtonText: 'Lihat Status Pesanan'
            }).then((result) => {
                // Setelah klik OK pada notif sukses, baru pindah halaman
                window.location.href = 'pesanan.php';
            });
        <?php endif; ?>
    </script>

</body>
</html>