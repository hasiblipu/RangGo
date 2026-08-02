<?php
require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $phone    = $data['phone'] ?? '';
    $password = $data['password'] ?? '';

    if (empty($phone) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "মোবাইল নম্বর ও পাসওয়ার্ড আবশ্যক"]);
        http_response_code(400);
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE phone = :phone");
    $stmt->bindParam(':phone', $phone);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Password check (Production-e password_verify babohar kora shrey)
    if ($user && password_verify($password, $user['password'])) {
        if ($user['status'] !== 'approved') {
            echo json_encode(["status" => "error", "message" => "আপনার একাউন্টটি এখনও অনুমোদিত নয়"]);
            exit();
        }

        // Fictional Token generation for Simple Raw API
        $token = bin2hex(random_bytes(16));

        echo json_encode([
            "status" => "success",
            "message" => "লগইন সফল হয়েছে",
            "token" => $token,
            "user" => [
                "id" => $user['id'],
                "name" => $user['name'],
                "phone" => $user['phone'],
                "role" => $user['role']
            ]
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "ভুল ফোন নম্বর অথবা পাসওয়ার্ড"]);
    }
}
?>