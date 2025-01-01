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
        $result = $conn->query("SELECT * FROM reviews");
        $reviews = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($reviews);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $conn->prepare("INSERT INTO reviews (user_id, court, rating, review) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isis", $data['user_id'], $data['court'], $data['rating'], $data['review']);
        $stmt->execute();
        echo json_encode(["id" => $stmt->insert_id]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $conn->prepare("UPDATE reviews SET user_id=?, court=?, rating=?, review=? WHERE id=?");
        $stmt->bind_param("isisi", $data['user_id'], $data['court'], $data['rating'], $data['review'], $data['id']);
        $stmt->execute();
        echo json_encode(["message" => "Review updated"]);
        break;

    case 'DELETE':
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("DELETE FROM reviews WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        echo json_encode(["message" => "Review deleted"]);
        break;

    default:
        echo json_encode(["error" => "Metode tidak diperbolehkan"]);
        break;
}

$conn->close();
?>