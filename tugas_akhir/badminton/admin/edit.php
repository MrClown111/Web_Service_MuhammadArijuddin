<?php
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

// Ambil data lapangan untuk ditampilkan
$court_id = $_GET['id'];
$court = null;

$stmt = $conn->prepare("SELECT * FROM courts WHERE id = ?");
$stmt->bind_param("i", $court_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $court = $result->fetch_assoc();
} else {
    die("Lapangan tidak ditemukan.");
}

// Proses pembaruan lapangan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $court_name = $_POST['court_name'];
    $description = $_POST['description'];
    $price_per_hour = $_POST['price_per_hour'];

    // Siapkan dan eksekusi pernyataan SQL untuk memperbarui data lapangan
    $stmt = $conn->prepare("UPDATE courts SET name = ?, description = ?, price_per_hour = ? WHERE id = ?");
    $stmt->bind_param("ssdi", $court_name, $description, $price_per_hour, $court_id);

    if ($stmt->execute()) {
        $success_message = "Lapangan berhasil diperbarui!";
    } else {
        $error_message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lapangan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h1 {
            color: #333;
        }
        form {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .success, .error {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

<h1>Edit Lapangan</h1>

<?php if (!empty($success_message)): ?>
    <div class="success"><?= $success_message; ?></div>
<?php endif; ?>
<?php if (!empty($error_message)): ?>
    <div class="error"><?= $error_message; ?></div>
<?php endif; ?>

<form method="POST" action="">
    <label for="court_name">Nama Lapangan:</label>
    <input type="text" id="court_name" name="court_name" value="<?= htmlspecialchars($court['name']); ?>" required>

    <label for="description">Deskripsi:</label>
    <textarea id="description" name="description" required><?= htmlspecialchars($court['description']); ?></textarea>

    <label for="price_per_hour">Harga per Jam:</label>
    <input type="number" id="price_per_hour" name="price_per_hour" step="0.01" value="<?= htmlspecialchars($court['price_per_hour']); ?>" required>

    <input type="submit" value="Perbarui Lapangan">
</form>

</body>
</html>