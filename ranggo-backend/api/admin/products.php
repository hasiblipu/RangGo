<?php
require_once '../../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'single';

// 1. EDIT PRODUCT (PUT Method)
if ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data['image'])) {
        $stmt = $conn->prepare("UPDATE products SET name = :name, description = :description, price = :price, image = :image WHERE id = :id");
        $stmt->bindParam(':image', $data['image']);
    } else {
        $stmt = $conn->prepare("UPDATE products SET name = :name, description = :description, price = :price WHERE id = :id");
    }

    $stmt->execute([
        ':name' => $data['name'],
        ':description' => $data['description'] ?? '',
        ':price' => $data['price'],
        ':id' => $data['id']
    ]);
    echo json_encode(["status" => "success", "message" => "Product updated"]);
    exit();
}

// 2. PRODUCT IMAGE UPLOAD
if ($method === 'POST' && $action === 'upload_image') {
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $fileName = time() . '_' . $_FILES['product_image']['name'];
        $uploadDir = '../../uploads/products/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        move_uploaded_file($_FILES['product_image']['tmp_name'], $uploadDir . $fileName);
        echo json_encode(["status" => "success", "image_url" => 'uploads/products/' . $fileName]);
    }
    exit();
}

// 3. ADD PRODUCT (Single & Bulk)
if ($method === 'POST') {
    if ($action === 'single') {
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $conn->prepare("INSERT INTO products (restaurant_id, name, description, price, image) VALUES (:restaurant_id, :name, :description, :price, :image)");
        $stmt->execute([
            ':restaurant_id' => $data['restaurant_id'],
            ':name' => $data['name'],
            ':description' => $data['description'] ?? '',
            ':price' => $data['price'],
            ':image' => $data['image'] ?? null
        ]);
        echo json_encode(["status" => "success", "message" => "Product added"]);
    }

    if ($action === 'bulk') {
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $conn->prepare("INSERT INTO products (restaurant_id, name, description, price, image) VALUES (:restaurant_id, :name, :description, :price, :image)");
        $conn->beginTransaction();
        foreach ($data as $p) {
            $stmt->execute([
                ':restaurant_id' => $p['restaurant_id'],
                ':name' => $p['name'],
                ':description' => $p['description'] ?? '',
                ':price' => $p['price'],
                ':image' => $p['image'] ?? null
            ]);
        }
        $conn->commit();
        echo json_encode(["status" => "success", "message" => "Bulk products added"]);
    }
    exit();
}

// 4. DELETE PRODUCT
if ($method === 'DELETE') {
    $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute([':id' => $_GET['id']]);
    echo json_encode(["status" => "success", "message" => "Product deleted"]);
    exit();
}
?>