<?php
session_start();

// 1. DATABASE CONNECTION
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "lunar_registration"; 
$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. GET FILTER PARAMETERS
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'relevance';
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 100000;

$results = [];

// 3. BUILD DYNAMIC SQL
$sql = "SELECT * FROM products WHERE name LIKE ?";
$search_term = "%$query%";
$sql .= " AND price <= ?";

if ($sort == 'price_low') {
    $sql .= " ORDER BY price ASC";
} elseif ($sort == 'price_high') {
    $sql .= " ORDER BY price DESC";
} elseif ($sort == 'newest') {
    $sql .= " ORDER BY id DESC";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $search_term, $max_price);
$stmt->execute();
$db_result = $stmt->get_result();

while ($row = $db_result->fetch_assoc()) {
    $results[] = $row;
}

// Calculate cart count
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lunar Moon | <?= htmlspecialchars($query) ?> Collection</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Assistant:wght@300;400;600&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    
    <style>
        /* --- ADDED: THEME VARIABLES --- */
        :root {
            --bg-color: #ffffff;
            --text-color: #121212;
            --header-bg: #ffffff;
            --border-color: #f2f2f2;
            --card-bg: #f9f9f9;
        }

        [data-theme="dark"] {
            --bg-color: #121212;
            --text-color: #ffffff;
            --header-bg: #1a1a1a;
            --border-color: #333333;
            --card-bg: #1e1e1e;
        }

        body { margin: 0; font-family: 'Assistant', sans-serif; color: var(--text-color); background-color: var(--bg-color); transition: background 0.3s; }
        
        .branded-header {
            display: flex; flex-direction: column; align-items: center;
            padding: 30px 20px; border-bottom: 1px solid var(--border-color); position: relative;
            background-color: var(--header-bg);
        }
        .branded-header .logo-link img { height: 120px; width: auto; mix-blend-mode: multiply; }
        
        /* Logo adjustment for dark mode */
        [data-theme="dark"] .branded-header .logo-link img { filter: invert(1) hue-rotate(180deg); mix-blend-mode: screen; }

        .cart-link { position: absolute; right: 50px; top: 50%; transform: translateY(-50%); text-decoration: none; color: var(--text-color); font-size: 20px; }
        #cart-count { position: absolute; top: -10px; right: -10px; background: #d4af37; color: #fff; font-size: 10px; padding: 2px 6px; border-radius: 50%; }

        .back-nav { margin-top: 15px; }
        .back-nav a { text-decoration: none; color: #888; font-size: 11px; text-transform: uppercase; letter-spacing: 2px; font-weight: 600; }

        .container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
        .search-title { text-align: center; margin-bottom: 40px; }
        .search-title h1 { font-family: 'Playfair Display', serif; font-size: 32px; font-weight: 400; font-style: italic; }

        .toolbar { 
            display: flex; justify-content: space-between; align-items: center; 
            padding: 15px 0; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);
            margin-bottom: 40px; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;
        }
        .filter-controls { display: flex; gap: 30px; }
        .filter-group select { border: none; background: transparent; font-family: inherit; color: var(--text-color); cursor: pointer; outline: none; }

        /* PRODUCT GRID */
        .results-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 40px 20px; }
        .product-card { text-align: center; position: relative; }
        .product-link { text-decoration: none; color: inherit; display: block; }
        .img-container { aspect-ratio: 1/1; overflow: hidden; background: var(--card-bg); margin-bottom: 15px; }
        .img-container img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.8s ease; }
        .product-card:hover img { transform: scale(1.1); }
        .product-card h3 { font-family: 'Playfair Display', serif; font-size: 18px; font-weight: 400; margin: 0 0 8px; }
        .price { font-size: 14px; font-weight: 600; letter-spacing: 1px; margin-bottom: 15px; }

        .add-btn {
            background: #121212; color: #fff; border: none; width: 100%;
            padding: 12px; font-size: 11px; text-transform: uppercase;
            letter-spacing: 2px; cursor: pointer; transition: 0.3s;
        }
        /* Dark mode button adjustment */
        [data-theme="dark"] .add-btn { background: #fff; color: #121212; }
        .add-btn:hover { background: #d4af37 !important; color: #fff !important; }

        .empty-results { grid-column: 1 / -1; text-align: center; padding: 100px 0; }

        @media (max-width: 1000px) { .results-grid { grid-template-columns: repeat(2, 1fr); } }
    </style>
</head>
<body>

<header class="branded-header">
    <a href="index.php" class="logo-link"><img src="logo2.jpg"></a>
    <a href="cart.php" class="cart-link">
        <i class="fa-solid fa-cart-shopping"></i>
        <span id="cart-count"><?= $cart_count ?></span>
    </a>
    <div class="back-nav"><a href="index.php"><i class="fa-solid fa-chevron-left"></i> Back to Home</a></div>
</header>

<div class="container">
    <div class="search-title">
        <h1>Collection: <?= htmlspecialchars($query) ?></h1>
        <p><?= count($results) ?> products found</p>
    </div>

    <form id="filterForm" action="search.php" method="GET">
        <input type="hidden" name="q" value="<?= htmlspecialchars($query) ?>">
        <div class="toolbar">
            <div class="filter-controls">
                <div class="filter-group">
                    <span>Under:</span>
                    <select name="max_price" onchange="this.form.submit()">
                        <option value="100000" <?= $max_price == 100000 ? 'selected' : '' ?>>Any Price</option>
                        <option value="5000" <?= $max_price == 5000 ? 'selected' : '' ?>>₱5,000</option>
                        <option value="10000" <?= $max_price == 10000 ? 'selected' : '' ?>>₱10,000</option>
                        <option value="20000" <?= $max_price == 20000 ? 'selected' : '' ?>>₱20,000</option>
                    </select>
                </div>
                <div class="filter-group">
                    <span>Sort:</span>
                    <select name="sort" onchange="this.form.submit()">
                        <option value="relevance" <?= $sort == 'relevance' ? 'selected' : '' ?>>Featured</option>
                        <option value="price_low" <?= $sort == 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                        <option value="price_high" <?= $sort == 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                    </select>
                </div>
            </div>
        </div>
    </form>

    <div class="results-grid">
        <?php if (!empty($results)): ?>
            <?php foreach ($results as $p): ?>
                <div class="product-card">
                    <a href="product-details.php?id=<?= $p['id'] ?>" class="product-link">
                        <div class="img-container">
                            <img src="<?= htmlspecialchars($p['image']) ?>">
                        </div>
                        <h3><?= htmlspecialchars($p['name']) ?></h3>
                        <div class="price">₱<?= number_format($p['price'], 2) ?></div>
                    </a>
                    <button class="add-btn" onclick="addToCart(<?= $p['id'] ?>, '<?= addslashes($p['name']) ?>', <?= $p['price'] ?>, '<?= $p['image'] ?>')">
                        Add to Cart
                    </button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-results"><p>No pieces found.</p></div>
        <?php endif; ?>
    </div>
</div>

<script>
// Check theme on load
(function() {
    const saved = localStorage.getItem('lunar-theme');
    if (saved === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
    }
})();

function addToCart(id, name, price, image) {
    const formData = new FormData();
    formData.append('product_id', id);
    formData.append('name', name);
    formData.append('price', price);
    formData.append('image', image);

    fetch('add_to_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(count => {
        document.getElementById('cart-count').innerText = count;
        alert(name + " has been added to your vault.");
    })
    .catch(error => console.error('Error:', error));
}
</script>

</body>
</html>