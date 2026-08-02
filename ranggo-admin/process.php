<?php
require_once 'config/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    exit("Unauthorized");
}

$action = $_GET['action'] ?? '';

// 1. Update Order Status
if ($action === 'update_order') {
    $id = $_GET['id'];
    $status = $_GET['status'];

    $stmt = $conn->prepare("UPDATE orders SET status = :status WHERE id = :id");
    $stmt->execute([':status' => $status, ':id' => $id]);
    
    header("Location: dashboard.php");
    exit();
}

// 2. Add Single Product
if ($action === 'add_product' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $restaurant_id = $_POST['restaurant_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("INSERT INTO products (restaurant_id, name, price) VALUES (:r_id, :name, :price)");
    $stmt->execute([':r_id' => $restaurant_id, ':name' => $name, ':price' => $price]);

    header("Location: dashboard.php");
    exit();
}

// 3. Delete Product
if ($action === 'delete_product') {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);

    header("Location: dashboard.php");
    exit();
}

// 4. Update Partner Status
if ($action === 'update_partner') {
    $id = $_GET['id'];
    $status = $_GET['status'];

    $stmt = $conn->prepare("UPDATE users SET status = :status WHERE id = :id AND role = 'partner'");
    $stmt->execute([':status' => $status, ':id' => $id]);

    header("Location: dashboard.php");
    exit();
}

// 5. Upload Banner Ad
if ($action === 'upload_ad' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileName = time() . '_' . $_FILES['image']['name'];
        $uploadDir = 'uploads/ads/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName)) {
            $imageUrl = 'uploads/ads/' . $fileName;
            $stmt = $conn->prepare("INSERT INTO ads (title, image_url) VALUES (:title, :url)");
            $stmt->execute([':title' => $title, ':url' => $imageUrl]);
        }
    }

    header("Location: dashboard.php");
    exit();
}
?>