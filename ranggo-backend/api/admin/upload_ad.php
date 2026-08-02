<?php
require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? 'Ad Banner';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileName = time() . '_' . $_FILES['image']['name'];
        $uploadFileDir = '../../uploads/ads/';
        if (!is_dir($uploadFileDir)) mkdir($uploadFileDir, 0777, true);
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFileDir . $fileName)) {
            $imageUrl = 'uploads/ads/' . $fileName;
            $stmt = $conn->prepare("INSERT INTO ads (title, image_url) VALUES (:title, :image_url)");
            $stmt->execute([':title' => $title, ':image_url' => $imageUrl]);
            echo json_encode(["status" => "success", "url" => $imageUrl]);
        }
    }
}
?>