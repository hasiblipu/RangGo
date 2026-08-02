<?php
require_once '../../config/db.php';
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $conn->prepare("
        SELECT o.*, r.restaurant_name, u.name as rider_name 
        FROM orders o 
        LEFT JOIN restaurants r ON o.restaurant_id = r.id 
        LEFT JOIN users u ON o.rider_id = u.id 
        ORDER BY o.id DESC
    ");
    $stmt->execute();
    echo json_encode(["status" => "success", "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit();
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $order_id = $data['order_id'] ?? null;
    $status = $data['status'] ?? null;
    $rider_id = $data['rider_id'] ?? null;

    if ($rider_id) {
        $stmt = $conn->prepare("UPDATE orders SET status = :status, rider_id = :rider_id WHERE id = :id");
        $stmt->bindParam(':rider_id', $rider_id);
    } else {
        $stmt = $conn->prepare("UPDATE orders SET status = :status WHERE id = :id");
    }

    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $order_id);

    if ($stmt->execute()) echo json_encode(["status" => "success", "message" => "Order updated"]);
}
?>