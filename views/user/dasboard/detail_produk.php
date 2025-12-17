<?php
session_start();
require_once __DIR__ . '/../../../config/koneksi.php';

$db_conn = isset($conn) ? $conn : (isset($koneksi) ? $koneksi : null);
if (!$db_conn) { die("Error: Koneksi database gagal."); }

$id_produk = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT 
            b.*, 
            k.nama_kategori, 
            k.deskripsi as deskripsi_kategori,
            COALESCE((SELECT SUM(jumlah) FROM stok_barang WHERE id_barang = b.id_barang), 0) as total_stok
        FROM barang b
        JOIN kategori_barang k ON b.id_kategori = k.id_kategori
        WHERE b.id_barang = ?";

$stmt = mysqli_prepare($db_conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_produk);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$produk = mysqli_fetch_assoc($result);

if (!$produk) {
    echo "<script>alert('Produk tidak ditemukan!'); window.location='home.php';</script>";
    exit;
}

$img_src = !empty($produk['gambar']) 
    ? '../../../public/images/product/img_produk/' . $produk['gambar'] 
    : 'https://placehold.co/400x400?text=No+Image';

if (isset($_POST['tambah_keranjang'])) {
    $qty_beli = (int)$_POST['qty'];
    
    if ($qty_beli > $produk['total_stok']) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({ icon: 'error', title: 'Stok Kurang', text: 'Maaf, stok tidak mencukupi permintaan Anda.' });
            });
        </script>";
    } else {
        if (isset($_SESSION['keranjang'][$id_produk])) {
            $_SESSION['keranjang'][$id_produk]['qty'] += $qty_beli;
        } else {
            $_SESSION['keranjang'][$id_produk] = [
                'nama'   => $produk['nama_barang'],
                'harga'  => $produk['harga'],
                'gambar' => $produk['gambar'],
                'qty'    => $qty_beli
            ];
        }
        $_SESSION['notif'] = "Berhasil menambahkan " . $produk['nama_barang'];
        header("Location: home.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Detail - <?= htmlspecialchars($produk['nama_barang']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --primary: #00A859;
            --primary-dark: #008f4c;
            --bg-color: #F2F4F8;
            --white: #ffffff;
            --text-dark: #1F2937;
            --text-grey: #6B7280;
        }

        * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; -webkit-tap-highlight-color: transparent;}
        body { background-color: var(--bg-color); padding-bottom: 100px; color: var(--text-dark); overflow-x: hidden; }

        /* --- ANIMATIONS KEYFRAMES --- */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes popIn {
            0% { opacity: 0; transform: scale(0.8); }
            100% { opacity: 1; transform: scale(1); }
        }
        @keyframes slideUpBar {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
        }

        /* --- HERO SECTION --- */
        .product-hero {
            position: relative;
            width: 100%;
            height: 42vh;
            background-color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            padding-top: 40px;
            /* Animasi Fade In Gambar */
            animation: popIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        /* TOMBOL BACK DENGAN ANIMASI */
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 45px;
            height: 45px;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(5px);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-dark);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            z-index: 10;
            text-decoration: none;
            /* Transisi halus */
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            animation: fadeInDown 0.5s ease-out 0.2s backwards; /* Muncul dari atas */
        }
        
        /* Efek Hover & Klik Tombol Back */
        .back-button:hover {
            transform: scale(1.1) rotate(-10deg); /* Membesar & miring dikit */
            box-shadow: 0 8px 20px rgba(0, 168, 89, 0.2);
            color: var(--primary);
        }
        .back-button:active {
            transform: scale(0.9); /* Mengecil saat ditekan */
        }

        .hero-img {
            max-width: 85%;
            max-height: 85%;
            object-fit: contain;
            filter: drop-shadow(0 15px 25px rgba(0,0,0,0.1));
            transition: transform 0.3s ease;
        }
        
        /* --- CONTENT SHEET --- */
        .content-sheet {
            margin-top: -35px;
            background-color: var(--white);
            border-top-left-radius: 40px;
            border-top-right-radius: 40px;
            padding: 35px 25px;
            position: relative;
            z-index: 5;
            min-height: 60vh;
            box-shadow: 0 -10px 40px rgba(0,0,0,0.05);
            /* Animasi Konten Naik */
            animation: fadeInUp 0.7s ease-out 0.1s backwards;
        }

        .category-tag {
            background-color: #E6F7EF;
            color: var(--primary);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
            margin-bottom: 12px;
        }

        .product-title {
            font-size: 26px;
            font-weight: 800;
            line-height: 1.2;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .product-price {
            font-size: 28px;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: -0.5px;
        }

        .stock-info {
            display: flex;
            gap: 12px;
            margin: 25px 0;
            padding-bottom: 25px;
            border-bottom: 1px dashed #eee;
        }

        .info-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--text-grey);
            background: #F9FAFB;
            padding: 10px 15px;
            border-radius: 14px;
            border: 1px solid #eee;
        }
        .info-pill i { color: var(--primary); font-size: 16px; }

        .desc-section h3 {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--text-dark);
        }

        .desc-text {
            font-size: 14px;
            line-height: 1.8;
            color: var(--text-grey);
            text-align: justify;
        }

        /* --- BOTTOM ACTION BAR (ANIMATED) --- */
        .bottom-action-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: var(--white);
            padding: 15px 25px 25px 25px;
            box-shadow: 0 -10px 40px rgba(0,0,0,0.08);
            border-top-left-radius: 30px;
            border-top-right-radius: 30px;
            z-index: 100;
            display: flex;
            align-items: center;
            gap: 20px;
            /* Animasi Slide Up dari Bawah */
            animation: slideUpBar 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.3s backwards;
        }

        .qty-control {
            display: flex;
            align-items: center;
            background: #F3F4F6;
            border-radius: 18px;
            padding: 5px;
        }

        .qty-btn {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            background: var(--white);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: var(--text-dark);
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            cursor: pointer;
            transition: all 0.2s;
        }
        .qty-btn:active { transform: scale(0.85); background: #eee; }

        .qty-input {
            width: 45px;
            text-align: center;
            border: none;
            background: transparent;
            font-weight: 700;
            font-size: 18px;
            color: var(--text-dark);
        }

        /* TOMBOL TAMBAH DENGAN ANIMASI */
        .add-cart-btn {
            flex: 1;
            background: var(--primary);
            color: white;
            border: none;
            padding: 18px;
            border-radius: 20px;
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 10px 20px rgba(0, 168, 89, 0.3);
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
        }
        
        /* Efek Hover Tombol Tambah */
        .add-cart-btn:hover {
            transform: translateY(-3px); /* Naik sedikit */
            box-shadow: 0 15px 30px rgba(0, 168, 89, 0.5);
            background-color: #009650;
        }
        
        /* Efek Klik Tombol Tambah */
        .add-cart-btn:active {
            transform: scale(0.95); /* Mengecil saat ditekan */
            box-shadow: 0 5px 10px rgba(0, 168, 89, 0.3);
        }

    </style>
</head>
<body>

    <div class="product-hero">
        <a href="home.php" class="back-button">
            <i class="fas fa-arrow-left"></i>
        </a>
        <img src="<?= $img_src ?>" alt="<?= htmlspecialchars($produk['nama_barang']) ?>" class="hero-img">
    </div>

    <div class="content-sheet">
        <span class="category-tag"><?= htmlspecialchars($produk['nama_kategori']) ?></span>
        
        <h1 class="product-title"><?= htmlspecialchars($produk['nama_barang']) ?></h1>
        <div class="product-price">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></div>

        <div class="stock-info">
            <div class="info-pill">
                <i class="fas fa-cubes"></i>
                <span>Stok: <b><?= $produk['total_stok'] ?></b> <?= $produk['satuan'] ?></span>
            </div>
            <div class="info-pill">
                <i class="fas fa-check-circle"></i>
                <span>Siap Saji</span>
            </div>
        </div>

        <div class="desc-section">
            <h3>Deskripsi Produk</h3>
            <p class="desc-text">
                <?= !empty($produk['deskripsi_kategori']) 
                    ? $produk['deskripsi_kategori'] 
                    : 'Nikmati kelezatan ' . $produk['nama_barang'] . ' yang segar dan higienis. Tersedia di kantin sekolah dengan kualitas terbaik untuk menemani jam istirahatmu.' ?>
            </p>
        </div>
        
        <div style="height: 100px;"></div> </div>

    <form method="POST" class="bottom-action-bar">
        
        <div class="qty-control">
            <button type="button" class="qty-btn" onclick="updateQty(-1)">
                <i class="fas fa-minus"></i>
            </button>
            <input type="text" name="qty" id="qty" value="1" class="qty-input" readonly>
            <button type="button" class="qty-btn" onclick="updateQty(1)">
                <i class="fas fa-plus"></i>
            </button>
        </div>

        <button type="submit" name="tambah_keranjang" class="add-cart-btn">
            <i class="fas fa-shopping-basket"></i>
            <span>Tambah</span>
        </button>

    </form>

    <script>
        const maxStock = <?= $produk['total_stok'] ?>;
        const qtyInput = document.getElementById('qty');

        function updateQty(change) {
            let currentVal = parseInt(qtyInput.value);
            let newVal = currentVal + change;
            
            if (newVal >= 1 && newVal <= maxStock) {
                qtyInput.value = newVal;
                // Efek animasi kecil pada angka
                qtyInput.style.transform = "scale(1.2)";
                setTimeout(() => qtyInput.style.transform = "scale(1)", 100);
            } else if (newVal > maxStock) {
                // Efek getar jika melebihi stok
                qtyInput.style.color = "red";
                setTimeout(() => qtyInput.style.color = "var(--text-dark)", 200);
            }
        }
    </script>

</body>
</html>