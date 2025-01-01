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

// Ambil daftar lapangan beserta harga
$courts = $conn->query("SELECT * FROM courts");

// Proses booking
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $court_id = $_POST['court_id'];
    
    // Ambil user_id dari sesi
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    // Pastikan user_id tidak null
    if ($user_id === null) {
        die("User tidak terautentikasi. Silakan login terlebih dahulu.");
    }

    $booking_date = $_POST['booking_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Hitung durasi bermain dalam jam
    $start_datetime = DateTime::createFromFormat('Y-m-d H:i', "$booking_date $start_time");
    $end_datetime = DateTime::createFromFormat('Y-m-d H:i', "$booking_date $end_time");
    $interval = $start_datetime->diff($end_datetime);
    $hours = $interval->h + ($interval->i > 0 ? 1 : 0); // Bulatkan ke atas jika ada menit

    // Ambil harga lapangan
    $stmt = $conn->prepare("SELECT price_per_hour FROM courts WHERE id = ?");
    $stmt->bind_param("i", $court_id);
    $stmt->execute();
    $stmt->bind_result($price_per_hour);
    $stmt->fetch();
    $stmt->close();

    // Hitung total_price
    $total_price = $price_per_hour * $hours;

    // Siapkan dan eksekusi pernyataan SQL untuk menyimpan data booking
    $stmt = $conn->prepare("INSERT INTO bookings (court_id, user_id, booking_date, start_time, end_time, total_price) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssd", $court_id, $user_id, $booking_date, $start_time, $end_time, $total_price);

    if ($stmt->execute()) {
        // Simpan informasi booking untuk digunakan di halaman pembayaran
        $_SESSION['last_booking'] = [
            'id' => $conn->insert_id, // Tambahkan ini
            'court_id' => $court_id,
            'user_id' => $user_id,
            'booking_date' => $booking_date,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'total_price' => $total_price,
        ];
        header("Location: payment.php"); // Redirect ke halaman payment.php
        exit();
    } else {
        $error_message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Ambil pesan sukses dari sesi
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
unset($_SESSION['success_message']); // Hapus pesan dari sesi setelah ditampilkan

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Lapangan</title>
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
        input, select {
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

<h1>Booking Lapangan</h1>

<?php if ($success_message): ?>
    <div class="success"><?= $success_message; ?></div>
<?php endif; ?>
<?php if (!empty($error_message)): ?>
    <div class="error"><?= $error_message; ?></div>
<?php endif; ?>

<form method="POST" action="">
    <label for="court_id">Pilih Lapangan:</label>
    <select id="court_id" name="court_id" required>
        <option value="">-- Pilih Lapangan --</option>
        <?php if ($courts->num_rows > 0): ?>
            <?php while ($row = $courts->fetch_assoc()): ?>
                <option value="<?= $row['id']; ?>">
                    <?= $row['name']; ?> - Rp <?= number_format($row['price_per_hour'], 2); ?>/jam
                </option>
            <?php endwhile; ?>
        <?php else: ?>
            <option value="">Tidak ada lapangan tersedia</option>
        <?php endif; ?>
    </select>

    <label for="booking_date">Tanggal Booking:</label>
    <input type="date" id="booking_date" name="booking_date" required>

    <label for="start_time">Waktu Mulai:</label>
    <input type="time" id="start_time" name="start_time" required>

    <label for="end_time">Waktu Selesai:</label>
    <input type="time" id="end_time" name="end_time" required>

    <input type="submit" value="Booking">
</form>

</body>
</html>