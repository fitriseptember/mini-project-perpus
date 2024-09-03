<?php
// Sertakan file yang berisi class Database dan Book
include_once 'Database.php';
include_once 'Book.php';

// Buat instance koneksi ke database
$database = new Database();
$db = $database->getConnection();

// Buat instance Book dan sambungkan ke database
$book = new Book($db);

// Tangani penghapusan buku jika parameter 'delete' ada dalam URL
if (isset($_GET['delete'])) {
    $book->setId($_GET['delete']); // Set ID buku yang akan dihapus
    if ($book->delete()) { // Jika berhasil dihapus, tampilkan pesan sukses
        echo "<p style='color:green;'>Buku berhasil dihapus!</p>";
    } else { // Jika gagal dihapus, tampilkan pesan kesalahan
        echo "<p style='color:red;'>Gagal menghapus buku.</p>";
    }
}

// Tangani penambahan atau pembaruan buku jika ada data yang dikirim melalui POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $book->setTitle($_POST['title']); // Set judul buku
    $book->setAuthor($_POST['author']); // Set penulis buku
    $book->setIsbn($_POST['isbn']); // Set ISBN buku

    // Penanganan unggahan file gambar
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif']; // Ekstensi file yang diperbolehkan
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION); // Dapatkan ekstensi file

        // Periksa apakah ekstensi file valid
        if (in_array(strtolower($file_extension), $allowed_extensions)) {
            // Beri nama baru untuk file gambar yang diunggah
            $file_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = 'uploads/' . $file_name;
            // Pindahkan file yang diunggah ke direktori 'uploads'
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $book->setImage($file_name); // Set gambar buku
            } else {
                echo "<p style='color:red;'>Gagal mengunggah gambar.</p>";
            }
        } else {
            echo "<p style='color:red;'>Format file tidak valid. Hanya file JPG, JPEG, PNG, dan GIF yang diperbolehkan.</p>";
        }
    } else {
        // Jika tidak ada gambar baru yang diunggah, gunakan gambar yang ada
        $book->setImage($_POST['existing_image'] ?? '');
    }

    // Tangani pembaruan buku jika ada ID buku yang diset untuk diedit
    if (isset($_POST['edit_book'])) {
        $book->setId($_POST['id']); // Set ID buku yang akan diperbarui
        if ($book->update()) { // Jika pembaruan berhasil, tampilkan pesan sukses
            echo "<p style='color:green;'>Buku berhasil diperbarui!</p>";
        } else { // Jika pembaruan gagal, tampilkan pesan kesalahan
            echo "<p style='color:red;'>Gagal memperbarui buku.</p>";
        }
    } else {
        // Tangani penambahan buku baru
        if ($book->create()) { // Jika penambahan berhasil, tampilkan pesan sukses
            echo "<p style='color:green;'>Buku berhasil ditambahkan!</p>";
        } else { // Jika penambahan gagal, tampilkan pesan kesalahan
            echo "<p style='color:red;'>Gagal menambahkan buku. ISBN mungkin sudah ada.</p>";
        }
    }
}

// Tangani pengeditan buku jika parameter 'edit' ada dalam URL
$editBook = null;
if (isset($_GET['edit'])) {
    $book->setId($_GET['edit']); // Set ID buku yang akan diedit
    $editBook = $book->readOne(); // Dapatkan data buku yang akan diedit
}

// Dapatkan semua data buku untuk ditampilkan di halaman
$stmt = $book->readAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management</title>
    <link rel="stylesheet" type="text/css" href="style.css"> <!-- Link ke file CSS -->
</head>

<body>
    <div class="container">
        <h2>Daftar Buku:</h2>
        <div class="book-list">
            <!-- Loop untuk menampilkan daftar buku -->
            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="book-item">
                    <!-- Jika buku memiliki gambar, tampilkan -->
                    <?php if (!empty($row['image'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Gambar Buku" style="width:100px;height:auto;">
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p>Penulis: <?php echo htmlspecialchars($row['author']); ?></p>
                    <p>ISBN: <?php echo htmlspecialchars($row['isbn']); ?></p>
                    <!-- Tautan untuk mengedit atau menghapus buku -->
                    <a href="?edit=<?php echo $row['id']; ?>">Edit</a>
                    <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">Hapus</a>
                </div>
            <?php endwhile; ?>
        </div>

        <h2><?php echo $editBook ? 'Edit Buku' : 'Tambah Buku Baru'; ?>:</h2>
        <!-- Form untuk menambah atau mengedit buku -->
        <form method="POST" action="" enctype="multipart/form-data">
            <label for="title">Judul:</label>
            <input type="text" id="title" name="title" value="<?php echo $editBook['title'] ?? ''; ?>" required><br>

            <label for="author">Penulis:</label>
            <input type="text" id="author" name="author" value="<?php echo $editBook['author'] ?? ''; ?>" required><br>

            <label for="isbn">ISBN:</label>
            <input type="text" id="isbn" name="isbn" value="<?php echo $editBook['isbn'] ?? ''; ?>" required><br>

            <label for="image">Gambar Buku:</label>
            <input type="file" id="image" name="image"><br>
            <!-- Tampilkan gambar yang ada jika buku sedang diedit -->
            <?php if ($editBook && !empty($editBook['image'])): ?>
                <p>Gambar saat ini: <img src="uploads/<?php echo htmlspecialchars($editBook['image']); ?>" alt="Gambar Buku" style="width:50px;height:auto;"></p>
                <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($editBook['image']); ?>">
            <?php endif; ?>

            <!-- Tombol untuk menambah atau memperbarui buku -->
            <?php if ($editBook): ?>
                <input type="hidden" name="id" value="<?php echo $editBook['id']; ?>">
                <input type="submit" name="edit_book" value="Perbarui Buku">
            <?php else: ?>
                <input type="submit" name="add_book" value="Tambahkan Buku">
            <?php endif; ?>
        </form>

        <h2>Cari Buku Berdasarkan Judul:</h2>
        <!-- Form untuk mencari buku berdasarkan judul -->
        <form method="GET" action="">
            <label for="search-title">Judul:</label>
            <input type="text" id="search-title" name="title" required><br>
            <input type="submit" name="search" value="Cari Buku">
        </form>

        <!-- Tampilkan hasil pencarian buku berdasarkan judul -->
        <?php
        if (isset($_GET['search'])) {
            $stmt = $book->searchByTitle($_GET['title']); // Cari buku berdasarkan judul
            echo "<h2>Hasil Pencarian:</h2>";
            if ($stmt->rowCount() > 0) { // Jika ada hasil pencarian, tampilkan
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<p>Title: " . htmlspecialchars($row['title']) . " | Author: " . htmlspecialchars($row['author']) . " | ISBN: " . htmlspecialchars($row['isbn']) . "</p>";
                }
            } else {
                echo "<p>Buku tidak ditemukan.</p>"; // Jika tidak ada hasil, tampilkan pesan ini
            }
        }
        ?>
         <a href="login.php6">Logout</a>
    </div>
</body>

</html>