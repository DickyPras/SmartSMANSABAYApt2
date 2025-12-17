<?php
require_once '../../config/koneksi.php';

// 1. Generate Nomor Invoice Otomatis (Format: TRX-20251217-XXXX)
$no_invoice_otomatis = 'TRX-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 4));

$error = '';
$success = false;

// 2. Ambil daftar anggota (siswa) untuk dropdown
$list_anggota = mysqli_query($koneksi, "SELECT id_user, nama FROM users WHERE role = 'siswa' ORDER BY nama ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $id_user     = (int)($_POST['id_user'] ?? 0);
    $tanggal     = $_POST['tanggal'] ?? date('Y-m-d H:i:s');
    $total_harga = (int)($_POST['total_harga'] ?? 0);
    $status      = mysqli_real_escape_string($koneksi, $_POST['status'] ?? 'pending');

    if ($id_user > 0 && $total_harga > 0) {
        // Query INSERT sesuai struktur tabel 'transaksi' di database
        // Kolom: id_transaksi (AI), id_user, tanggal, total_harga, status
        $sql = "INSERT INTO transaksi (id_user, tanggal, total_harga, status) 
                VALUES ('$id_user', '$tanggal', '$total_harga', '$status')";

        if (mysqli_query($koneksi, $sql)) {
            header('Location: ../../views/admin/transaksi.php?success=1');
            exit;
        } else {
            $error = "Gagal menyimpan transaksi: " . mysqli_error($koneksi);
        }
    } else {
        $error = "Pilih anggota dan masukkan total harga dengan benar.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Transaksi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Poppins', 'sans-serif'] },
                    colors: { 'primary': '#FACC15', 'bg-soft': '#F0FDF4' }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto bg-bg-soft min-h-screen relative shadow-2xl overflow-hidden flex flex-col">
        <div class="bg-primary px-6 pt-8 pb-10 rounded-b-[2.5rem] shadow-sm z-10">
            <div class="flex items-center justify-between mb-6">
                <a href="../../views/admin/transaksi.php" class="bg-white/20 p-2 rounded-xl hover:bg-white/40 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-xl font-bold text-gray-900">Transaksi Baru</h1>
                <div class="w-10"></div>
            </div>
            <p class="text-sm text-gray-700 font-medium italic">Catat transaksi penjualan ke database.</p>
        </div>

        <div class="flex-1 px-6 pt-6 pb-12 space-y-6 overflow-y-auto">
            <?php if ($error): ?>
                <div class="bg-red-100 text-red-700 px-4 py-3 rounded-2xl text-xs font-semibold"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">No. Invoice</label>
                    <input type="text" value="<?= $no_invoice_otomatis ?>" readonly class="w-full px-4 py-3 rounded-2xl border border-gray-100 bg-gray-50 text-gray-500 font-mono text-sm focus:outline-none cursor-not-allowed">
                    <p class="text-[10px] text-gray-400 mt-1 italic">*Dihasilkan secara otomatis oleh sistem</p>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-1">Anggota (Siswa)</label>
                    <select name="id_user" required class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white appearance-none">
                        <option value="">-- Pilih Siswa --</option>
                        <?php while($agt = mysqli_fetch_assoc($list_anggota)): ?>
                            <option value="<?= $agt['id_user'] ?>"><?= htmlspecialchars($agt['nama']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-1">Tanggal & Waktu</label>
                    <input type="datetime-local" name="tanggal" required value="<?= date('Y-m-d\TH:i') ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-1">Total Harga (Rp)</label>
                    <input type="number" name="total_harga" min="0" required placeholder="0" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary/80 focus:outline-none shadow-sm bg-white font-bold text-lg text-gray-800">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Status Pembayaran</label>
                    <div class="flex gap-3">
                        <label class="flex-1 border rounded-2xl px-4 py-4 text-center cursor-pointer transition-all hover:bg-white has-[:checked]:bg-gray-900 has-[:checked]:text-white">
                            <input type="radio" name="status" value="dibayar" class="hidden" checked>
                            <span class="text-sm font-bold">LUNAS</span>
                        </label>
                        <label class="flex-1 border rounded-2xl px-4 py-4 text-center cursor-pointer transition-all hover:bg-white has-[:checked]:bg-gray-900 has-[:checked]:text-white">
                            <input type="radio" name="status" value="pending" class="hidden">
                            <span class="text-sm font-bold">PENDING</span>
                        </label>
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full bg-gray-900 text-white py-4 rounded-3xl shadow-xl hover:bg-gray-800 transition transform active:scale-95 font-bold">
                        Simpan Transaksi
                    </button>
                    <a href="../../views/admin/transaksi.php" class="block text-center mt-4 text-sm text-gray-400 font-medium">Batalkan</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>