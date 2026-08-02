<?php
require_once 'config/db.php';

$message = '';

if (isset($_POST['login'])) {
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE phone = :phone AND role = 'admin'");
    $stmt->execute([':phone' => $phone]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_name'] = $admin['name'];
        header("Location: dashboard.php");
        exit();
    } else {
        $message = "অবৈধ লগইন তথ্য অথবা আপনি এডমিন নন!";
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>RangGo Admin Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="display:flex; justify-content:center; align-items:center; height:100vh;">

    <div class="card" style="width: 350px;">
        <h2 style="color:#FF4500; text-align:center; margin-bottom:20px;">RangGo Admin</h2>
        
        <?php if($message): ?>
            <p style="color:red; font-size:12px; margin-bottom:10px;"><?= $message ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label>ফোন নম্বর</label>
                <input type="text" name="phone" required placeholder="017XXXXXXXX">
            </div>
            <div class="form-group">
                <label>পাসওয়ার্ড</label>
                <input type="password" name="password" required placeholder="******">
            </div>
            <button type="submit" name="login" class="btn btn-primary" style="width:100%; padding:10px;">লগইন করুন</button>
        </form>
    </div>

</body>
</html>