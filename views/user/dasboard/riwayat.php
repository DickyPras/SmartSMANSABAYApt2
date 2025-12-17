<?php
session_start();
require_once __DIR__ . '/../../../config/koneksi.php';

// Cek Koneksi
$db_conn = isset($conn) ? $conn : (isset($koneksi) ? $koneksi : null);
if (!$db_conn) { die("Error: Koneksi database gagal."); }

// Ambil ID User (Default 1 jika belum login)
$id_user = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

// --- 1. AMBIL DATA RIWAYAT (HANYA STATUS 'SELESAI') ---
$sql = "SELECT 
            t.id_transaksi, 
            t.kode_transaksi,
            t.tanggal, 
            t.total_harga, 
            b.nama_barang,
            d.jumlah
        FROM transaksi t
        JOIN detail_transaksi d ON t.id_transaksi = d.id_transaksi
        JOIN barang b ON d.id_barang = b.id_barang
        WHERE t.id_user = ? AND t.status = 'selesai' 
        ORDER BY t.tanggal DESC";

$stmt = $db_conn->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

// Grouping Data
$riwayat_pembelian = [];
while ($row = $result->fetch_assoc()) {
    $id = $row['id_transaksi'];
    
    // Gunakan Kode Unik jika ada, jika tidak pakai ID biasa
    $kode_tampil = !empty($row['kode_transaksi']) ? $row['kode_transaksi'] : 'INV-'.date('Ymd', strtotime($row['tanggal'])).'-'.str_pad($id, 3, '0', STR_PAD_LEFT);

    if (!isset($riwayat_pembelian[$id])) {
        $riwayat_pembelian[$id] = [
            'id_order' => $kode_tampil,
            'tanggal'  => date('d M Y, H:i', strtotime($row['tanggal'])),
            'total'    => $row['total_harga'],
            'status'   => 'Selesai', 
            'items'    => []
        ];
    }
    // Masukkan item ke list
    $riwayat_pembelian[$id]['items'][] = [
        'nama' => $row['nama_barang'], 
        'qty' => $row['jumlah']
    ];
}

