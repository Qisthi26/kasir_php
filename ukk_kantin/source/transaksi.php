<?php
session_start(); // Pastikan session dimulai
include 'koneksi.php'; // Koneksi ke database

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'tambah':
            if (isset($_POST['produk_id'], $_POST['warung_id'], $_POST['jumlah'])) {
                $produk_id = $_POST['produk_id'];
                $warung_id = $_POST['warung_id'];
                $jumlah = $_POST['jumlah'];

                // Gunakan prepared statement untuk keamanan
                $stmt = $conn->prepare("SELECT produk_harga, produk_stok FROM produk WHERE produk_id = ?");
                $stmt->bind_param("i", $produk_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $produk = $result->fetch_assoc();
                $stmt->close();

                if ($produk) {
                    $harga = $produk['produk_harga'];
                    $stok = $produk['produk_stok'];

                    if ($jumlah > $stok) {
                        $_SESSION['pesan_error'] = 'Jumlah melebihi stok yang tersedia!';
                        header("Location: list2.php");
                        exit;
                    }

                    $total = $harga * $jumlah;

                    // Tambah transaksi
                    $stmt = $conn->prepare("INSERT INTO transaksi (warung_id, produk_id, transaksi_jumlah, transaksi_total) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("iiid", $warung_id, $produk_id, $jumlah, $total);
                    $stmt->execute();
                    $stmt->close();

                    // Kurangi stok
                    $stok_baru = $stok - $jumlah;
                    $stmt = $conn->prepare("UPDATE produk SET produk_stok = ? WHERE produk_id = ?");
                    $stmt->bind_param("ii", $stok_baru, $produk_id);
                    $stmt->execute();
                    $stmt->close();
                }
            }
            break;

        case 'edit':
            if (isset($_POST['edit_id'], $_POST['edit_jumlah'])) {
                $id = $_POST['edit_id'];
                $jumlah_baru = $_POST['edit_jumlah'];

                // Ambil data transaksi lama
                $stmt = $conn->prepare("SELECT produk_id, transaksi_jumlah FROM transaksi WHERE transaksi_id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $transaksi = $result->fetch_assoc();
                $stmt->close();

                if ($transaksi) {
                    $produk_id = $transaksi['produk_id'];
                    $jumlah_lama = $transaksi['transaksi_jumlah'];

                    // Ambil data produk
                    $stmt = $conn->prepare("SELECT produk_harga, produk_stok FROM produk WHERE produk_id = ?");
                    $stmt->bind_param("i", $produk_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $produk = $result->fetch_assoc();
                    $stmt->close();

                    if ($produk) {
                        $harga = $produk['produk_harga'];
                        $stok = $produk['produk_stok'];

                        // Hitung stok yang tersedia termasuk yang dibeli sebelumnya
                        $stok_tersedia = $stok + $jumlah_lama;

                        if ($jumlah_baru > $stok_tersedia) {
                            $_SESSION['pesan_error'] = 'Jumlah melebihi stok yang tersedia setelah penyesuaian!';
                            header("Location: list2.php");
                            exit;
                        }

                        // Update transaksi
                        $total = $harga * $jumlah_baru;
                        $stmt = $conn->prepare("UPDATE transaksi SET transaksi_jumlah = ?, transaksi_total = ? WHERE transaksi_id = ?");
                        $stmt->bind_param("idi", $jumlah_baru, $total, $id);
                        $stmt->execute();
                        $stmt->close();

                        // Update stok di tabel produk
                        $stok_baru = $stok_tersedia - $jumlah_baru;
                        $stmt = $conn->prepare("UPDATE produk SET produk_stok = ? WHERE produk_id = ?");
                        $stmt->bind_param("ii", $stok_baru, $produk_id);
                        $stmt->execute();
                        $stmt->close();
                    }
                }
            }
            break;

        case 'hapus':
            if (isset($_POST['hapus_id'])) {
                $id = $_POST['hapus_id'];

                // Ambil jumlah & produk dari transaksi yang akan dihapus
                $stmt = $conn->prepare("SELECT produk_id, transaksi_jumlah FROM transaksi WHERE transaksi_id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $transaksi = $result->fetch_assoc();
                $stmt->close();

                if ($transaksi) {
                    $produk_id = $transaksi['produk_id'];
                    $jumlah = $transaksi['transaksi_jumlah'];

                    // Kembalikan stok
                    $stmt = $conn->prepare("UPDATE produk SET produk_stok = produk_stok + ? WHERE produk_id = ?");
                    $stmt->bind_param("ii", $jumlah, $produk_id);
                    $stmt->execute();
                    $stmt->close();

                    // Hapus transaksi
                    $stmt = $conn->prepare("DELETE FROM transaksi WHERE transaksi_id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $stmt->close();
                }
            }
            break;
            // Case 'selesai' sudah di handle di list2.php karena reset session
    }

    // Redirect kembali ke list2.php setelah setiap operasi
    header("Location: list2.php");
    exit;
}
?>