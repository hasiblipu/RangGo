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
$ads = $conn->query("SELECT * FROM ads ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>RangGo Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .edit-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 100; }
        .edit-modal.active { display: flex; }
        .modal-box { background: #fff; padding: 25px; border-radius: 12px; width: 450px; max-width: 90%; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .btn-warning { background-color: #ffc107; color: #000; }
    </style>
</head>
<body>

<div class="admin-wrapper">
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>RangGo</h2>
        <ul>
            <li><a class="nav-link active" onclick="showTab('orders', this)">📦 Orders</a></li>
            <li><a class="nav-link" onclick="showTab('products', this)">🍕 Products Management</a></li>
            <li><a class="nav-link" onclick="showTab('partners', this)">🤝 Partners</a></li>
            <li><a class="nav-link" onclick="showTab('ads', this)">📢 Banner Ads</a></li>
        </ul>
    </div>

    <!-- Main Body -->
    <div class="main-content">
        <div class="header-bar">
            <h3>Admin Panel</h3>
            <div>
                <span>স্বাগতম, <strong><?= htmlspecialchars($_SESSION['admin_name']) ?></strong></span>
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

        <!-- 2. Products Section (Single, Bulk CSV Upload & Edit) -->
        <div id="products" class="tab-content">
            
            <div class="grid-2">
                <!-- Single Product Form -->
                <div class="card">
                    <h3>একক প্রোডাক্ট যোগ করুন</h3>
                    <form action="process.php?action=add_product" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>রেস্টুরেন্ট সিলেক্ট করুন</label>
                            <select name="restaurant_id" required>
                                <?php foreach($restaurants as $r): ?>
                                    <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['restaurant_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>খাবারের নাম</label>
                            <input type="text" name="name" required placeholder="Chicken Burger">
                        </div>
                        <div class="form-group">
                            <label>দাম (৳)</label>
                            <input type="number" step="0.01" name="price" required placeholder="180">
                        </div>
                        <div class="form-group">
                            <label>খাবারের ছবি (Image Upload)</label>
                            <input type="file" name="image" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%;">প্রোডাক্ট সেভ করুন</button>
                    </form>
                </div>

                <!-- Bulk Product CSV Upload -->
                <div class="card" style="border: 2px dashed #FF4500; background: #fff8f5;">
                    <h3 style="color: #FF4500;">📁 বাল্ক প্রোডাক্ট আপলোড (CSV File)</h3>
                    <p style="font-size: 12px; color: #666; margin-bottom: 15px;">
                        একসাথে অনেক প্রোডাক্ট আপলোড করতে CSV ফাইল সিলেক্ট করুন। <br>
                        <strong>ফাইল ফরম্যাট:</strong> Restaurant_ID, Name, Description, Price
                    </p>
                    <form action="process.php?action=bulk_product_upload" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>CSV ফাইল সিলেক্ট করুন (.csv)</label>
                            <input type="file" name="csv_file" accept=".csv" required>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%; background-color: #28a745;">বাল্ক আপলোড নিশ্চিত করুন</button>
                    </form>
                    
                    <div style="margin-top: 15px; font-size: 11px; background: #eee; padding: 10px; border-radius: 6px;">
                        <strong>Sample CSV Format:</strong><br>
                        1, Chicken Burger, Crispy burger, 180<br>
                        1, Beef Burger, Cheese burger, 220
                    </div>
                </div>
            </div>

            <!-- Products List Table -->
            <div class="card">
                <h3>প্রোডাক্ট লিস্ট</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
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
                            <td>
                                <?php if($p['image']): ?>
                                    <img src="<?= htmlspecialchars($p['image']) ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                <?php else: ?>
                                    <span style="color:#aaa; font-size:11px;">No Image</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($p['restaurant_name']) ?></td>
                            <td><?= htmlspecialchars($p['name']) ?></td>
                            <td>৳<?= $p['price'] ?></td>
                            <td>
                                <button class="btn btn-warning" onclick='openEditModal(<?= json_encode($p) ?>)'>Edit</button>
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

        <!-- 4. Ads Banner Upload Section & List -->
        <div id="ads" class="tab-content">
            <div class="grid-2">
                <div class="card">
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

                <div class="card">
                    <h3>আপলোডকৃত ব্যানারের তালিকা</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Preview</th>
                                <th>Title</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($ads as $ad): ?>
                            <tr>
                                <td><img src="<?= htmlspecialchars($ad['image_url']) ?>" style="height: 40px; border-radius: 4px;"></td>
                                <td><?= htmlspecialchars($ad['title']) ?></td>
                                <td>
                                    <a href="process.php?action=delete_ad&id=<?= $ad['id'] ?>" class="btn btn-cancel">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal for Product Editing -->
<div id="editModal" class="edit-modal">
    <div class="modal-box">
        <h3 style="margin-bottom: 15px; color: #FF4500;">প্রোডাক্ট এডিট করুন</h3>
        <form action="process.php?action=edit_product" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" id="edit_id">
            
            <div class="form-group">
                <label>খাবারের নাম</label>
                <input type="text" name="name" id="edit_name" required>
            </div>
            
            <div class="form-group">
                <label>দাম (৳)</label>
                <input type="number" step="0.01" name="price" id="edit_price" required>
            </div>
            
            <div class="form-group">
                <label>ছবি পরিবর্তন করতে চাইলে সিলেক্ট করুন (Optional)</label>
                <input type="file" name="image" accept="image/*">
            </div>

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn btn-approve" style="flex: 1;">আপডেট করুন</button>
                <button type="button" class="btn btn-cancel" style="flex: 1;" onclick="closeEditModal()">বাতিল</button>
            </div>
        </form>
    </div>
</div>

<script>
function showTab(tabId, element) {
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
    
    document.getElementById(tabId).classList.add('active');
    element.classList.add('active');
}

function openEditModal(product) {
    document.getElementById('edit_id').value = product.id;
    document.getElementById('edit_name').value = product.name;
    document.getElementById('edit_price').value = product.price;
    document.getElementById('editModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}
</script>

</body>
</html>