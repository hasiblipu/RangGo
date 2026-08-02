<?php
require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $name     = $data['name'] ?? '';
    $phone    = $data['phone'] ?? '';
    $password = $data['password'] ?? '';

    if (empty($name) || empty($phone) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "সব ঘর সঠিকভাবে পূরণ করুন"]);
        http_response_code(400);
        exit();
    }

    // পাসওয়ার্ড হ্যাশ করা
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    try {
        $stmt = $conn->prepare("INSERT INTO users (name, phone, password, role, status) VALUES (:name, :phone, :password, 'customer', 'approved')");
        $stmt->execute([':name' => $name, ':phone' => $phone, ':password' => $hashedPassword]);

        echo json_encode(["status" => "success", "message" => "সাইন-আপ সফল হয়েছে!"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "এই ফোন নম্বরটি দিয়ে আগেই একাউন্ট খোলা হয়েছে"]);
    }
}
?>