<?php
// api/restaurants.php
require_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$restaurant_id = $_GET['id'] ?? null;
$category = $_GET['category'] ?? null;

if ($method === 'GET') {
    try {
        // ১. কোনো নির্দিষ্ট রেস্টুরেন্টের বিস্তারিত ও মেনু ডাটা ফেচ করা (Single Restaurant View)
        if ($restaurant_id) {
            // রেস্টুরেন্টের তথ্য আনা
            $stmtRes = $conn->prepare("
                SELECT r.id, r.restaurant_name, r.address, r.cuisine_type, u.status 
                FROM restaurants r 
                JOIN users u ON r.user_id = u.id 
                WHERE r.id = :id AND u.status = 'approved'
            ");
            $stmtRes->execute([':id' => $restaurant_id]);
            $restaurant = $stmtRes->fetch(PDO::FETCH_ASSOC);

            if (!$restaurant) {
                echo json_encode(["status" => "error", "message" => "রেস্টুরেন্টটি পাওয়া যায়নি অথবা অনুমোদিত নয়"]);
                http_response_code(404);
                exit();
            }

            // ওই রেস্টুরেন্টের খাবারের মেনু আনা
            $stmtMenu = $conn->prepare("SELECT * FROM products WHERE restaurant_id = :restaurant_id ORDER BY id DESC");
            $stmtMenu->execute([':restaurant_id' => $restaurant_id]);
            $menu = $stmtMenu->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "status" => "success",
                "data" => [
                    "restaurant" => $restaurant,
                    "menu" => $menu
                ]
            ]);
            exit();
        }

        // ২. সকল অনুমোদিত রেস্টুরেন্টের লিস্ট ফেচ করা (Homepage Restaurant Grid)
        if ($category && $category !== 'All') {
            // ক্যাটাগরি ফিল্টার অনুযায়ী রেস্টুরেন্ট খোঁজা
            $stmt = $conn->prepare("
                SELECT r.id, r.restaurant_name, r.address, r.cuisine_type 
                FROM restaurants r 
                JOIN users u ON r.user_id = u.id 
                WHERE u.status = 'approved' AND r.cuisine_type LIKE :category 
                ORDER BY r.id DESC
            ");
            $stmt->execute([':category' => '%' . $category . '%']);
        } else {
            // সব অনুমোদিত রেস্টুরেন্ট
            $stmt = $conn->prepare("
                SELECT r.id, r.restaurant_name, r.address, r.cuisine_type 
                FROM restaurants r 
                JOIN users u ON r.user_id = u.id 
                WHERE u.status = 'approved' 
                ORDER BY r.id DESC
            ");
            $stmt->execute();
        }

        $restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            "status" => "success",
            "data" => $restaurants
        ]);

    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "ডাটাবেস সমস্যা: " . $e->getMessage()]);
        http_response_code(500);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid Request Method"]);
    http_response_code(405);
}
?>