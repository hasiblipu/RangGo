<?php
require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $customer_name    = $data['name'] ?? '';
    $customer_phone   = $data['phone'] ?? '';
    $delivery_address = $data['address'] ?? '';
    $restaurant_id    = $data['restaurant_id'] ?? null;
    $total_amount     = $data['total_amount'] ?? 0;

    if (empty($customer_name) || empty($customer_phone) || empty($delivery_address) || !$restaurant_id) {
        echo json_encode(["status" => "error", "message" => "সকল তথ্য সঠিকভাবে প্রদান করুন"]);
        http_response_code(400);
        exit();
    }

    $stmt = $conn->prepare("
        INSERT INTO orders (customer_name, customer_phone, delivery_address, restaurant_id, total_amount, status) 
        VALUES (:name, :phone, :address, :restaurant_id, :total_amount, 'pending')
    ");

    $stmt->bindParam(':name', $customer_name);
    $stmt->bindParam(':phone', $customer_phone);
    $stmt->bindParam(':address', $delivery_address);
    $stmt->bindParam(':restaurant_id', $restaurant_id);
    $stmt->bindParam(':total_amount', $total_amount);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success", 
            "message" => "অর্ডার সফলভাবে জমা হয়েছে!", 
            "order_id" => $conn->lastInsertId()
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "অর্ডার জমা নিতে ব্যর্থ হয়েছে"]);
    }
}
?>