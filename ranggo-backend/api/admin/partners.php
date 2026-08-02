<?php
require_once '../../config/db.php';
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $conn->prepare("SELECT u.id as user_id, u.name, u.phone, u.status, r.restaurant_name FROM users u JOIN restaurants r ON u.id = r.user_id WHERE u.role = 'partner'");
    $stmt->execute();
    echo json_encode(["status" => "success", "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit();
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $conn->prepare("UPDATE users SET status = :status WHERE id = :id AND role = 'partner'");
    $stmt->bindParam(':status', $data['status']);
    $stmt->bindParam(':id', $data['user_id']);
    if ($stmt->execute()) echo json_encode(["status" => "success", "message" => "Partner status updated"]);
}
?>