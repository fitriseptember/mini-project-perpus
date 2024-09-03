<?php
class Book
{
    // Properti untuk menyimpan koneksi database dan nama tabel
    private $conn;
    private $table_name = "books";

    // Properti untuk menyimpan data buku
    private $id;
    private $title;
    private $author;
    private $isbn;
    private $image;

    // Konstruktor untuk menginisialisasi koneksi database
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Metode setter untuk mengatur nilai properti id
    public function setId($id)
    {
        $this->id = $id;
    }

    // Metode setter untuk mengatur nilai properti title
    public function setTitle($title)
    {
        $this->title = $title;
    }

    // Metode setter untuk mengatur nilai properti author
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    // Metode setter untuk mengatur nilai properti isbn
    public function setIsbn($isbn)
    {
        $this->isbn = $isbn;
    }

    // Metode setter untuk mengatur nilai properti image
    public function setImage($image)
    {
        $this->image = $image;
    }

    // Metode untuk membuat (menambahkan) buku baru ke dalam database
    public function create()
    {
        // Query SQL untuk menyisipkan data buku ke dalam tabel
        $query = "INSERT INTO " . $this->table_name . " SET title=:title, author=:author, isbn=:isbn, image=:image";
        $stmt = $this->conn->prepare($query);

        // Bind parameter agar aman dari SQL Injection
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":author", $this->author);
        $stmt->bindParam(":isbn", $this->isbn);
        $stmt->bindParam(":image", $this->image);

        // Jalankan query dan kembalikan true jika berhasil, false jika gagal
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Metode untuk membaca semua buku dari database
    public function readAll()
    {
        // Query SQL untuk memilih semua data dari tabel buku
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt; // Mengembalikan hasil query
    }

    // Metode untuk membaca satu buku berdasarkan id
    public function readOne()
    {
        // Query SQL untuk memilih satu buku berdasarkan id
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);

        // Bind parameter id
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        // Ambil data buku dari hasil query
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->title = $row['title'];
        $this->author = $row['author'];
        $this->isbn = $row['isbn'];
        $this->image = $row['image'];

        return $row; // Mengembalikan data buku
    }

    // Metode untuk memperbarui data buku berdasarkan id
    public function update()
    {
        // Query SQL untuk memperbarui data buku berdasarkan id
        $query = "UPDATE " . $this->table_name . " SET title = :title, author = :author, isbn = :isbn, image = :image WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":author", $this->author);
        $stmt->bindParam(":isbn", $this->isbn);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":id", $this->id);

        // Jalankan query dan kembalikan true jika berhasil, false jika gagal
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Metode untuk menghapus buku berdasarkan id
    public function delete()
    {
        // Query SQL untuk menghapus buku berdasarkan id
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        // Bind parameter id
        $stmt->bindParam(1, $this->id);

        // Jalankan query dan kembalikan true jika berhasil, false jika gagal
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Metode untuk mencari buku berdasarkan judul
    public function searchByTitle($title)
    {
        // Query SQL untuk mencari buku berdasarkan judul
        $query = "SELECT * FROM " . $this->table_name . " WHERE title LIKE ?";
        $stmt = $this->conn->prepare($query);

        // Tambahkan wildcard pada judul untuk pencarian
        $title = "%" . $title . "%";
        $stmt->bindParam(1, $title);
        $stmt->execute();

        return $stmt; // Mengembalikan hasil pencarian
    }
}
?>