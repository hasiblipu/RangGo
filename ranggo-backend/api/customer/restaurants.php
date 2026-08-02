<?php
require_once '../../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$restaurant_id = $_GET['id'] ?? null;

if ($method === 'GET') {
    if ($restaurant_id) {
        // একটি নির্দিষ্ট রেস্টুরেন্ট ও তার খাবার (Menu) ফেচ করা
        $stmtRes = $conn->prepare("SELECT * FROM restaurants WHERE id = :id");
        $stmtRes->execute([':id' => $restaurant_id]);
        $restaurant = $stmtRes->fetch(PDO::FETCH_ASSOC);

        $stmtItems = $conn->prepare("SELECT * FROM products WHERE restaurant_id = :id");
        $stmtItems->execute([':id' => $restaurant_id]);
        $menu = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            "status" => "success",
            "data" => [
                "restaurant" => $restaurant,
                "menu" => $menu
            ]
        ]);
    } else {
        // সকল অনুমোদিত রেস্টুরেন্টের তালিকা ফেচ করা
        $stmt = $conn->prepare("
            SELECT r.*, u.status 
            FROM restaurants r 
            JOIN users u ON r.user_id = u.id 
            WHERE u.status = 'approved'
        ");
        $stmt->execute();
        $restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["status" => "success", "data" => $restaurants]);
    }
}
?>