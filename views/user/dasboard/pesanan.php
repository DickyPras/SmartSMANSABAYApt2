<?php
session_start();
require_once __DIR__ . '/../../../config/koneksi.php';

$db_conn = isset($conn) ? $conn : (isset($koneksi) ? $koneksi : null);
if (!$db_conn) { die("Error: Koneksi database gagal."); }
$id_user = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

// --- LOGIKA TERIMA BARANG ---
if (isset($_POST['terima_pesanan'])) {
    $id_trx = $_POST['id_transaksi'];
    $stmt = $db_conn->prepare("UPDATE transaksi SET status = 'selesai' WHERE id_transaksi = ? AND status = 'dibayar'");
    $stmt->bind_param("i", $id_trx);
    $stmt->execute();
    header("Location: pesanan.php"); exit;
}

// --- AMBIL DATA ---
$sql = "SELECT t.id_transaksi, t.kode_transaksi, t.tanggal, t.total_harga, t.status, t.metode_pembayaran, b.nama_barang, d.jumlah
        FROM transaksi t
        JOIN detail_transaksi d ON t.id_transaksi = d.id_transaksi
        JOIN barang b ON d.id_barang = b.id_barang
        WHERE t.id_user = ? ORDER BY t.tanggal DESC";
$stmt = $db_conn->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $id = $row['id_transaksi'];
    $kode = !empty($row['kode_transaksi']) ? $row['kode_transaksi'] : '#TRX-'.str_pad($id, 4, '0', STR_PAD_LEFT);
    
    if (!isset($orders[$id])) {
        $orders[$id] = [
            'id' => $id, 'code' => $kode,
            'date' => date('d M Y, H:i', strtotime($row['tanggal'])),
            'status' => $row['status'], 
            'total' => $row['total_harga'],
            'metode' => $row['metode_pembayaran'], 
            'items' => []
        ];
    }
    $orders[$id]['items'][] = $row['nama_barang'] . " (" . $row['jumlah'] . "x)";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pesanan Saya</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; -webkit-tap-highlight-color: transparent; }
        body { background-color: #F8F9FD; padding-bottom: 100px; }
        a { text-decoration: none; }
        .header-simple { background-color: white; padding: 25px 20px 15px 20px; position: sticky; top: 0; z-index: 100; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border-bottom-left-radius: 20px; border-bottom-right-radius: 20px; }
        .page-title { font-size: 22px; font-weight: 700; color: #333; }
        .page-subtitle { font-size: 13px; color: #888; margin-top: 2px; }
        .container { padding: 20px; }
        .order-card { background-color: white; border-radius: 18px; padding: 18px; margin-bottom: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.04); position: relative; overflow: hidden; }
        .card-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .order-id-box { display: flex; align-items: center; gap: 8px; }
        .icon-receipt { width: 32px; height: 32px; background: #E8F5E9; color: #00A859; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px; }
        .order-id-text { font-size: 14px; font-weight: 700; color: #333; }
        .order-date { font-size: 11px; color: #999; display: block; margin-top: 2px; }
        .status-badge { padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; display: flex; align-items: center; gap: 5px; }
        .bg-pending { background:#FFF3E0; color:#EF6C00; }
        .bg-dibayar { background:#E3F2FD; color:#1565C0; }
        .bg-selesai { background:#E8F5E9; color:#2E7D32; }
        .divider { border-bottom: 2px dashed #f0f0f0; margin: 10px 0 15px 0; }
        .menu-list { margin-bottom: 15px; }
        .menu-item { font-size: 13px; color: #555; margin-bottom: 6px; display: flex; justify-content: space-between; }
        .card-bottom { display: flex; justify-content: space-between; align-items: center; }
        .label-total { font-size: 11px; color: #888; }
        .price-total { font-size: 16px; font-weight: 700; color: #00A859; }
        .btn-action { padding: 10px 20px; border-radius: 10px; font-size: 12px; font-weight: 600; border: none; cursor: pointer; }
        .btn-green { background-color: #00A859; color: white; box-shadow: 0 4px 10px rgba(0, 168, 89, 0.3); }
        .btn-outline { background-color: white; color: #00A859; border: 1px solid #00A859; }
        .btn-grey { background-color: #f0f0f0; color: #999; cursor:not-allowed; }
        .pickup-info { background-color: #FFF8E1; color: #F57F17; font-size: 11px; padding: 8px 12px; border-radius: 8px; margin-top: 12px; display: flex; align-items: center; gap: 8px; }
        .payment-method { font-size: 10px; color: #00A859; font-weight: 600; margin-top: 2px; text-transform: uppercase; }
        .bottom-navbar { position: fixed; bottom: 0; left: 0; width: 100%; background-color: white; height: 75px; display: flex; justify-content: space-between; padding: 0 20px; border-top-left-radius: 25px; border-top-right-radius: 25px; box-shadow: 0 -5px 30px rgba(0,0,0,0.08); z-index: 100; }
        .nav-link { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #C4C4C4; font-size: 11px; font-weight: 500;}
        .nav-link i { font-size: 22px; margin-bottom: 6px; }
        .nav-link.active { color: #00A859; font-weight: 700; }
        .nav-center-wrapper { position: relative; width: 60px; display: flex; justify-content: center; }
        .nav-fab { position: absolute; top: -30px; width: 64px; height: 64px; background: linear-gradient(135deg, #00C870, #00A859); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 26px; box-shadow: 0 10px 20px rgba(0, 168, 89, 0.4); border: 5px solid #F8F9FD; }
    </style>
</head>
<body>

    <div class="header-simple">
        <div class="page-title">Pesanan Saya</div>
        <div class="page-subtitle">Pantau status pesananmu di sini</div>
    </div>

    <div class="container">
        <?php if(empty($orders)): ?>
            <div class="empty-state">
                <i class="fas fa-receipt" style="font-size: 60px; margin-bottom: 20px; opacity: 0.3;"></i>
                <p>Belum ada riwayat jajan nih.</p>
                <a href="home.php" style="color: #00A859; font-weight: 600; font-size: 14px; margin-top: 10px; display: block;">Mulai Jajan</a>
            </div>
        <?php else: ?>
            <?php foreach($orders as $order): ?>
            <div class="order-card">
                <div class="card-top">
                    <div class="order-id-box">
                        <div class="icon-receipt"><i class="fas fa-receipt"></i></div>
                        <div>
                            <div class="order-id-text"><?= $order['code'] ?></div>
                            <div class="payment-method">Metode: <?= $order['metode'] ?></div>
                            <span class="order-date"><?= $order['date'] ?></span>
                        </div>
                    </div>
                    <?php if($order['status'] == 'pending'): ?>
                        <div class="status-badge bg-pending"><i class="fas fa-clock"></i> Menunggu</div>
                    <?php elseif($order['status'] == 'dibayar'): ?>
                        <div class="status-badge bg-dibayar"><i class="fas fa-bell"></i> Diproses</div>
                    <?php elseif($order['status'] == 'selesai'): ?>
                        <div class="status-badge bg-selesai"><i class="fas fa-check-circle"></i> Selesai</div>
                    <?php endif; ?>
                </div>

                <div class="divider"></div>

                <div class="menu-list">
                    <?php foreach($order['items'] as $item): ?>
                        <div class="menu-item"><span><?= $item ?></span></div>
                    <?php endforeach; ?>
                </div>

                <div class="card-bottom">
                    <div>
                        <div class="label-total">Total Bayar</div>
                        <div class="price-total">Rp <?= number_format($order['total'], 0, ',', '.') ?></div>
                        
                        <div style="font-size: 10px; margin-top: 4px; font-weight: 600;">
                            <?php if($order['status'] == 'pending'): ?>
                                <?php if($order['metode'] == 'COD'): ?>
                                    <span style="color: #C62828;"><i class="fas fa-exclamation-circle"></i> BELUM BAYAR</span>
                                <?php else: ?>
                                    <span style="color: #00A859;"><i class="fas fa-check-circle"></i> SUDAH MEMBAYAR</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span style="color: #00A859;"><i class="fas fa-check-circle"></i> SUDAH MEMBAYAR</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if($order['status'] == 'dibayar'): ?>
                        <form method="POST">
                            <input type="hidden" name="id_transaksi" value="<?= $order['id'] ?>">
                            <button type="submit" name="terima_pesanan" class="btn-action btn-green">Pesanan Diterima</button>
                        </form>
                    <?php elseif($order['status'] == 'selesai'): ?>
                        <a href="home.php" class="btn-action btn-outline">Pesan Lagi</a>
                    <?php elseif($order['status'] == 'pending'): ?>
                        <button class="btn-action btn-grey" disabled>Belum Diambil</button>
                    <?php endif; ?>
                </div>

                <?php if($order['status'] == 'pending'): ?>
                <div class="pickup-info">
                    <i class="fas fa-info-circle"></i> Mohon tunggu konfirmasi admin.
                </div>
                <?php endif; ?>
                
                <?php if($order['status'] == 'dibayar'): ?>
                <div class="pickup-info" style="background:#E3F2FD; color:#1565C0;">
                    <i class="fas fa-shopping-bag"></i> <b>Silahkan ambil pesanan di kantin!</b>
                </div>
                <?php endif; ?>

            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <nav class="bottom-navbar">
        <a href="home.php" class="nav-link"><i class="fas fa-home"></i><span>Beranda</span></a>
        <a href="pesanan.php" class="nav-link active"><i class="fas fa-receipt"></i><span>Pesanan</span></a>
        <div class="nav-center-wrapper">
            <a href="keranjang_belanja.php" class="nav-fab"><i class="fas fa-shopping-basket"></i></a>
        </div>
        <a href="riwayat.php" class="nav-link"><i class="fas fa-history"></i><span>Riwayat</span></a>
        <a href="profile_user.php" class="nav-link"><i class="fas fa-user"></i><span>Profil</span></a>
    </nav>

</body>
</html>