// --- 2. HITUNG BADGE KERANJANG (DARI DATABASE AGAR AKURAT) ---
$total_keranjang = 0;
$stmt_cart = $db_conn->prepare("SELECT SUM(jumlah) as total FROM keranjang WHERE id_user = ?");
$stmt_cart->bind_param("i", $id_user);
$stmt_cart->execute();
$res_cart = $stmt_cart->get_result();
if($row_cart = $res_cart->fetch_assoc()) {
    $total_keranjang = $row_cart['total'] ? $row_cart['total'] : 0;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Riwayat Pembelian - Smart SMANSABAYA</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; -webkit-tap-highlight-color: transparent; }
        body { background-color: #F8F9FD; padding-bottom: 100px; overflow-x: hidden; }
        a { text-decoration: none; }

        /* HEADER */
        .simple-header {
            background-color: #00A859; padding: 20px 20px 30px 20px;
            border-bottom-left-radius: 30px; border-bottom-right-radius: 30px;
            color: white; box-shadow: 0 10px 25px rgba(0, 168, 89, 0.25);
            text-align: center; position: sticky; top: 0; z-index: 50;
        }
        .page-title { font-size: 18px; font-weight: 700; }

        /* CONTENT */
        .main-content { padding: 20px; margin-top: -10px; }

        /* CARD RIWAYAT */
        .history-card {
            background: white; border-radius: 18px; padding: 16px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.03); margin-bottom: 15px;
            border-left: 5px solid #00A859; 
        }
        
        .card-header {
            display: flex; justify-content: space-between; align-items: start;
            border-bottom: 1px dashed #eee; padding-bottom: 10px; margin-bottom: 10px;
        }
        
        .order-date { font-size: 11px; color: #888; display: flex; align-items: center; gap: 5px; }
        .order-id { font-size: 12px; font-weight: 600; color: #333; margin-bottom: 2px;}

        .status-badge {
            font-size: 10px; font-weight: 700; color: #00A859;
            background-color: #E8F8F0;
            padding: 4px 10px; border-radius: 20px;
            text-transform: uppercase; letter-spacing: 0.5px;
        }

        .item-list { margin-bottom: 12px; }
        .item-row { 
            display: flex; justify-content: space-between; 
            font-size: 13px; color: #555; margin-bottom: 4px; 
        }
        .item-qty { font-weight: 600; color: #333; margin-right: 5px; }

        .card-footer {
            display: flex; justify-content: space-between; align-items: center;
            padding-top: 5px;
        }
        .total-label { font-size: 11px; color: #888; }
        .total-price { font-size: 15px; font-weight: 700; color: #00A859; }

        .btn-reorder {
            background-color: #00A859; color: white;
            padding: 6px 14px; border-radius: 8px; font-size: 11px; font-weight: 600;
            box-shadow: 0 4px 10px rgba(0, 168, 89, 0.2);
        }
        .btn-reorder:active { transform: scale(0.95); }

        /* EMPTY STATE */
        .empty-history { text-align: center; padding: 50px 20px; color: #aaa; }
        .empty-history i { font-size: 50px; margin-bottom: 15px; color: #ddd; }

        /* NAVBAR */
        .bottom-navbar {
            position: fixed; bottom: 0; left: 0; width: 100%; background-color: white; height: 75px;
            display: flex; justify-content: space-between; padding: 0 20px;
            border-top-left-radius: 25px; border-top-right-radius: 25px;
            box-shadow: 0 -5px 30px rgba(0,0,0,0.08); z-index: 100;
        }
        .nav-link { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #C4C4C4; font-size: 11px; font-weight: 500;}
        .nav-link i { font-size: 22px; margin-bottom: 6px; }
        .nav-link.active { color: #00A859; font-weight: 700; }
        
        .nav-center-wrapper { position: relative; width: 60px; display: flex; justify-content: center; }
        .nav-fab {
            position: absolute; top: -30px; width: 64px; height: 64px; 
            background: linear-gradient(135deg, #00C870, #00A859); border-radius: 50%;
            display: flex; align-items: center; justify-content: center; color: white; font-size: 26px;
            box-shadow: 0 10px 20px rgba(0, 168, 89, 0.4); border: 5px solid #F8F9FD;
        }
        .cart-badge {
            position: absolute; top: -25px; right: 0;
            background-color: #FF4757; color: white; font-size: 10px; font-weight: bold;
            width: 20px; height: 20px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid white;
        }
    </style>
</head>
<body>

    <header class="simple-header">
        <div class="page-title">Riwayat Pembelian</div>
    </header>

    <main class="main-content">
        
        <?php if (empty($riwayat_pembelian)): ?>
            <div class="empty-history">
                <i class="fas fa-receipt"></i>
                <p>Belum ada pembelian yang selesai.</p>
                <a href="home.php" style="color: #00A859; font-weight: 600; font-size: 12px; margin-top: 10px; display: inline-block;">Belanja Sekarang</a>
            </div>
        <?php else: ?>
            
            <?php foreach ($riwayat_pembelian as $order): ?>
                <div class="history-card">
                    <div class="card-header">
                        <div>
                            <div class="order-id"><?= $order['id_order'] ?></div>
                            <div class="order-date">
                                <i class="far fa-calendar-check"></i> <?= $order['tanggal'] ?>
                            </div>
                        </div>
                        <span class="status-badge">
                            <i class="fas fa-check-circle" style="margin-right:3px;"></i> SELESAI
                        </span>
                    </div>

                    <div class="item-list">
                        <?php foreach($order['items'] as $item): ?>
                        <div class="item-row">
                            <span><span class="item-qty"><?= $item['qty'] ?>x</span> <?= $item['nama'] ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="card-footer">
                        <div>
                            <div class="total-label">Total Bayar</div>
                            <div class="total-price">Rp <?= number_format($order['total'], 0, ',', '.') ?></div>
                        </div>
                        <a href="home.php" class="btn-reorder">Beli Lagi</a>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>

    </main>

    <nav class="bottom-navbar">
        <a href="home.php" class="nav-link"><i class="fas fa-home"></i><span>Beranda</span></a>
        
        <a href="pesanan.php" class="nav-link"><i class="fas fa-receipt"></i><span>Pesanan</span></a>
        
        <div class="nav-center-wrapper">
            <a href="keranjang_belanja.php" class="nav-fab">
                <i class="fas fa-shopping-basket"></i>
            </a>
            <?php if($total_keranjang > 0): ?>
                <div class="cart-badge"><?= $total_keranjang ?></div>
            <?php endif; ?>
        </div>

        <a href="riwayat.php" class="nav-link active"><i class="fas fa-history"></i><span>Riwayat</span></a>
        
        <a href="profile_user.php" class="nav-link"><i class="fas fa-user"></i><span>Profil</span></a>
    </nav>

</body>
</html>