<?php
// Mengimpor atau menyertakan file 'db.php' yang berisi koneksi database
include('db.php');

// Memeriksa apakah form telah dikirim menggunakan metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Menyimpan input username, password, dan konfirmasi password dari form ke dalam variabel
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi form: Memeriksa apakah password dan konfirmasi password sesuai
    if ($password !== $confirm_password) {
        $error = "Password tidak cocok!";
    } else {
        // Jika validasi berhasil, enkripsi password menggunakan algoritma bcrypt
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Cek apakah username sudah ada di database
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql); // Mempersiapkan statement SQL
        $stmt->bind_param("s", $username); // Mengikat parameter (username) ke dalam statement SQL
        $stmt->execute(); // Menjalankan statement SQL
        $result = $stmt->get_result(); // Mendapatkan hasil dari eksekusi query

        // Jika username sudah ada, tampilkan pesan kesalahan
        if ($result->num_rows > 0) {
            $error = "Username sudah digunakan!";
        } else {
            // Jika username belum ada, simpan data pengguna baru ke dalam database
            $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
            $stmt = $conn->prepare($sql); // Mempersiapkan statement SQL untuk menyimpan data
            $stmt->bind_param("ss", $username, $hashed_password); // Mengikat parameter (username dan password) ke dalam statement SQL
            if ($stmt->execute()) { // Menjalankan statement SQL untuk menyimpan data
                $success = "Pendaftaran berhasil! <a href='login.php'>Login</a>";
            } else {
                // Jika terjadi kesalahan saat menyimpan data, tampilkan pesan kesalahan
                $error = "Terjadi kesalahan saat pendaftaran: " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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

        /* Mengatur gaya untuk container form register */
        .register-container {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        /* Mengatur gaya untuk heading di dalam container */
        .register-container h2 {
            margin: 0 0 20px;
            color: #ff6f7d;
            /* Warna baby pink untuk heading */
        }

        /* Mengatur gaya untuk input form (username, password, konfirmasi password) */
        .register-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        /* Mengatur gaya untuk tombol register */
        .register-container button {
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
        .register-container button:hover {
            background: #ff4f5d;
            /* Warna baby pink yang lebih gelap saat hover */
        }

        /* Mengatur gaya untuk pesan kesalahan dan pesan sukses */
        .error,
        .success {
            margin: 10px 0;
        }

        .error {
            color: red;
            /* Warna merah untuk pesan kesalahan */
        }

        .success {
            color: green;
            /* Warna hijau untuk pesan sukses */
        }
    </style>
</head>

<body>
    <div class="register-container">
        <h2>Register</h2>
        <?php
        // Menampilkan pesan kesalahan jika ada
        if (isset($error)) {
            echo "<p class='error'>$error</p>";
        }
        // Menampilkan pesan sukses jika ada
        if (isset($success)) {
            echo "<p class='success'>$success</p>";
        }
        ?>
        <!-- Form untuk registrasi -->
        <form method="post" action="">
            <input type="text" name="username" placeholder="Username" required> <!-- Input untuk username -->
            <input type="password" name="password" placeholder="Password" required> <!-- Input untuk password -->
            <input type="password" name="confirm_password" placeholder="Confirm Password" required> <!-- Input untuk konfirmasi password -->
            <button type="submit">Register</button> <!-- Tombol untuk submit form -->
        </form>
    </div>
</body>

</html>