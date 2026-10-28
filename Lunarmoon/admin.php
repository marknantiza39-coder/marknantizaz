<?php
session_start();

// 1. SECURITY CHECK
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$host = "localhost"; $user = "root"; $pass = ""; $dbname = "lunar_registration"; 
$conn = new mysqli($host, $user, $pass, $dbname);

// --- VIEW TOGGLE ---
$view = isset($_GET['view']) ? $_GET['view'] : 'inventory';

// 2. HANDLE DELETE (Inventory)
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM products WHERE id = $id");
    header("Location: admin.php?view=inventory");
}

// 3. HANDLE ADD OR UPDATE PRODUCT
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $product_id = $_POST['product_id']; 
    
    $image_path = $_POST['existing_image']; 
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        $file_name = time() . "_" . basename($_FILES["image"]["name"]); 
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        }
    }

    if (!empty($product_id)) {
        $stmt = $conn->prepare("UPDATE products SET name=?, price=?, category=?, image=? WHERE id=?");
        $stmt->bind_param("sdssi", $name, $price, $category, $image_path, $product_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name, price, category, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdss", $name, $price, $category, $image_path);
    }
    $stmt->execute();
    header("Location: admin.php?view=inventory");
}

// HANDLE STATUS UPDATE
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
    header("Location: admin.php?view=orders");
}

// 4. FETCH DATA
$edit_item = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM products WHERE id = $id");
    $edit_item = $res->fetch_assoc();
}

// SEARCH LOGIC
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
    $sql = "SELECT * FROM products WHERE name LIKE '%$search_query%' OR category LIKE '%$search_query%' ORDER BY id DESC";
} else {
    $sql = "SELECT * FROM products ORDER BY id DESC";
}
$products = $conn->query($sql);

