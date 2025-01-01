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
        $result = $conn->query("SELECT * FROM payments");
        $payments = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($payments);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $conn->prepare("INSERT INTO payments (booking_id, user_id, amount_paid) VALUES (?, ?, ?)");
        $stmt->bind_param("iid", $data['booking_id'], $data['user_id'], $data['amount_paid']);
        $stmt->execute();
        echo json_encode(["id" => $stmt->insert_id]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $conn->prepare("UPDATE payments SET booking_id=?, user_id=?, amount_paid=? WHERE id=?");
        $stmt->bind_param("iidi", $data['booking_id'], $data['user_id'], $data['amount_paid'], $data['id']);
        $stmt->execute();
        echo json_encode(["message" => "Payment updated"]);
        break;

    case 'DELETE':
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("DELETE FROM payments WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        echo json_encode(["message" => "Payment deleted"]);
        break;

    default:
        echo json_encode(["error" => "Metode tidak diperbolehkan"]);
        break;
}

$conn->close();
?>