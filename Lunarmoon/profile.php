<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "lunar_registration"; 

$conn = new mysqli($host, $user, $pass, $dbname);

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$uid = $_SESSION['user_id'];

// 1. FETCH USER INFO
$stmt = $conn->prepare("SELECT fullname, email FROM users WHERE id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();

if (!$user_data) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$user_email = $user_data['email'];

// 2. FETCH TRANSACTIONS (Matching by customer_email)
// Ensure your 'orders' table has a column named 'customer_email'
$order_stmt = $conn->prepare("SELECT id, product_name, total_price, status, order_date FROM orders WHERE customer_email = ? ORDER BY order_date DESC");
$order_stmt->bind_param("s", $user_email);
$order_stmt->execute();
$orders_result = $order_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <title>My Profile - Lunar Moon</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Assistant:wght@300;400;600&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #fafafa;
            --text-color: #121212;
            --card-bg: #fff;
            --header-bg: #fff;
            --border-color: #eee;
            --accent: #d4af37;
        }

        [data-theme="dark"] {
            --bg-color: #121212;
            --text-color: #fff;
            --card-bg: #1e1e1e;
            --header-bg: #1a1a1a;
            --border-color: #333;
        }

        body { margin: 0; font-family: 'Assistant', sans-serif; background: var(--bg-color); color: var(--text-color); transition: 0.3s; }
        header { background: var(--header-bg); padding: 20px 50px; border-bottom: 1px solid var(--border-color); }
        .wrapper { max-width: 1200px; margin: 40px auto; display: grid; grid-template-columns: 350px 1fr; gap: 30px; padding: 0 20px; }
        .card { background: var(--card-bg); padding: 40px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); border-radius: 4px; height: fit-content; }
        
        h2 { font-family: 'Playfair Display', serif; font-weight: 400; margin-bottom: 25px; font-size: 22px; }
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 15px; }

        /* Form Styles */
        .form-group { margin-bottom: 20px; }
        .form-group label { font-size: 10px; text-transform: uppercase; color: #888; display: block; margin-bottom: 8px; letter-spacing: 1px; }
        .form-group input { width: 100%; padding: 10px 0; border: none; border-bottom: 1px solid var(--border-color); outline: none; font-size: 15px; background: transparent; color: var(--text-color); }
        
        .save-btn { width: 100%; padding: 16px; background: #121212; color: #fff; border: none; cursor: pointer; text-transform: uppercase; letter-spacing: 2px; font-size: 11px; transition: 0.3s; }
        [data-theme="dark"] .save-btn { background: #fff; color: #121212; }
        .save-btn:hover { background: var(--accent) !important; color: #fff !important; }

        /* Table Styles */
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 10px; text-transform: uppercase; color: #999; padding: 12px 10px; border-bottom: 1px solid var(--border-color); }
        td { padding: 20px 10px; font-size: 13px; border-bottom: 1px solid var(--border-color); }
        
        .status-badge { font-size: 9px; text-transform: uppercase; padding: 4px 10px; border-radius: 20px; font-weight: 700; letter-spacing: 0.5px; }
        .status-completed { background: #e8f5e9; color: #2e7d32; }
        .status-pending { background: #fff3e0; color: #ef6c00; }

        .order-search input { padding: 8px 12px 8px 35px; border: 1px solid var(--border-color); border-radius: 4px; font-size: 13px; background: transparent; color: var(--text-color); }
        
        .empty-state { text-align: center; padding: 60px 20px; color: #888; }
        .empty-state i { font-size: 40px; margin-bottom: 15px; opacity: 0.3; }

        .logout-link { display: block; margin-top: 25px; text-align: center; color: #c0392b; text-decoration: none; font-size: 11px; font-weight: 600; text-transform: uppercase; }

        @media (max-width: 900px) { .wrapper { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<header>
    <a href="index.php" style="text-decoration:none; color:var(--text-color); font-weight:600; letter-spacing:2px;">LUNAR MOON</a>
</header>

<div class="wrapper">
    <div class="card">
        <h2>Account Settings</h2>
        <form action="update_profile.php" method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="fullname" value="<?= htmlspecialchars($user_data['fullname']) ?>" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" value="<?= htmlspecialchars($user_data['email']) ?>" readonly style="opacity: 0.6; cursor: not-allowed;">
            </div>

            <h2 style="margin-top:40px; font-size: 18px;">Security</h2>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" placeholder="••••••••">
            </div>
            <button type="submit" class="save-btn">Save Changes</button>
        </form>
        <a href="logout.php" class="logout-link">Logout Account</a>
    </div>

    <div class="card">
        <div class="header-flex">
            <h2>Transactions</h2>
            <div class="order-search" style="position:relative;">
                <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:12px; top:10px; color:#aaa; font-size:12px;"></i>
                <input type="text" id="orderInput" onkeyup="searchOrders()" placeholder="Search order ID...">
            </div>
        </div>

        <?php if ($orders_result->num_rows > 0): ?>
            <table id="orderTable">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Product</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($order = $orders_result->fetch_assoc()): ?>
                    <tr>
                        <td style="font-weight:600;">#<?= $order['id'] ?></td>
                        <td><?= htmlspecialchars($order['product_name']) ?></td>
                        <td>₱<?= number_format($order['total_price'], 2) ?></td>
                        <td>
                            <?php 
                                $statusClass = (strtolower($order['status']) == 'completed') ? 'status-completed' : 'status-pending';
                            ?>
                            <span class="status-badge <?= $statusClass ?>"><?= $order['status'] ?></span>
                        </td>
                        <td style="color:#888;"><?= date('M d, Y', strtotime($order['order_date'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fa-solid fa-box-open"></i>
                <p>No transactions found for <strong><?= htmlspecialchars($user_email) ?></strong>.</p>
                <a href="index.php" style="color:var(--accent); font-size:13px; text-decoration:none;">Start shopping →</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Theme Management
    (function() {
        const saved = localStorage.getItem('lunar-theme');
        if (saved === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    })();

    // Search Logic
    function searchOrders() {
        let input = document.getElementById("orderInput");
        let filter = input.value.toUpperCase();
        let table = document.getElementById("orderTable");
        if(!table) return;
        let tr = table.getElementsByTagName("tr");

        for (let i = 1; i < tr.length; i++) {
            let tdID = tr[i].getElementsByTagName("td")[0];
            let tdProduct = tr[i].getElementsByTagName("td")[1];
            if (tdID || tdProduct) {
                let text = (tdID.textContent || tdID.innerText) + (tdProduct.textContent || tdProduct.innerText);
                tr[i].style.display = text.toUpperCase().indexOf(filter) > -1 ? "" : "none";
            }
        }
    }
</script>

</body>
</html>