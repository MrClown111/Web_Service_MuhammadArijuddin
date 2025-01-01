<?php
header("Content-Type: application/json");
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "badminton";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Koneksi gagal: " . $conn->connect_error]));
}

$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {
    case 'GET':
        $result = $conn->query("SELECT * FROM bookings");
        $bookings = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($bookings);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, court_id, booking_date, start_time, end_time, total_price) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssd", $data['user_id'], $data['court_id'], $data['booking_date'], $data['start_time'], $data['end_time'], $data['total_price']);
        $stmt->execute();
        echo json_encode(["id" => $stmt->insert_id]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $conn->prepare("UPDATE bookings SET user_id=?, court_id=?, booking_date=?, start_time=?, end_time=?, total_price=? WHERE id=?");
        $stmt->bind_param("iisssdi", $data['user_id'], $data['court_id'], $data['booking_date'], $data['start_time'], $data['end_time'], $data['total_price'], $data['id']);
        $stmt->execute();
        echo json_encode(["message" => "Booking updated"]);
        break;

    case 'DELETE':
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("DELETE FROM bookings WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        echo json_encode(["message" => "Booking deleted"]);
        break;

    default:
        echo json_encode(["error" => "Metode tidak diperbolehkan"]);
        break;
}

$conn->close();
?>