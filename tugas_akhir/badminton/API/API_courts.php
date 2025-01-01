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
        $result = $conn->query("SELECT * FROM courts");
        $courts = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($courts);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $conn->prepare("INSERT INTO courts (name, description, price_per_hour) VALUES (?, ?, ?)");
        $stmt->bind_param("ssd", $data['name'], $data['description'], $data['price_per_hour']);
        $stmt->execute();
        echo json_encode(["id" => $stmt->insert_id]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $conn->prepare("UPDATE courts SET name=?, description=?, price_per_hour=? WHERE id=?");
        $stmt->bind_param("ssdi", $data['name'], $data['description'], $data['price_per_hour'], $data['id']);
        $stmt->execute();
        echo json_encode(["message" => "Court updated"]);
        break;

    case 'DELETE':
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("DELETE FROM courts WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        echo json_encode(["message" => "Court deleted"]);
        break;

    default:
        echo json_encode(["error" => "Metode tidak diperbolehkan"]);
        break;
}

$conn->close();
?>