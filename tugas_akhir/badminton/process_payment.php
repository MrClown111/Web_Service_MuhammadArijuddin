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

// Ambil data dari form
$booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : null;
$payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : null;
$amount_paid = isset($_POST['total_price']) ? floatval($_POST['total_price']) : 0;
$payment_date = date("Y-m-d H:i:s");
$payment_status = 'Completed'; // Status pembayaran bisa diubah sesuai logika Anda

// Periksa apakah booking_id dan payment_method tidak kosong
if (empty($booking_id) || empty($payment_method)) {
    die("Booking ID atau metode pembayaran tidak boleh kosong.");
}

// Debugging untuk memastikan data input benar
// echo "Booking ID: $booking_id<br>";
// echo "User ID: $user_id<br>";
// echo "Payment Method: $payment_method<br>";
// echo "Amount Paid: $amount_paid<br>";
// echo "Payment Date: $payment_date<br>";
// exit();

// Pastikan booking_id valid
$stmt = $conn->prepare("SELECT id FROM bookings WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Booking ID tidak valid atau Anda tidak memiliki booking ini. Tidak dapat melanjutkan pembayaran.");
}

// Siapkan pernyataan SQL untuk menyimpan data pembayaran
$stmt = $conn->prepare("INSERT INTO payments (booking_id, user_id, payment_method, amount_paid, payment_date, payment_status) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iissss", $booking_id, $user_id, $payment_method, $amount_paid, $payment_date, $payment_status);

if ($stmt->execute()) {
    // Jika berhasil, simpan pesan di sesi dan redirect ke index.php
    $_SESSION['success_message'] = "Pembayaran telah berhasil!";
    header("Location: index.php"); // Ganti dengan halaman yang sesuai
    exit();
} else {
    $error_message = "Error: " . $stmt->error;
    // Tampilkan pesan error untuk debugging
    echo $error_message;
}

// Tutup pernyataan dan koneksi
$stmt->close();
$conn->close();
?>
