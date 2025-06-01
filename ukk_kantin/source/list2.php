<?php
session_start();
include 'koneksi.php';

$current_page = basename($_SERVER['PHP_SELF']);

// Ambil data produk dan kelompokkan berdasarkan warung
$query_produk = $conn->query("SELECT * FROM produk INNER JOIN warung ON produk.warung_id = warung.warung_id ORDER BY warung.warung_nama, produk.produk_nama");

$kantin_data = [];
if ($query_produk && $query_produk->num_rows > 0) {
    while($row = $query_produk->fetch_assoc()){
        $kantin_data[$row['warung_id']]['nama'] = $row['warung_nama'];
        $kantin_data[$row['warung_id']]['produk'][] = $row;
    }
}

// Ambil data transaksi untuk ditampilkan
$query_transaksi = $conn->query("SELECT * FROM transaksi
    JOIN warung ON transaksi.warung_id = warung.warung_id
    JOIN produk ON transaksi.produk_id = produk.produk_id");

// Hitung total transaksi dan jumlah transaksi aktif untuk footer tabel
$total_transaksi = 0;
$transaksi_aktif = 0;

$query_total = $conn->query("SELECT SUM(transaksi_total) AS total FROM transaksi");
if ($query_total && $row_total = $query_total->fetch_assoc()) {
    $total_transaksi = $row_total['total'] ?? 0;
}

$query_jumlah = $conn->query("SELECT COUNT(*) AS jumlah FROM transaksi");
if ($query_jumlah && $row_jumlah = $query_jumlah->fetch_assoc()) {
    $transaksi_aktif = $row_jumlah['jumlah'] ?? 0;
}

// URL QR code dummy
$qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?data=Total%20Bayar%3A%20Rp" . urlencode(number_format($total_transaksi)) . "&size=200x200";

// Tangani tombol Bayar Sekarang dan Pembayaran Selesai (ini tetap di list2.php karena berkaitan dengan status sesi di halaman ini)
if (isset($_POST['bayar'])) {
    $_SESSION['bayar_ditekan'] = true;
    header("Location: list2.php"); // Redirect untuk mencegah form resubmission
    exit;
}

if (isset($_POST['selesai'])) {
    // untuk menghapus semua transaksi.
    $conn->query("DELETE FROM transaksi"); // Hapus semua transaksi dari DB
    $_SESSION['bayar_ditekan'] = false; // Reset status pembayaran
    header("Location: list2.php"); // Redirect kembali ke halaman list produk
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kantin Telkom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { padding-top: 60px; }
        .card-img-left-custom {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-top-left-radius: var(--bs-card-inner-border-radius);
            border-bottom-left-radius: var(--bs-card-inner-border-radius);
        }
        .card .row.g-0 {
            height: 100%;
        }
        .card .row.g-0 > div {
            display: flex;
            align-items: center;
        }
        .card .row.g-0 > div:first-child {
        align-items: stretch;
        }
        /* Penyesuaian baru untuk jarak */
        .kantin-section {
            margin-bottom: 4rem; /* Menambah jarak antar bagian kantin */
        }
        .produk-card {
            margin-bottom: 1.5rem; /* Menambah jarak antar kartu produk jika diperlukan */
        }
        .nav-link.active {
            text-decoration: underline;
            color: #d9534f !important;
        }
    </style>
    <?php
    $current_page = basename($_SERVER['PHP_SELF']);
    ?>
</head>
<body>

<!-- navbar -->
    <nav class="navbar navbar-expand-lg fixed-top bg-light-subtle border-bottom border-danger border-4">
        <div class="container">
            <!-- logo -->
            <a class="navbar-brand" href="beranda.php">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRJBBUnkE6bLR4vXkghOR5P3sWRF3V3LjA3ZA&s" alt="logo" width="30">
            </a>
            <!-- menu navbar -->
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link"  href="beranda.php">About Kantin</a>
                    </li>
                    <li class="nav-item hover">
                        <a class="nav-link" href="#menu">Cafeteria List</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#ts">How to Buy</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

<section id="menu" class="container mt-5 col-md-8 mx-auto pb-5"> <h2 class="mb-5 text-center">Cafeteria List</h2> <?php if (!empty($kantin_data)): ?>
        <?php foreach ($kantin_data as $warung_id => $data_kantin): ?>
            <div class="mb-5 kantin-section">
                <h3 class="mb-4">Kantin <?= htmlspecialchars($data_kantin['nama']); ?></h3> <div class="row row-cols-1 row-cols-md-2 g-4">
                    <?php foreach ($data_kantin['produk'] as $produk): ?>
                        <div class="col">
                            <div class="card h-100 produk-card">
                                <div class="row g-0">
                                    <div class="col-md-4">
                                        <img src="<?= htmlspecialchars($produk['produk_gambar']); ?>" class="card-img-left-custom" alt="<?= htmlspecialchars($produk['produk_nama']); ?>">
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card-body d-flex flex-column justify-content-center">
                                            <h5 class="card-title"><?= htmlspecialchars($produk['produk_nama']); ?></h5>
                                            <p class="card-text mb-1">Harga: Rp<?= number_format($produk['produk_harga']); ?></p>
                                            <p class="card-text mb-2">Stok: <?= htmlspecialchars($produk['produk_stok']); ?></p>

                                            <form method="post" action="transaksi.php" class="d-flex align-items-center gap-2">
                                                <input type="hidden" name="action" value="tambah">
                                                <input type="hidden" name="produk_id" value="<?= $produk['produk_id']; ?>">
                                                <input type="hidden" name="warung_id" value="<?= $produk['warung_id']; ?>">
                                                <label for="jumlah_<?= $produk['produk_id']; ?>" class="form-label mb-0">Jumlah:</label>
                                                <input type="number" id="jumlah_<?= $produk['produk_id']; ?>" name="jumlah" class="form-control w-25" required placeholder="0" min="1" max="<?= htmlspecialchars($produk['produk_stok']); ?>">
                                                <button type="submit" class="btn btn-success flex-grow-1" <?= isset($_SESSION['bayar_ditekan']) && $_SESSION['bayar_ditekan'] ? 'disabled' : '' ?>>Tambah</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-center">Tidak ada kantin atau produk yang tersedia.</p>
    <?php endif; ?>
</section>
<!-- transaksi -->
<section id='ts' class="container mt-5 pt-4 pb-5"> <h2 class="mb-4">Daftar Transaksi</h2>
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Nama Kantin</th>
                <th>Produk</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Total</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($query_transaksi && $query_transaksi->num_rows > 0): ?>
                <?php $no = 1; while($row = $query_transaksi->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['warung_nama']); ?></td>
                        <td><?= htmlspecialchars($row['produk_nama']); ?></td>
                        <td>Rp<?= number_format($row['produk_harga']); ?></td>
                        <td><?= $row['transaksi_jumlah']; ?></td>
                        <td>Rp<?= number_format($row['transaksi_total']); ?></td>
                        <td>
                            <form method="post" action="transaksi.php" class="d-inline-flex align-items-center gap-1">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="edit_id" value="<?= $row['transaksi_id']; ?>">
                                <input type="number" name="edit_jumlah" value="<?= $row['transaksi_jumlah']; ?>" min="1" class="form-control form-control-sm" style="width: 70px;">
                                <button type="submit" class="btn btn-sm btn-primary" title="Update" <?= isset($_SESSION['bayar_ditekan']) && $_SESSION['bayar_ditekan'] ? 'disabled' : '' ?>>
                                    <i class="bi bi-check2-square"></i>
                                </button>
                            </form>

                            <form method="post" action="transaksi.php" class="d-inline">
                                <input type="hidden" name="action" value="hapus">
                                <input type="hidden" name="hapus_id" value="<?= $row['transaksi_id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Hapus transaksi ini?')" <?= isset($_SESSION['bayar_ditekan']) && $_SESSION['bayar_ditekan'] ? 'disabled' : '' ?>>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center">Belum ada transaksi.</td></tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-end">Total Bayar</th>
                <th colspan="2">Rp<?= number_format($total_transaksi); ?></th>
            </tr>
        </tfoot>
    </table>

    <?php if ($transaksi_aktif > 0): ?>
        <div class="d-flex justify-content-between align-items-center mt-4"> <form method="post" class="d-flex gap-2">
                <?php if (!isset($_SESSION['bayar_ditekan']) || !$_SESSION['bayar_ditekan']): ?>
                    <button name="bayar" class="btn btn-primary btn-lg">Bayar Sekarang</button>
                <?php else: ?>
                    <button name="selesai" class="btn btn-success btn-lg">Pembayaran Selesai</button>
                <?php endif; ?>
            </form>

            <?php if (isset($_SESSION['bayar_ditekan']) && $_SESSION['bayar_ditekan']): ?>
                <div class="text-center">
                    <h5>Scan untuk Bayar:</h5>
                    <img src="<?= $qr_code_url; ?>" alt="QR Code Pembayaran" class="img-fluid" style="max-width: 150px;">
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>
<!-- footer -->
    <footer class="bg-dark text-white text-center py-3">
        <div class="container">
            <p class="mb-0">&copy; 2025 Muna Aliya Bil Qisthi - SMK Telkom Jakarta.</p>
        </div>
    </footer>
</body>
</html>