// FETCH ORDERS
$orders = $conn->query("SELECT * FROM orders ORDER BY order_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Lunar Admin | <?= ucfirst($view) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Assistant:wght@300;600&family=Playfair+Display:ital@1&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Assistant', sans-serif; margin: 0; background: #f9f9f9; display: flex; }
        .sidebar { width: 250px; background: #121212; color: #fff; height: 100vh; padding: 40px 20px; position: fixed; }
        .sidebar h2 { font-family: 'Playfair Display', serif; font-style: italic; color: #fff; margin-bottom: 30px; }
        .sidebar a { display: block; color: #888; text-decoration: none; padding: 15px 0; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; }
        .sidebar a:hover, .sidebar a.active { color: #d4af37; }
        
        .main { margin-left: 290px; padding: 40px; width: calc(100% - 290px); }
        .card { background: #fff; padding: 30px; border: 1px solid #eee; margin-bottom: 30px; }
        h2 { font-family: 'Playfair Display', serif; font-style: italic; margin-top: 0; }
        
        .search-container { display: flex; gap: 10px; margin-bottom: 20px; }
        .search-input { flex: 1; padding: 10px; border: 1px solid #ddd; }
        .btn-search { background: #121212; color: white; border: none; padding: 0 20px; cursor: pointer; text-transform: uppercase; font-size: 11px; }

        .form-group { margin-bottom: 15px; }
        label { display: block; font-size: 11px; text-transform: uppercase; color: #888; margin-bottom: 5px; }
        input, select { width: 100%; padding: 12px; border: 1px solid #ddd; box-sizing: border-box; }
        
        .btn-save { background: #121212; color: #fff; border: none; padding: 15px 30px; cursor: pointer; text-transform: uppercase; letter-spacing: 2px; width: 100%; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; }
        th { text-align: left; padding: 15px; border-bottom: 2px solid #121212; font-size: 12px; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #eee; font-size: 14px; }
        .thumb { width: 50px; height: 50px; object-fit: cover; }
        
        .status-select { padding: 5px; border: 1px solid #ddd; font-family: inherit; font-size: 12px; cursor: pointer; }

        .btn-print { background: #f1c40f; color: #000; padding: 8px 12px; text-decoration: none; font-size: 11px; font-weight: 600; border-radius: 3px; display: inline-block; border: none; cursor: pointer; }
        .actions a { margin-right: 15px; text-decoration: none; font-size: 12px; font-weight: 600; }
        .edit-link { color: #2980b9; }
        .del-link { color: #c0392b; }

        /* Hide screen elements during general print */
        @media print {
            .sidebar, .main { display: none !important; }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Lunar Admin</h2>
    <a href="admin.php?view=inventory" class="<?= $view == 'inventory' ? 'active' : '' ?>">Inventory</a>
    <a href="admin.php?view=orders" class="<?= $view == 'orders' ? 'active' : '' ?>">Customer Orders</a>
    <a href="index.php">View Store</a>
    <a href="logout.php">Sign Out</a>
</div>

<div class="main">
    
    <?php if($view == 'inventory'): ?>
        <div class="card">
            <h2><?= $edit_item ? 'Edit Piece' : 'Add New Masterpiece' ?></h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="product_id" value="<?= $edit_item['id'] ?? '' ?>">
                <input type="hidden" name="existing_image" value="<?= $edit_item['image'] ?? '' ?>">
                <div class="form-group"><label>Product Name</label><input type="text" name="name" value="<?= $edit_item['name'] ?? '' ?>" required></div>
                <div style="display:flex; gap:20px;">
                    <div class="form-group" style="flex:1"><label>Price (₱)</label><input type="number" step="0.01" name="price" value="<?= $edit_item['price'] ?? '' ?>" required></div>
                    <div class="form-group" style="flex:1">
                        <label>Category</label>
                        <select name="category">
                            <option value="rings" <?= (isset($edit_item) && $edit_item['category'] == 'rings') ? 'selected' : '' ?>>Rings</option>
                            <option value="necklaces" <?= (isset($edit_item) && $edit_item['category'] == 'necklaces') ? 'selected' : '' ?>>Necklaces</option>
                            <option value="earrings" <?= (isset($edit_item) && $edit_item['category'] == 'earrings') ? 'selected' : '' ?>>Earrings</option>
                        </select>
                    </div>
                </div>
                <div class="form-group"><label>Product Image</label><input type="file" name="image" accept="image/*"></div>
                <button type="submit" name="save_product" class="btn-save"><?= $edit_item ? 'Update Product' : 'Add to Collection' ?></button>
            </form>
        </div>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2>Current Inventory</h2>
                <form method="GET" action="admin.php" class="search-container">
                    <input type="hidden" name="view" value="inventory">
                    <input type="text" name="search" class="search-input" placeholder="Search..." value="<?= htmlspecialchars($search_query) ?>">
                    <button type="submit" class="btn-search">Search</button>
                </form>
            </div>
            <table>
                <thead><tr><th>Image</th><th>Name</th><th>Price</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php while($row = $products->fetch_assoc()): ?>
                    <tr>
                        <td><img src="<?= $row['image'] ?>" class="thumb"></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td>₱<?= number_format($row['price'], 2) ?></td>
                        <td class="actions">
                            <a href="admin.php?view=inventory&edit=<?= $row['id'] ?>" class="edit-link">Edit</a>
                            <a href="admin.php?view=inventory&delete=<?= $row['id'] ?>" class="del-link" onclick="return confirm('Delete?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>
        <div class="card">
            <h2>Customer Orders</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Address</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($orders && $orders->num_rows > 0): ?>
                        <?php while($order = $orders->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['customer_name']) ?><br><small><?= $order['customer_email'] ?></small></td>
                            <td><?= htmlspecialchars($order['shipping_address']) ?></td>
                            <td><?= htmlspecialchars($order['product_name']) ?></td>
                            <td>₱<?= number_format($order['total_price'], 2) ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <input type="hidden" name="update_status" value="1">
                                    <select name="status" class="status-select" onchange="this.form.submit()">
                                        <option value="Pending" <?= ($order['status'] ?? '') == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="Processing" <?= ($order['status'] ?? '') == 'Processing' ? 'selected' : '' ?>>Processing</option>
                                        <option value="Shipped" <?= ($order['status'] ?? '') == 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                                        <option value="Delivered" <?= ($order['status'] ?? '') == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                                    </select>
                                </form>
                            </td>
                            <td><?= date('M d', strtotime($order['order_date'])) ?></td>
                            <td>
                                <button onclick="printSingleReceipt(
                                    '<?= $order['id'] ?>', 
                                    '<?= addslashes($order['customer_name']) ?>', 
                                    '<?= addslashes($order['shipping_address']) ?>', 
                                    '<?= addslashes($order['product_name']) ?>', 
                                    '<?= number_format($order['total_price'], 2) ?>',
                                    '<?= date('M d, Y', strtotime($order['order_date'])) ?>'
                                )" class="btn-print">
                                    <i class="fa fa-print"></i> Print Receipt
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="8" style="text-align:center;">No orders found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

<script>
function printSingleReceipt(id, name, address, items, total, date) {
    var printWindow = window.open('', '_blank', 'height=600,width=800');
    
    printWindow.document.write('<html><head><title>Receipt - Order #' + id + '</title>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: "Assistant", sans-serif; padding: 40px; color: #333; }');
    printWindow.document.write('.header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 30px; }');
    printWindow.document.write('.header h1 { font-family: "Playfair Display", serif; font-style: italic; margin: 0; }');
    printWindow.document.write('.details { margin-bottom: 30px; line-height: 1.6; }');
    printWindow.document.write('.details strong { text-transform: uppercase; font-size: 12px; color: #888; display: inline-block; width: 120px; }');
    printWindow.document.write('.item-table { width: 100%; border-collapse: collapse; margin-top: 20px; }');
    printWindow.document.write('.item-table th { text-align: left; border-bottom: 1px solid #000; padding: 10px 0; font-size: 13px; }');
    printWindow.document.write('.item-table td { padding: 15px 0; border-bottom: 1px solid #eee; }');
    printWindow.document.write('.total-row { text-align: right; font-size: 18px; font-weight: bold; margin-top: 20px; }');
    printWindow.document.write('.footer { margin-top: 50px; text-align: center; font-size: 12px; font-style: italic; }');
    printWindow.document.write('</style></head><body>');
    
    printWindow.document.write('<div class="header"><h1>Lunar Moon Jewelry</h1><p>OFFICIAL CUSTOMER RECEIPT</p></div>');
    
    printWindow.document.write('<div class="details">');
    printWindow.document.write('<p><strong>Order ID:</strong> #' + id + '</p>');
    printWindow.document.write('<p><strong>Date:</strong> ' + date + '</p>');
    printWindow.document.write('<p><strong>Customer:</strong> ' + name + '</p>');
    printWindow.document.write('<p><strong>Shipping:</strong> ' + address + '</p>');
    printWindow.document.write('</div>');
    
    printWindow.document.write('<table class="item-table"><thead><tr><th>PRODUCT DESCRIPTION</th><th style="text-align:right">AMOUNT</th></tr></thead><tbody>');
    printWindow.document.write('<tr><td>' + items + '</td><td style="text-align:right">₱' + total + '</td></tr>');
    printWindow.document.write('</tbody></table>');
    
    printWindow.document.write('<div class="total-row">TOTAL PAID: ₱' + total + '</div>');
    printWindow.document.write('<div class="footer">Thank you for choosing Lunar Moon Jewelry. Keep this receipt for your records.</div>');
    
    printWindow.document.write('</body></html>');
    
    printWindow.document.close();
    printWindow.focus();
    
    // Give it a tiny moment to load fonts/styles before opening print dialog
    setTimeout(function() {
        printWindow.print();
        printWindow.close();
    }, 500);
}
</script>

</body>
</html>