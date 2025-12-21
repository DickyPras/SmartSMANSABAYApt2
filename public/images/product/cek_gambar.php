<?php
require_once __DIR__ . '/../config/koneksi.php';

$db_conn = isset($conn) ? $conn : (isset($koneksi) ? $koneksi : null);

if (!$db_conn) {
    die("Koneksi database gagal.");
}

echo "<h2>Cek Data Gambar Produk</h2>";
echo "<style>table { border-collapse: collapse; width: 100%; } th, td { border: 1px solid #ddd; padding: 8px; text-align: left; } th { background-color: #4CAF50; color: white; }</style>";

$sql = "SELECT id_barang, nama_barang, gambar FROM barang ORDER BY id_barang ASC";
$result = mysqli_query($db_conn, $sql);

if ($result) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Nama Produk</th><th>Nama File Gambar</th><th>Status</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        $status = empty($row['gambar']) ? '<span style="color: red;">❌ KOSONG</span>' : '<span style="color: green;">✅ Ada</span>';
        $gambar_path = !empty($row['gambar']) ? __DIR__ . '/../public/images/product/img_produk/' . $row['gambar'] : '';
        $file_exists = !empty($gambar_path) && file_exists($gambar_path) ? '<span style="color: green;">✅ File Ada</span>' : '<span style="color: red;">❌ File Tidak Ada</span>';
        
        echo "<tr>";
        echo "<td>" . $row['id_barang'] . "</td>";
        echo "<td>" . htmlspecialchars($row['nama_barang']) . "</td>";
        echo "<td>" . ($row['gambar'] ?: '-') . "</td>";
        echo "<td>" . $status . " | " . $file_exists . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Hitung statistik
    mysqli_data_seek($result, 0);
    $total = 0;
    $ada_gambar = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $total++;
        if (!empty($row['gambar'])) {
            $ada_gambar++;
        }
    }
    
    echo "<br><h3>Statistik:</h3>";
    echo "<p>Total Produk: <b>$total</b></p>";
    echo "<p>Produk dengan Gambar: <b>$ada_gambar</b></p>";
    echo "<p>Produk tanpa Gambar: <b>" . ($total - $ada_gambar) . "</b></p>";
} else {
    echo "Error: " . mysqli_error($db_conn);
}
