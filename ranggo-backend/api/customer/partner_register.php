<?php
require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $restaurant_name = $data['restaurantName'] ?? '';
    $phone           = $data['phone'] ?? '';
    $address         = $data['address'] ?? 'Rangpur';

    if (empty($restaurant_name) || empty($phone)) {
        echo json_encode(["status" => "error", "message" => "রেস্টুরেন্টের নাম ও ফোন নম্বর আবশ্যক"]);
        http_response_code(400);
        exit();
    }

    try {
        $conn->beginTransaction();

        // ১. ইউজার টেবিল এ পেন্ডিং পার্টনার হিসেবে সেভ করা
        $stmtUser = $conn->prepare("INSERT INTO users (name, phone, role, status) VALUES (:name, :phone, 'partner', 'pending')");
        $stmtUser->execute([':name' => $restaurant_name, ':phone' => $phone]);
        $user_id = $conn->lastInsertId();

        // ২. রেস্টুরেন্ট টেবিল এ সেভ করা
        $stmtRest = $conn->prepare("INSERT INTO restaurants (user_id, restaurant_name, address) VALUES (:user_id, :r_name, :address)");
        $stmtRest->execute([':user_id' => $user_id, ':r_name' => $restaurant_name, ':address' => $address]);

        $conn->commit();
        echo json_encode(["status" => "success", "message" => "পার্টনার রেজিস্ট্রেশন রিকোয়েস্ট সফলভাবে জমা হয়েছে!"]);
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(["status" => "error", "message" => "রেজিস্ট্রেশন জমা নিতে সমস্যা হয়েছে"]);
    }
}
?>