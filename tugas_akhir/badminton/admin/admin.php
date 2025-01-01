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

// Proses penambahan lapangan
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_court'])) {
    $court_name = $_POST['court_name'];
    $description = $_POST['description'];
    $price_per_hour = $_POST['price_per_hour'];

    // Siapkan dan eksekusi pernyataan SQL untuk menyimpan data lapangan
    $stmt = $conn->prepare("INSERT INTO courts (name, description, price_per_hour) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $court_name, $description, $price_per_hour);

    if ($stmt->execute()) {
        $success_message = "Lapangan berhasil ditambahkan!";
    } else {
        $error_message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Proses hapus lapangan
if (isset($_GET['delete'])) {
    $court_id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM courts WHERE id = ?");
    $stmt->bind_param("i", $court_id);

    if ($stmt->execute()) {
        $success_message = "Lapangan berhasil dihapus!";
    } else {
        $error_message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Proses hapus pengguna
if (isset($_GET['delete_user'])) {
    $user_id = $_GET['delete_user'];

    // Siapkan pernyataan SQL untuk menghapus pengguna
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        $success_message = "Pengguna berhasil dihapus!";
    } else {
        $error_message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Ambil data pelanggan
$users = $conn->query("SELECT * FROM users");

// Ambil data lapangan
$courts = $conn->query("SELECT * FROM courts");

// Ambil data pembayaran
$payments = $conn->query("
    SELECT p.id, u.name AS user_name, c.name AS court_name, b.booking_date, 
           b.start_time, b.end_time, p.amount_paid, p.payment_date, p.payment_status
    FROM payments p 
    JOIN bookings b ON p.booking_id = b.id 
    JOIN users u ON p.user_id = u.id 
    JOIN courts c ON b.court_id = c.id
");

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
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
        .button {
            padding: 5px 10px;
            margin-right: 5px;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .button.edit {
            background-color: #ffc107;
        }
        .button:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>

<h1>Dashboard Admin</h1>

<?php if (!empty($success_message)): ?>
    <div class="success"><?= $success_message; ?></div>
<?php endif; ?>
<?php if (!empty($error_message)): ?>
    <div class="error"><?= $error_message; ?></div>
<?php endif; ?>

<h2>Data Pelanggan</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Nama</th>
        <th>Email</th>
        <th>Telepon</th>
        <th>Tanggal Registrasi</th>
    </tr>
    <?php if ($users->num_rows > 0): ?>
        <?php while ($row = $users->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= $row['name']; ?></td>
                <td><?= $row['email']; ?></td>
                <td><?= $row['phone']; ?></td>
                <td><?= $row['created_at']; ?></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="5">Tidak ada data pelanggan.</td>
        </tr>
    <?php endif; ?>
</table>

<h2>Tambah Lapangan</h2>
<form method="POST" action="">
    <label for="court_name">Nama Lapangan:</label><br>
    <input type="text" id="court_name" name="court_name" required><br><br>

    <label for="description">Deskripsi:</label><br>
    <textarea id="description" name="description" required></textarea><br><br>

    <label for="price_per_hour">Harga per Jam:</label><br>
    <input type="number" id="price_per_hour" name="price_per_hour" step="0.01" required><br><br>

    <input type="submit" name="add_court" value="Tambah Lapangan">
</form>

<h2>Data Lapangan</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Nama Lapangan</th>
        <th>Deskripsi</th>
        <th>Harga per Jam</th>
        <th>Aksi</th>
    </tr>
    <?php if ($courts->num_rows > 0): ?>
        <?php while ($row = $courts->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= $row['name']; ?></td>
                <td><?= $row['description']; ?></td>
                <td><?= number_format($row['price_per_hour'], 2); ?></td>
                <td>
                    <a href="edit.php?id=<?= $row['id']; ?>" class="button edit">Edit</a>
                    <a href="?delete=<?= $row['id']; ?>" class="button" onclick="return confirm('Apakah Anda yakin ingin menghapus lapangan ini?');">Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="5">Tidak ada data lapangan.</td>
        </tr>
    <?php endif; ?>
</table>

<h2>Data Pembayaran</h2>
<table>
    <tr>
        <th>ID Pembayaran</th>
        <th>Nama Pelanggan</th>
        <th>Nama Lapangan</th>
        <th>Tanggal Booking</th>
        <th>Waktu Mulai</th>
        <th>Waktu Selesai</th>
        <th>Jumlah Dibayar</th>
        <th>Tanggal Pembayaran</th>
        <th>Status</th>
    </tr>
    <?php if ($payments->num_rows > 0): ?>
        <?php while ($row = $payments->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= htmlspecialchars($row['user_name']); ?></td>
                <td><?= htmlspecialchars($row['court_name']); ?></td>
                <td><?= htmlspecialchars($row['booking_date']); ?></td>
                <td><?= htmlspecialchars($row['start_time']); ?></td>
                <td><?= htmlspecialchars($row['end_time']); ?></td>
                <td>Rp <?= number_format($row['amount_paid'], 2); ?></td>
                <td><?= htmlspecialchars($row['payment_date']); ?></td>
                <td><?= htmlspecialchars($row['payment_status']); ?></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="9">Tidak ada data pembayaran.</td>
        </tr>
    <?php endif; ?>
</table>

</body>
</html>