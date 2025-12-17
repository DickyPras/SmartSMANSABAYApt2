<?php
session_start();

// --- 1. KONEKSI KE DATABASE ---
require_once __DIR__ . '/../../../config/koneksi.php';

// Deteksi variabel koneksi
$db_conn = isset($conn) ? $conn : (isset($koneksi) ? $koneksi : null);

if (!$db_conn) {
    die("Error: Koneksi database gagal. Pastikan file config/koneksi.php ada dan konfigurasi benar.");
}

// --- 2. AMBIL DATA PRODUK DARI DATABASE ---
$products = [];

$sql = "SELECT 
            b.id_barang, 
            b.nama_barang, 
            b.harga, 
            b.gambar, 
            k.nama_kategori,
            COALESCE((SELECT SUM(jumlah) FROM stok_barang WHERE id_barang = b.id_barang), 0) as total_stok
        FROM barang b
        JOIN kategori_barang k ON b.id_kategori = k.id_kategori
        ORDER BY b.id_barang ASC";

$result = mysqli_query($db_conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // --- Normalisasi Kategori ---
        $cat_lower = strtolower($row['nama_kategori']);
        $kategori_final = 'Makanan'; 

        if (strpos($cat_lower, 'minum') !== false || strpos($cat_lower, 'susu') !== false || strpos($cat_lower, 'kopi') !== false || strpos($cat_lower, 'teh') !== false || strpos($cat_lower, 'air') !== false) {
            $kategori_final = 'Minuman';
        } elseif (strpos($cat_lower, 'beku') !== false || strpos($cat_lower, 'es krim') !== false || strpos($cat_lower, 'frozen') !== false) {
            $kategori_final = 'Makanan Beku';
        } 
        
        // --- Path Gambar ---
        $img_src = !empty($row['gambar']) 
            ? '../../../public/images/product/img_produk/' . $row['gambar'] 
            : 'https://placehold.co/200x200?text=No+Image';

        $products[] = [
            'id'       => $row['id_barang'],
            'name'     => $row['nama_barang'],
            'price'    => $row['harga'],
            'image'    => $img_src,
            'stock'    => $row['total_stok'],
            'category' => $kategori_final
        ];
    }
}

// --- 3. LOGIKA TAMBAH KERANJANG ---
if (isset($_POST['tambah_keranjang'])) {
    $id_produk = $_POST['product_id'];
    $current_params = $_GET;
    unset($current_params['tambah_keranjang']);
    $query_string = http_build_query($current_params);

    $produk_terpilih = null;
    foreach ($products as $p) {
        if ($p['id'] == $id_produk) {
            $produk_terpilih = $p;
            break;
        }
    }

    if ($produk_terpilih) {
        if (isset($_SESSION['keranjang'][$id_produk])) {
            $_SESSION['keranjang'][$id_produk]['qty'] += 1;
        } else {
            $_SESSION['keranjang'][$id_produk] = [
                'nama'   => $produk_terpilih['name'],
                'harga'  => $produk_terpilih['price'],
                'gambar' => $produk_terpilih['image'], // Simpan path gambar
                'qty'    => 1
            ];
        }
        $_SESSION['notif'] = "Berhasil menambahkan " . $produk_terpilih['name'];
        header("Location: home.php?" . $query_string);
        exit;
    }
}

// --- 4. LOGIKA FILTER ---
$filtered_products = [];
$kategori_aktif = isset($_GET['kategori']) ? $_GET['kategori'] : 'all';
$keyword        = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$judul_section  = "Rekomendasi Menu";
$mapKategori    = ['makanan' => 'Makanan', 'minuman' => 'Minuman', 'frozen'  => 'Makanan Beku'];

if (!empty($products)) {
    foreach ($products as $p) {
        $lolos_kategori = true;
        $lolos_keyword  = true;

        if ($kategori_aktif != 'all' && isset($mapKategori[$kategori_aktif])) {
            $target_cat = $mapKategori[$kategori_aktif];
            $judul_section = $target_cat;
            if ($p['category'] !== $target_cat) $lolos_kategori = false;
        }

        if (!empty($keyword)) {
            if (stripos($p['name'], $keyword) === false) $lolos_keyword = false;
            $judul_section = "Hasil pencarian: \"" . htmlspecialchars($keyword) . "\"";
        }

        if ($lolos_kategori && $lolos_keyword) $filtered_products[] = $p;
    }
}

