<?php
session_start(); // Memulai sesi

// Cek apakah ada pesan sukses
if (isset($_SESSION['success_message'])) {
    echo '<div style="background-color: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 20px;">';
    echo htmlspecialchars($_SESSION['success_message']);
    echo '</div>';
    // Hapus pesan setelah ditampilkan
    unset($_SESSION['success_message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Badminton Booking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #007bff;
            padding: 10px 20px;
            color: white;
        }
        .navbar .brand {
            font-size: 20px;
            font-weight: bold;
        }
        .navbar .nav-links {
            display: flex;
            gap: 15px;
        }
        .navbar .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .navbar .nav-links a:hover {
            background-color: #0056b3;
        }
        .content {
            margin: 20px;
            text-align: center;
        }
        .content h1 {
            font-size: 36px;
            color: #333;
        }
        .content p {
            font-size: 18px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="brand">Badminton</div>
        <div class="nav-links">
            <a href="registrasi.php">Registrasi</a>
            <a href="booking.php">Booking</a>
            <a href="review.php">Review</a> <!-- Tautan Review ditambahkan di sini -->
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <h1>Selamat Datang di Sistem Booking Lapangan Badminton</h1>
        <p>Pilih navigasi di atas untuk mendaftar atau melakukan booking lapangan.</p>
    </div>
</body>
</html>