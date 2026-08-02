<?php
require_once 'config/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    exit("Unauthorized Access");
}

$action = $_GET['action'] ?? '';

// 1. Single Product Upload (With Image)
if ($action === 'add_product' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $restaurant_id = $_POST['restaurant_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $imagePath = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileName = time() . '_' . $_FILES['image']['name'];
        $uploadDir = 'uploads/products/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName)) {
            $imagePath = $uploadDir . $fileName;
        }
    }

    $stmt = $conn->prepare("INSERT INTO products (restaurant_id, name, price, image) VALUES (:r_id, :name, :price, :image)");
    $stmt->execute([':r_id' => $restaurant_id, ':name' => $name, ':price' => $price, ':image' => $imagePath]);

    header("Location: dashboard.php");
    exit();
}

// 2. BULK PRODUCT UPLOAD (CSV File Processing)
if ($action === 'bulk_product_upload' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, "r");

        if ($handle !== FALSE) {
            $stmt = $conn->prepare("INSERT INTO products (restaurant_id, name, description, price) VALUES (:r_id, :name, :desc, :price)");
            
            $conn->beginTransaction();
            try {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if (isset($data[0], $data[1], $data[3])) {
                        $stmt->execute([
                            ':r_id'  => trim($data[0]),
                            ':name'  => trim($data[1]),
                            ':desc'  => isset($data[2]) ? trim($data[2]) : '',
                            ':price' => trim($data[3])
                        ]);
                    }
                }
                $conn->commit();
                fclose($handle);
            } catch (Exception $e) {
                $conn->rollBack();
            }
        }
    }
    header("Location: dashboard.php");
    exit();
}

// 3. EDIT PRODUCT (Name, Price & New Image)
if ($action === 'edit_product' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['product_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileName = time() . '_' . $_FILES['image']['name'];
        $uploadDir = 'uploads/products/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName)) {
            $imagePath = $uploadDir . $fileName;
            $stmt = $conn->prepare("UPDATE products SET name = :name, price = :price, image = :image WHERE id = :id");
            $stmt->execute([':name' => $name, ':price' => $price, ':image' => $imagePath, ':id' => $id]);
        }
    } else {
        $stmt = $conn->prepare("UPDATE products SET name = :name, price = :price WHERE id = :id");
        $stmt->execute([':name' => $name, ':price' => $price, ':id' => $id]);
    }

    header("Location: dashboard.php");
    exit();
}

// 4. Delete Product
if ($action === 'delete_product') {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);

    header("Location: dashboard.php");
    exit();
}

// 5. Update Order Status
if ($action === 'update_order') {
    $id = $_GET['id'];
    $status = $_GET['status'];
    $stmt = $conn->prepare("UPDATE orders SET status = :status WHERE id = :id");
    $stmt->execute([':status' => $status, ':id' => $id]);

    header("Location: dashboard.php");
    exit();
}

// 6. Update Partner Status
if ($action === 'update_partner') {
    $id = $_GET['id'];
    $status = $_GET['status'];
    $stmt = $conn->prepare("UPDATE users SET status = :status WHERE id = :id AND role = 'partner'");
    $stmt->execute([':status' => $status, ':id' => $id]);

    header("Location: dashboard.php");
    exit();
}

// 7. Upload Banner Ad
if ($action === 'upload_ad' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileName = time() . '_' . $_FILES['image']['name'];
        $uploadDir = 'uploads/ads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName)) {
            $imageUrl = 'uploads/ads/' . $fileName;
            $stmt = $conn->prepare("INSERT INTO ads (title, image_url) VALUES (:title, :url)");
            $stmt->execute([':title' => $title, ':url' => $imageUrl]);
        }
    }
    header("Location: dashboard.php");
    exit();
}

// 8. Delete Banner Ad
if ($action === 'delete_ad') {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM ads WHERE id = :id");
    $stmt->execute([':id' => $id]);

    header("Location: dashboard.php");
    exit();
}
?>