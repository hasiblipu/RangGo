<?php
require_once 'config/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

// Data Fetching Queries
$orders = $conn->query("SELECT o.*, r.restaurant_name FROM orders o LEFT JOIN restaurants r ON o.restaurant_id = r.id ORDER BY o.id DESC")->fetchAll(PDO::FETCH_ASSOC);
$partners = $conn->query("SELECT u.id as user_id, u.phone, u.status, r.restaurant_name FROM users u JOIN restaurants r ON u.id = r.user_id WHERE u.role = 'partner'")->fetchAll(PDO::FETCH_ASSOC);
$products = $conn->query("SELECT p.*, r.restaurant_name FROM products p JOIN restaurants r ON p.restaurant_id = r.id ORDER BY p.id DESC")->fetchAll(PDO::FETCH_ASSOC);
$restaurants = $conn->query("SELECT * FROM restaurants")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>RangGo Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="admin-wrapper">
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>RangGo</h2>
        <ul>
            <li><a class="nav-link active" onclick="showTab('orders', this)">📦 Orders</a></li>
            <li><a class="nav-link" onclick="showTab('products', this)">🍕 Products</a></li>
            <li><a class="nav-link" onclick="showTab('partners', this)">🤝 Partners</a></li>
            <li><a class="nav-link" onclick="showTab('ads', this)">📢 Banner Ads</a></li>
        </ul>
    </div>

    <!-- Main Body -->
    <div class="main-content">
        <div class="header-bar">
            <h3>Admin Panel</h3>
            <div>
                <span>স্বাগতম, <strong><?= $_SESSION['admin_name'] ?></strong></span>
                <a href="logout.php" class="btn btn-cancel" style="margin-left: 15px;">Logout</a>
            </div>
        </div>

        <!-- 1. Orders Section -->
        <div id="orders" class="tab-content active">
            <div class="card">
                <h3>অর্ডার ম্যানেজমেন্ট</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer Name</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders as $o): ?>
                        <tr>
                            <td>#<?= $o['id'] ?></td>
                            <td><?= htmlspecialchars($o['customer_name']) ?></td>
                            <td><?= htmlspecialchars($o['customer_phone']) ?></td>
                            <td><?= htmlspecialchars($o['delivery_address']) ?></td>
                            <td>৳<?= $o['total_amount'] ?></td>
                            <td><span class="badge badge-<?= $o['status'] ?>"><?= $o['status'] ?></span></td>
                            <td>
                                <a href="process.php?action=update_order&id=<?= $o['id'] ?>&status=approved" class="btn btn-approve">Approve</a>
                                <a href="process.php?action=update_order&id=<?= $o['id'] ?>&status=cancelled" class="btn btn-cancel">Cancel</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. Products Section -->
        <div id="products" class="tab-content">
            <div class="card">
                <h3>একক প্রোডাক্ট যোগ করুন</h3>
                <form action="process.php?action=add_product" method="POST">
                    <div style="display:flex; gap:15px;">
                        <div class="form-group" style="flex:1;">
                            <label>রেস্টুরেন্ট সিলেক্ট করুন</label>
                            <select name="restaurant_id" required>
                                <?php foreach($restaurants as $r): ?>
                                    <option value="<?= $r['id'] ?>"><?= $r['restaurant_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label>খাবারের নাম</label>
                            <input type="text" name="name" required placeholder="Burger">
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label>দাম (৳)</label>
                            <input type="number" step="0.01" name="price" required placeholder="180">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">প্রোডাক্ট সেভ করুন</button>
                </form>
            </div>

            <div class="card">
                <h3>প্রোডাক্ট লিস্ট</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Restaurant</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $p): ?>
                        <tr>
                            <td>#<?= $p['id'] ?></td>
                            <td><?= htmlspecialchars($p['restaurant_name']) ?></td>
                            <td><?= htmlspecialchars($p['name']) ?></td>
                            <td>৳<?= $p['price'] ?></td>
                            <td>
                                <a href="process.php?action=delete_product&id=<?= $p['id'] ?>" class="btn btn-cancel" onclick="return confirm('ডিলিট নিশ্চিত করুন?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. Partners Section -->
        <div id="partners" class="tab-content">
            <div class="card">
                <h3>পার্টনার রেজিস্ট্রেশন রিকোয়েস্ট</h3>
                <table>
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Restaurant Name</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($partners as $partner): ?>
                        <tr>
                            <td>#<?= $partner['user_id'] ?></td>
                            <td><?= htmlspecialchars($partner['restaurant_name']) ?></td>
                            <td><?= htmlspecialchars($partner['phone']) ?></td>
                            <td><span class="badge badge-<?= $partner['status'] ?>"><?= $partner['status'] ?></span></td>
                            <td>
                                <a href="process.php?action=update_partner&id=<?= $partner['user_id'] ?>&status=approved" class="btn btn-approve">Approve Shop</a>
                                <a href="process.php?action=update_partner&id=<?= $partner['user_id'] ?>&status=rejected" class="btn btn-cancel">Reject</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 4. Ads Banner Upload Section -->
        <div id="ads" class="tab-content">
            <div class="card" style="max-width: 500px;">
                <h3>বিজ্ঞাপনী ব্যানার আপলোড</h3>
                <form action="process.php?action=upload_ad" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>বিজ্ঞাপনের শিরোনাম/টাইটেল</label>
                        <input type="text" name="title" required placeholder="30% OFF Banner">
                    </div>
                    <div class="form-group">
                        <label>ইমেজ ছবি (Banner Image)</label>
                        <input type="file" name="image" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary">ব্যানার আপলোড করুন</button>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
function showTab(tabId, element) {
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
    
    document.getElementById(tabId).classList.add('active');
    element.classList.add('active');
}
</script>

</body>
</html>