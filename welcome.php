<?php
// Memulai sesi untuk melacak informasi pengguna yang sedang login
session_start();

// Memeriksa apakah pengguna telah login. Jika belum, arahkan ke halaman login.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php"); // Mengarahkan pengguna ke halaman login jika belum login
    exit; // Menghentikan eksekusi kode lebih lanjut
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <style>
        /* Mengatur gaya dasar untuk seluruh halaman */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Mengatur gaya untuk container pesan selamat datang */
        .welcome-container {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        /* Mengatur gaya untuk heading selamat datang */
        .welcome-container h1 {
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="welcome-container">
        <!-- Menampilkan pesan selamat datang dengan nama pengguna yang telah login -->
        <h1>Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <!-- Tautan untuk logout -->
        <a href="logout.php">Logout</a>
    </div>
</body>

</html>