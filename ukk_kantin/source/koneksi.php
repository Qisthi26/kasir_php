<?php
$conn = new mysqli("localhost", "root", "", "kantin");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>