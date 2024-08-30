<?php
// Menentukan nama server database (biasanya 'localhost' untuk pengembangan lokal)
$servername = "localhost";

// Menentukan nama pengguna untuk koneksi database (default MySQL biasanya 'root')
$username = "root"; // Ganti dengan username database Anda

// Menentukan kata sandi untuk koneksi database (default MySQL biasanya kosong untuk 'root')
$password = ""; // Ganti dengan password database Anda

// Menentukan nama database yang akan digunakan untuk koneksi
$dbname = "login_system";

// Membuat koneksi ke database menggunakan objek mysqli
$conn = new mysqli($servername, $username, $password, $dbname);

// Mengecek apakah koneksi berhasil
if ($conn->connect_error) {
    // Jika koneksi gagal, hentikan eksekusi dan tampilkan pesan kesalahan
    die("Koneksi gagal: " . $conn->connect_error);
}

// Jika koneksi berhasil, kode di bawah ini akan dieksekusi (tidak ada di kode ini)