$total_keranjang = 0;
if (isset($_SESSION['keranjang'])) {
    foreach ($_SESSION['keranjang'] as $item) $total_keranjang += $item['qty'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Smart SMANSABAYA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* CSS LENGKAP */
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; -webkit-tap-highlight-color:transparent; }
        body { background-color:#F8F9FD; padding-bottom:100px; overflow-x:hidden; }
        a { text-decoration:none; }
        button { border:none; outline:none; background:none; cursor:pointer; }
        .header { background-color:#00A859; padding:25px 20px 30px; border-bottom-left-radius:30px; border-bottom-right-radius:30px; color:white; box-shadow:0 10px 25px rgba(0,168,89,0.25); position:relative; z-index:10; }
        .header-top { display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; }
        .user-info h4 { font-size:13px; font-weight:400; opacity:0.9; margin-bottom:2px; }
        .user-info h2 { font-size:20px; font-weight:700; letter-spacing:-0.5px; }
        .btn-header-icon { background:rgba(255,255,255,0.2); width:45px; height:45px; border-radius:14px; display:flex; align-items:center; justify-content:center; backdrop-filter:blur(10px); border:1px solid rgba(255,255,255,0.1); }
        .search-wrapper { position:relative; width:100%; }
        .search-input { width:100%; padding:16px 20px 16px 50px; border-radius:18px; border:none; outline:none; font-size:14px; color:#444; background-color:#fff; box-shadow:0 8px 20px rgba(0,0,0,0.06); }
        .search-icon { position:absolute; left:18px; top:50%; transform:translateY(-50%); color:#00A859; font-size:18px; pointer-events:none; }
        .main-content { padding:25px 20px; }
        .promo-card { background:linear-gradient(120deg, #00C870, #00A859); border-radius:24px; padding:22px; color:white; display:flex; justify-content:space-between; align-items:center; position:relative; overflow:hidden; box-shadow:0 12px 25px rgba(0,168,89,0.3); margin-bottom:30px; }
        .badge-promo { background-color:#FFF; color:#00A859; padding:5px 12px; border-radius:8px; font-size:11px; font-weight:800; display:inline-block; margin-bottom:8px; }
        .promo-bg-icon { position:absolute; right:-15px; bottom:-25px; font-size:90px; opacity:0.2; color:#fff; transform:rotate(-10deg); }
        .section-header { display:flex; justify-content:space-between; align-items:end; margin-bottom:15px; margin-top:10px; }
        .section-title { font-size:18px; font-weight:700; color:#2d3436; }
        .section-link { font-size:12px; font-weight:600; color:#00A859; }
        .category-grid { display:grid; grid-template-columns:repeat(3, 1fr); gap:12px; margin-bottom:30px; }
        .cat-card { position:relative; height:110px; border-radius:20px; padding:15px; display:flex; flex-direction:column; justify-content:flex-end; text-align:left; overflow:hidden; box-shadow:0 8px 15px rgba(0,0,0,0.05); transition:transform 0.1s; }
        .cat-card:active { transform:scale(0.96); }
        .cat-title { color:white; font-size:13px; font-weight:700; z-index:2; line-height:1.3; text-shadow:0 2px 4px rgba(0,0,0,0.1); }
        .cat-food { background:linear-gradient(135deg, #FF9F43 0%, #FF6B6B 100%); }
        .cat-frozen { background:linear-gradient(135deg, #48DBFB 0%, #0ABDE3 100%); }
        .cat-drink { background:linear-gradient(135deg, #FDA7DF 0%, #9980FA 100%); }
        .cat-icon-bg { position:absolute; top:-10px; right:-15px; font-size:60px; color:white; opacity:0.25; transform:rotate(15deg); z-index:1; }
        .cat-icon-float { position:absolute; top:12px; left:12px; background:rgba(255,255,255,0.25); width:32px; height:32px; border-radius:10px; display:flex; align-items:center; justify-content:center; color:white; font-size:14px; backdrop-filter:blur(5px); z-index:2; border:1px solid rgba(255,255,255,0.2); }
        .product-grid { display:grid; grid-template-columns:repeat(2, 1fr); gap:15px; }
        .product-card { background:white; border-radius:18px; padding:12px; box-shadow:0 5px 15px rgba(0,0,0,0.03); display:flex; flex-direction:column; position:relative; }
        .product-img-box { width:100%; height:120px; background-color:#f0f0f0; border-radius:14px; margin-bottom:10px; overflow:hidden; display:flex; align-items:center; justify-content:center; }
        .product-img-box img { width:100%; height:100%; object-fit:cover; }
        .product-name { font-size:13px; font-weight:600; color:#333; margin-bottom:4px; line-height:1.3; display:-webkit-box; -webkit-box-orient:vertical; overflow:hidden; height:34px; }
        .product-meta { display:flex; justify-content:space-between; align-items:center; margin-top:5px; }
        .product-price { font-size:14px; font-weight:700; color:#00A859; }
        .btn-add { width:32px; height:32px; background-color:#00A859; border-radius:10px; display:flex; align-items:center; justify-content:center; color:white; font-size:14px; transition:transform 0.1s; }
        .btn-add:active { transform:scale(0.9); background-color:#008954; }
        .stock-label { font-size:10px; color:#999; margin-bottom:2px; }
        .empty-state { text-align:center; color:#999; padding:20px; font-size:12px; width:100%; grid-column:span 2; }
        .bottom-navbar { position:fixed; bottom:0; left:0; width:100%; background-color:white; height:75px; display:flex; justify-content:space-between; padding:0 20px; border-top-left-radius:25px; border-top-right-radius:25px; box-shadow:0 -5px 30px rgba(0,0,0,0.08); z-index:100; }
        .nav-link { flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#C4C4C4; font-size:11px; font-weight:500; }
        .nav-link i { font-size:22px; margin-bottom:6px; }
        .nav-link.active { color:#00A859; font-weight:700; }
        .nav-center-wrapper { position:relative; width:60px; display:flex; justify-content:center; }
        .nav-fab { position:absolute; top:-30px; width:64px; height:64px; background:linear-gradient(135deg, #00C870, #00A859); border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; font-size:26px; box-shadow:0 10px 20px rgba(0,168,89,0.4); border:5px solid #F8F9FD; }
        .cart-badge { position:absolute; top:-25px; right:0; background-color:#FF4757; color:white; font-size:10px; font-weight:bold; width:20px; height:20px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2px solid white; }
    </style>
</head>
<body>

    <header class="header">
        <div class="header-top">
            <div class="user-info">
                <h4>Selamat Datang,</h4>
                <h2>Siswa SMANSABAYA</h2>
            </div>
            <div class="btn-header-icon"><i class="fas fa-bell" style="color:white;"></i></div>
        </div>
        <form method="GET" action="home.php" class="search-wrapper">
            <?php if ($kategori_aktif != 'all'): ?>
                <input type="hidden" name="kategori" value="<?= htmlspecialchars($kategori_aktif) ?>">
            <?php endif; ?>
            <i class="fas fa-search search-icon"></i>
            <input type="text" name="keyword" class="search-input" placeholder="Mau jajan apa hari ini?" value="<?= htmlspecialchars($keyword) ?>" autocomplete="off">
        </form>
    </header>

    <main class="main-content">
        <div class="promo-card">
            <div class="promo-details">
                <span class="badge-promo">PROMO KANTIN</span>
                <h3 style="font-size:20px; font-weight:700; line-height:1.2; margin-bottom:5px;">Paket Hemat<br>Istirahat</h3>
                <p style="font-size:12px; opacity:0.9;">Diskon 10% pada hari jum'at dengan pembelian minimal 3 barang</p>
            </div>
            <i class="fas fa-hamburger promo-bg-icon"></i>
        </div>

        <div class="section-header">
            <span class="section-title">Kategori Menu</span>
            <a href="home.php" class="section-link">Reset Filter</a>
        </div>

        <div class="category-grid">
            <a href="?kategori=makanan<?= !empty($keyword)?'&keyword='.$keyword:'' ?>" class="cat-card cat-food">
                <div class="cat-icon-float"><i class="fas fa-utensils"></i></div>
                <i class="fas fa-bowl-food cat-icon-bg"></i>
                <span class="cat-title">Makanan<br>Berat</span>
            </a>
            <a href="?kategori=frozen<?= !empty($keyword)?'&keyword='.$keyword:'' ?>" class="cat-card cat-frozen">
                <div class="cat-icon-float"><i class="fas fa-snowflake"></i></div>
                <i class="fas fa-icicles cat-icon-bg"></i>
                <span class="cat-title">Makanan<br>Beku</span>
            </a>
            <a href="?kategori=minuman<?= !empty($keyword)?'&keyword='.$keyword:'' ?>" class="cat-card cat-drink">
                <div class="cat-icon-float"><i class="fas fa-glass-water"></i></div>
                <i class="fas fa-mug-hot cat-icon-bg"></i>
                <span class="cat-title">Aneka<br>Minuman</span>
            </a>
        </div>

        <div class="section-header">
            <span class="section-title"><?= htmlspecialchars($judul_section) ?></span>
        </div>

        <div class="product-grid">
            <?php if (empty($filtered_products)): ?>
                <div class="empty-state">
                    <i class="fas fa-search" style="font-size:30px; margin-bottom:10px;"></i><br>
                    <?php if (empty($products)): ?>
                        Gagal memuat data produk atau data kosong.<br>Pastikan tabel 'barang' dan 'stok_barang' terisi.
                    <?php else: ?>
                        Produk tidak ditemukan.<br><a href="home.php" style="color:#00A859; font-weight:bold; margin-top:5px; display:inline-block;">Lihat Semua Menu</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php foreach ($filtered_products as $item): ?>
                    <div class="product-card">
                        <a href="detail_produk.php?id=<?= $item['id'] ?>" class="product-img-box">
                            <img src="<?= $item['image'] ?>" alt="<?= $item['name'] ?>" onerror="this.src='https://placehold.co/200x200?text=No+Image'">
                        </a>

                        <div class="stock-label">Stok: <?= $item['stock'] ?></div>
                        
                        <a href="detail_produk.php?id=<?= $item['id'] ?>" class="product-name" style="text-decoration:none;">
                            <?= $item['name'] ?>
                        </a>

                        <div class="product-meta">
                            <div class="product-price">Rp <?= number_format($item['price'], 0, ',', '.') ?></div>
                            <form method="POST" action="">
                                <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                <button type="submit" name="tambah_keranjang" class="btn-add"><i class="fas fa-plus"></i></button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <nav class="bottom-navbar">
        <a href="home.php" class="nav-link active"><i class="fas fa-home"></i><span>Beranda</span></a>
        <a href="pesanan.php" class="nav-link"><i class="fas fa-receipt"></i><span>Pesanan</span></a>
        <div class="nav-center-wrapper">
            <a href="keranjang_belanja.php" class="nav-fab"><i class="fas fa-shopping-basket"></i></a>
            <?php if ($total_keranjang > 0): ?><div class="cart-badge"><?= $total_keranjang ?></div><?php endif; ?>
        </div>
        <a href="riwayat.php" class="nav-link"><i class="fas fa-history"></i><span>Riwayat</span></a>
        <a href="profile_user.php" class="nav-link"><i class="fas fa-user"></i><span>Profil</span></a>
    </nav>

    <?php if (isset($_SESSION['notif'])): ?>
    <script>
        Swal.fire({
            icon: 'success', title: 'Berhasil!', text: '<?= $_SESSION['notif'] ?>', showConfirmButton: false, timer: 1500, toast: true, position: 'top-end', background: '#00A859', color: '#fff'
        });
    </script>
    <?php unset($_SESSION['notif']); endif; ?>
</body>
</html>