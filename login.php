<?php
// Memulai sesi untuk melacak status login pengguna
session_start();

// Mengimpor atau menyertakan file 'db.php' yang berisi koneksi database
include('db.php');

// Memeriksa apakah form telah dikirim menggunakan metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Menyimpan input username dan password dari form ke dalam variabel
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Mempersiapkan query SQL untuk mengambil data pengguna berdasarkan username
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql); // Menyiapkan statement SQL dengan menggunakan prepared statement untuk mencegah SQL injection
    $stmt->bind_param("s", $username); // Mengikat parameter (username) ke dalam statement SQL
    $stmt->execute(); // Menjalankan statement SQL
    $result = $stmt->get_result(); // Mendapatkan hasil dari eksekusi query
    $user = $result->fetch_assoc(); // Mengambil data pengguna dalam bentuk array asosiatif

    // Memeriksa apakah pengguna ditemukan dan password yang diberikan sesuai
    if ($user && password_verify($password, $user['password'])) {
        // Jika validasi berhasil, mengatur sesi untuk menandakan bahwa pengguna telah login
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header("Location: welcome.php"); // Mengarahkan pengguna ke halaman 'welcome.php'
        exit; // Menghentikan eksekusi skrip untuk memastikan tidak ada kode lain yang dijalankan
    } else {
        // Jika validasi gagal, menetapkan pesan kesalahan
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* Mengatur gaya dasar untuk seluruh halaman */
        body {
            font-family: Arial, sans-serif;
            background-color: #fbe8eb;
            /* Latar belakang berwarna baby pink */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Mengatur gaya untuk container form login */
        .login-container {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        /* Mengatur gaya untuk heading di dalam container */
        .login-container h2 {
            margin: 0 0 20px;
            color: #ff6f7d;
            /* Warna baby pink untuk heading */
        }

        /* Mengatur gaya untuk input form (username dan password) */
        .login-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        /* Mengatur gaya untuk tombol login */
        .login-container button {
            width: 100%;
            padding: 10px;
            background: #ff6f7d;
            /* Latar belakang tombol berwarna baby pink */
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Mengubah warna tombol saat dihover */
        .login-container button:hover {
            background: #ff4f5d;
            /* Warna baby pink yang lebih gelap saat hover */
        }

        /* Mengatur gaya untuk pesan kesalahan */
        .error {
            color: red;
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php
        // Menampilkan pesan kesalahan jika ada
        if (isset($error)) {
            echo "<p class='error'>$error</p>";
        }
        ?>
        <!-- Form untuk login -->
        <form method="post" action="">
            <input type="text" name="username" placeholder="Username" required> <!-- Input untuk username -->
            <input type="password" name="password" placeholder="Password" required> <!-- Input untuk password -->
            <button type="submit">Login</button> <!-- Tombol untuk submit form -->
        </form>
    </div>
</body>

</html>