<?php
// api/partner-register.php
require_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // React Request Body (JSON) ফেচ করা
    $data = json_decode(file_get_contents("php://input"), true);

    $restaurant_name = trim($data['restaurantName'] ?? '');
    $phone           = trim($data['phone'] ?? '');
    $address         = trim($data['address'] ?? 'Rangpur City');
    $cuisine_type    = trim($data['cuisineType'] ?? 'Fast Food, Bangla');

    // ইনপুট ভ্যালিডেশন
    if (empty($restaurant_name) || empty($phone)) {
        echo json_encode([
            "status" => "error", 
            "message" => "রেস্টুরেন্টের নাম এবং মোবাইল নম্বর প্রদান করা আবশ্যক!"
        ]);
        http_response_code(400);
        exit();
    }

    try {
        // একই ফোন নম্বর দিয়ে আগে আবেদন করা হয়েছে কিনা তা পরীক্ষা করা
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE phone = :phone");
        $checkStmt->execute([':phone' => $phone]);
        
        if ($checkStmt->rowCount() > 0) {
            echo json_encode([
                "status" => "error", 
                "message" => "এই মোবাইল নম্বরটি দিয়ে ইতিপূর্বে আবেদন করা হয়েছে!"
            ]);
            http_response_code(409);
            exit();
        }

        // Transaction শুরু (যেন দুটো টেবিলেই একসাথে ডাটা সেভ হয়)
        $conn->beginTransaction();

        // ১. users টেবিলে পার্টনার হিসেবে জমা করা (Status: 'pending')
        $stmtUser = $conn->prepare("
            INSERT INTO users (name, phone, role, status) 
            VALUES (:name, :phone, 'partner', 'pending')
        ");
        $stmtUser->execute([
            ':name' => $restaurant_name,
            ':phone' => $phone
        ]);
        
        $user_id = $conn->lastInsertId();

        // ২. restaurants টেবিলে রেস্টুরেন্টের তথ্য সেভ করা
        $stmtRest = $conn->prepare("
            INSERT INTO restaurants (user_id, restaurant_name, address, cuisine_type) 
            VALUES (:user_id, :r_name, :address, :cuisine)
        ");
        $stmtRest->execute([
            ':user_id' => $user_id,
            ':r_name'  => $restaurant_name,
            ':address' => $address,
            ':cuisine' => $cuisine_type
        ]);

        // Transaction সম্পন্ন
        $conn->commit();

        echo json_encode([
            "status" => "success",
            "message" => "পার্টনার রেজিস্ট্রেশন রিকোয়েস্ট সফলভাবে জমা হয়েছে! আমাদের টিম যাচাই-বাছাই করে আপনার সাথে যোগাযোগ করবে।"
        ]);
        http_response_code(201);

    } catch (Exception $e) {
        // কোনো সমস্যা হলে Transaction বাতিল করা
        $conn->rollBack();
        echo json_encode([
            "status" => "error", 
            "message" => "সার্ভার সমস্যা: আবেদন জমা নেওয়া সম্ভব হয়নি! " . $e->getMessage()
        ]);
        http_response_code(500);
    }
} else {
    echo json_encode([
        "status" => "error", 
        "message" => "Invalid Request Method"
    ]);
    http_response_code(405);
}
?>