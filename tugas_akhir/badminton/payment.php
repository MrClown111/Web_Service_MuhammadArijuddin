<?php
// Mengaktifkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Ambil data user
$user_result = $conn->query("SELECT name, email, phone FROM users WHERE id = $user_id");
$user_data = $user_result->fetch_assoc();

// Ambil data booking terakhir dari sesi
$last_booking = isset($_SESSION['last_booking']) ? $_SESSION['last_booking'] : null;

if ($last_booking) {
    // Ambil nama lapangan berdasarkan court_id dari booking terakhir
    $court_id = $last_booking['court_id'];
    $court_result = $conn->query("SELECT name FROM courts WHERE id = $court_id");
    $court_data = $court_result->fetch_assoc();
    $court_name = $court_data['name'] ?? 'Tidak tersedia';
} else {
    $court_name = 'Tidak tersedia';
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran</title>
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
        .payment-details {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            padding: 10px;
            border-radius: 5px;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .error-message {
            color: red;
        }
    </style>
</head>
<body>

<h1>Pembayaran</h1>

<?php if ($last_booking): ?>
    <div class="payment-details">
        <h2>Detail Booking</h2>
        <p><strong>Nama Pemesan:</strong> <?= htmlspecialchars($user_data['name'] ?? 'Tidak tersedia'); ?></p>
        <p><strong>Email Pemesan:</strong> <?= htmlspecialchars($user_data['email'] ?? 'Tidak tersedia'); ?></p>
        <p><strong>Telepon Pemesan:</strong> <?= htmlspecialchars($user_data['phone'] ?? 'Tidak tersedia'); ?></p>
        <p><strong>Lapangan:</strong> <?= htmlspecialchars($court_name); ?></p> <!-- Menampilkan nama lapangan -->
        <p><strong>Tanggal Booking:</strong> <?= htmlspecialchars($last_booking['booking_date'] ?? 'Tidak tersedia'); ?></p>
        <p><strong>Waktu Mulai:</strong> <?= htmlspecialchars($last_booking['start_time'] ?? 'Tidak tersedia'); ?></p>
        <p><strong>Waktu Selesai:</strong> <?= htmlspecialchars($last_booking['end_time'] ?? 'Tidak tersedia'); ?></p>
        <p><strong>Total Bayar:</strong> Rp <?= number_format($last_booking['total_price'] ?? 0, 2); ?></p>
    </div>
    
    <form method="POST" action="process_payment.php">
        <input type="hidden" name="booking_id" value="<?= htmlspecialchars($last_booking['id'] ?? ''); ?>"> <!-- ID booking -->
        <input type="hidden" name="total_price" value="<?= htmlspecialchars($last_booking['total_price'] ?? ''); ?>"> <!-- Total harga -->
        <input type="hidden" name="payment_method" value="BCA"> <!-- Metode pembayaran default -->
        <input type="submit" value="Bayar">
    </form>
<?php else: ?>
    <p class="error-message">Tidak ada detail booking yang ditemukan. Silakan lakukan booking terlebih dahulu.</p>
<?php endif; ?>

</body>
</html>