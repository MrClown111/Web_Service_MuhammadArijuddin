<?php
session_start(); // Memulai sesi

// Koneksi ke database
$servername = "localhost"; // Ganti dengan host database Anda
$username = "root"; // Ganti dengan username database Anda
$password = ""; // Ganti dengan password database Anda
$dbname = "badminton"; // Ganti dengan nama database Anda

$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil user_id dari sesi
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Pastikan user_id tidak null
if ($user_id === null) {
    die("User tidak terautentikasi. Silakan login terlebih dahulu.");
}

// Ambil daftar lapangan
$courts = [];
$result = $conn->query("SELECT id, name FROM courts");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $courts[] = $row;
    }
}

// Proses pengiriman ulasan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $court_name = isset($_POST['court_name']) ? $_POST['court_name'] : '';
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : null;
    $review = isset($_POST['review']) ? $_POST['review'] : '';

    // Validasi input
    if (empty($court_name) || empty($rating) || $rating < 1 || $rating > 5) {
        die("Nama lapangan, rating, dan ulasan tidak boleh kosong. Rating harus antara 1 hingga 5.");
    }

    // Siapkan pernyataan SQL untuk menyimpan ulasan
    $stmt = $conn->prepare("INSERT INTO reviews (user_id, court, rating, review, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("issi", $user_id, $court_name, $rating, $review);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Ulasan berhasil ditambahkan!";
        $stmt->close();
        // Redirect ke review.php setelah berhasil
        header("Location: review.php");
        exit();
    } else {
        $error_message = "Error: " . $stmt->error;
    }
}

// Ambil ulasan yang telah ditambahkan
$reviews = $conn->query("SELECT r.*, u.name AS user_name FROM reviews r JOIN users u ON r.user_id = u.id");

// Tutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berikan Ulasan</title>
</head>
<body>

<h1>Berikan Ulasan untuk Lapangan</h1>

<?php if (!empty($error_message)): ?>
    <div class="error"><?= $error_message; ?></div>
<?php endif; ?>
<?php if (!empty($_SESSION['success_message'])): ?>
    <div class="success"><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
<?php endif; ?>

<form method="POST" action="">
    <label for="court_name">Nama Lapangan:</label><br>
    <select id="court_name" name="court_name" required>
        <option value="">Pilih Lapangan</option>
        <?php foreach ($courts as $court): ?>
            <option value="<?= htmlspecialchars($court['name']); ?>"><?= htmlspecialchars($court['name']); ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label for="rating">Rating (1-5):</label><br>
    <input type="number" id="rating" name="rating" min="1" max="5" required><br><br>

    <label for="review">Ulasan:</label><br>
    <textarea id="review" name="review" required></textarea><br><br>

    <input type="submit" value="Kirim Ulasan">
</form>

<h2>Ulasan yang Diberikan</h2>
<?php if ($reviews->num_rows > 0): ?>
    <ul>
        <?php while ($row = $reviews->fetch_assoc()): ?>
            <li>
                <strong><?= htmlspecialchars($row['user_name']); ?></strong> - <em><?= htmlspecialchars($row['court']); ?></em><br>
                Rating: <?= htmlspecialchars($row['rating']); ?> <br>
                <?= htmlspecialchars($row['review']); ?> <br>
                <small><?= $row['created_at']; ?></small>
            </li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p>Tidak ada ulasan yang diberikan.</p>
<?php endif; ?>

</body>
</html>