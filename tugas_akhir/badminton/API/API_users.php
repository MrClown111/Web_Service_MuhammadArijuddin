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
        $result = $conn->query("SELECT * FROM users");
        $users = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($users);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $data['name'], $data['email'], password_hash($data['password'], PASSWORD_DEFAULT), $data['phone']);
        $stmt->execute();
        echo json_encode(["id" => $stmt->insert_id]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=? WHERE id=?");
        $stmt->bind_param("sssi", $data['name'], $data['email'], $data['phone'], $data['id']);
        $stmt->execute();
        echo json_encode(["message" => "User updated"]);
        break;

    case 'DELETE':
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        echo json_encode(["message" => "User deleted"]);
        break;

    default:
        echo json_encode(["error" => "Metode tidak diperbolehkan"]);
        break;
}

$conn->close();
?>