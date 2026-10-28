<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "lunar_registration"; 

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. FETCH PRODUCTS FROM DATABASE
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

$products = [
    'rings' => [],
    'necklaces' => [],
    'earrings' => []
];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $cat = $row['category'];
        if (array_key_exists($cat, $products)) {
            $products[$cat][] = $row;
        }
    }
}

// Calculate total cart quantity for the header badge
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
<title>Lunar Moon Jewelry</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Assistant:wght@300;400;600&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

<style>
/* --- ADDED: THEME VARIABLES --- */
:root {
    --bg-color: #fff;
    --text-color: #121212;
    --header-bg: #fff;
    --border-color: #f2f2f2;
}

[data-theme="dark"] {
    --bg-color: #121212;
    --text-color: #fff;
    --header-bg: #1a1a1a;
    --border-color: #333;
}

/* GLOBAL STYLES */
body { margin: 0; font-family: 'Assistant', sans-serif; color: var(--text-color); line-height: 1.5; background-color: var(--bg-color); transition: background 0.3s; }

/* STICKY HEADER */
header { 
    position: sticky; top: 0; display: flex; align-items: center; justify-content: space-between;
    padding: 10px 50px; background: var(--header-bg); border-bottom: 1px solid var(--border-color); z-index: 1001; 
    transition: all 0.4s ease;
}
header.scrolled { padding: 5px 50px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }

.logo-container { flex: 1; text-align: left; }
.site-logo { height: 160px; width: auto; display: block; mix-blend-mode: multiply; transition: height 0.4s ease; }
/* Logo adjustment for dark mode */
[data-theme="dark"] .site-logo { filter: invert(1) hue-rotate(180deg); mix-blend-mode: screen; }

header.scrolled .site-logo { height: 90px; } 

nav { flex: 2; text-align: center; }
nav ul { list-style: none; display: inline-flex; justify-content: center; gap: 40px; padding: 0; margin: 0; }
nav a { text-decoration: none; color: var(--text-color); font-size: 13px; text-transform: uppercase; letter-spacing: 2px; font-weight: 600; cursor: pointer; }

.auth-icons { flex: 1; display: flex; justify-content: flex-end; gap: 22px; align-items: center; }
.auth-icons i { cursor: pointer; font-size: 19px; color: var(--text-color); }

/* --- THEME TOGGLE ICON STYLE --- */
#theme-toggle { font-size: 18px; cursor: pointer; }

/* PROFILE BUTTON STYLES */
.profile-btn-nav { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; cursor: pointer; border: 1px solid var(--border-color); padding: 5px 15px; border-radius: 20px; transition: 0.3s; display: flex; align-items: center; gap: 8px; color: var(--text-color); }
.profile-btn-nav:hover { background: #121212; color: #fff; }

.cart-icon-wrapper { position: relative; cursor: pointer; }
#cart-count {
    position: absolute; top: -10px; right: -10px;
    background: #d4af37; color: white; font-size: 10px;
    padding: 2px 6px; border-radius: 50%; font-weight: bold;
}

/* SEARCH DROPDOWN */
.search-bar-dropdown {
  display: none; position: absolute; top: 100%; left: 0; width: 100%; 
  background: var(--bg-color); padding: 20px 0; box-shadow: 0 10px 30px rgba(0,0,0,0.1);
  z-index: 1000; border-bottom: 1px solid var(--border-color);
}
.search-inner { max-width: 600px; margin: 0 auto; width: 90%; position: relative; }
.search-input-wrapper { display: flex; align-items: center; border: 1px solid var(--border-color); padding: 10px 15px; border-radius: 4px; }
.search-input-wrapper input { width: 100%; border: none; outline: none; font-size: 16px; font-family: inherit; background: transparent; color: var(--text-color); }

#searchResultsList { 
    margin-top: 10px; max-height: 400px; overflow-y: auto; 
    background: var(--bg-color); border-radius: 4px; text-align: left;
}
.search-result-item { 
    display: flex; align-items: center; gap: 15px; padding: 10px; 
    border-bottom: 1px solid var(--border-color); cursor: pointer; transition: background 0.2s;
}
.search-result-item img { width: 50px; height: 50px; object-fit: cover; border-radius: 3px; }

/* NOTIFICATION TOAST */
.toast-note {
    position: fixed; top: 100px; right: 20px; background: #121212; color: white;
    padding: 16px 25px; display: flex; align-items: center; gap: 12px; z-index: 10000;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15); transform: translateX(125%);
    transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    font-size: 13px; letter-spacing: 1px; text-transform: uppercase;
}
.toast-note.show { transform: translateX(0); }

/* MODALS */
.modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px); }
.modal-content { background: var(--bg-color); color: var(--text-color); width: 90%; max-width: 400px; padding: 50px 40px; position: relative; text-align: center; }
.form-group { margin-bottom: 20px; text-align: left; position: relative; }
.form-group label { font-size: 11px; text-transform: uppercase; color: #888; display: block; margin-bottom: 5px; }
.form-group input { width: 100%; padding: 12px 0; border: none; border-bottom: 1px solid var(--border-color); outline: none; font-size: 15px; background: transparent; color: var(--text-color); }
.toggle-password { position: absolute; right: 0; bottom: 12px; cursor: pointer; color: #888; }
.modal-content button { width: 100%; padding: 16px; background: #121212; color: #fff; border: none; cursor: pointer; text-transform: uppercase; letter-spacing: 2px; margin-top: 20px; }
.close-modal { position: absolute; right: 20px; top: 20px; cursor: pointer; font-size: 20px; color: #bbb; }

/* INTRO & HERO */
.intro-text-section { padding: 80px 50px; text-align: center; max-width: 800px; margin: 0 auto; }
.intro-text-section p { font-size: 18px; color: var(--text-color); opacity: 0.8; line-height: 1.8; }
.hero { position: relative; height: 80vh; display: flex; align-items: center; justify-content: center; overflow: hidden; background-color: #000; }
.hero-bg { position: absolute; inset: 0; background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('cornelia-ng-2zHQhfEpisc-unsplash.jpg'); background-size: cover; background-position: center; z-index: 1; }
.hero h1 { position: relative; z-index: 2; color: #fff; font-family: 'Playfair Display', serif; font-size: clamp(40px, 6vw, 70px); font-weight: 400; font-style: italic; }

/* PRODUCTS */
.products-section { padding: 80px 50px; }
.tabs { display: flex; justify-content: center; gap: 30px; margin-bottom: 50px; }
.tabs button { border: none; background: transparent; cursor: pointer; text-transform: uppercase; letter-spacing: 2px; font-family: inherit; font-weight: 600; padding-bottom: 5px; border-bottom: 2px solid transparent; color: var(--text-color); }
.tabs button.active { border-bottom-color: var(--text-color); }
.products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 40px; }
.product-card { text-align: center; }
.img-container { overflow: hidden; aspect-ratio: 1/1; }
.product-card img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease; }
.product-card:hover img { transform: scale(1.15); }

.add-btn {
    margin-top: 15px; background: #121212; color: #fff;
    border: none; padding: 12px 20px; font-size: 11px;
    text-transform: uppercase; letter-spacing: 1px;
    cursor: pointer; width: 100%; transition: 0.3s;
}
.add-btn:hover { background: #d4af37; }

footer { text-align: center; padding: 80px 20px; font-size: 11px; color: #999; letter-spacing: 3px; text-transform: uppercase; }
</style>
</head>

<body>

<div id="note-reg" class="toast-note"><i class="fa-solid fa-crown"></i><span>Welcome! Please log in.</span></div>
<div id="note-login" class="toast-note"><i class="fa-solid fa-moon"></i><span>Welcome back, <?= isset($_SESSION['user_name']) ? explode(' ', $_SESSION['user_name'])[0] : 'User' ?>!</span></div>

<header id="mainHeader">
  <div class="logo-container"><img src="logo2.jpg" alt="Lunar Moon Logo" class="site-logo"></div>
  <nav>
    <ul>
      <li><a onclick="switchCategory('rings')">Rings</a></li>
      <li><a onclick="switchCategory('necklaces')">Necklaces</a></li>
      <li><a onclick="switchCategory('earrings')">Earrings</a></li>
    </ul>
  </nav>
  <div class="auth-icons">
    <i class="fa-solid fa-moon" id="theme-toggle" onclick="toggleTheme()"></i>

    <i class="fa-solid fa-magnifying-glass" onclick="toggleSearch()"></i>
    
    <div class="cart-icon-wrapper" onclick="window.location.href='cart.php'">
        <i class="fa-solid fa-cart-shopping"></i>
        <span id="cart-count"><?= $cart_count ?></span>
    </div>

    <?php if(isset($_SESSION['user_name'])): ?>
        <div class="profile-btn-nav" onclick="window.location.href='profile.php'">
            <i class="fa-regular fa-circle-user"></i> Profile
        </div>
    <?php else: ?>
        <i class="fa-regular fa-user" onclick="openModal('loginModal')"></i>
    <?php endif; ?>
  </div>

  <div class="search-bar-dropdown" id="searchBox">
    <div class="search-inner">
      <form action="search.php" method="GET" id="searchForm">
        <div class="search-input-wrapper">
          <input type="text" name="q" id="searchInput" placeholder="Search products..." onkeyup="filterProducts()">
          <button type="submit" style="background:none; border:none; cursor:pointer;"><i class="fa-solid fa-magnifying-glass"></i></button>
        </div>
      </form>
      <div id="searchResultsList"></div>
      <div style="cursor:pointer; position:absolute; right:-40px; top:12px;" onclick="toggleSearch()">✕</div>
    </div>
  </div>
</header>

<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-content"><h1>Welcome to Lunar Moon Jewelry</h1></div>
</section>

<section class="intro-text-section">
    <p>
        Lunar Moon Jewelry is your premier destination for exquisite and timeless jewelry pieces that are very affordable. 
        Giving a stunning designs that celebrate the beauty and individuality of every person. 
        Founded in 2026, our mission is to provide high-quality, handcrafted jewelry that captivates and inspires.
    </p>
</section>

<section class="products-section" id="products">
  <div class="tabs">
    <button class="tab-btn active" data-target="rings">Rings</button>
    <button class="tab-btn" data-target="necklaces">Necklaces</button>
    <button class="tab-btn" data-target="earrings">Earrings</button>
  </div>

  <?php foreach($products as $cat=>$items): ?>
  <div class="products-grid" id="<?= $cat ?>" style="<?= $cat==='rings'?'':'display:none' ?>">
    <?php foreach($items as $item): ?>
    <div class="product-card">
        <div class="img-container"><img src="<?= htmlspecialchars($item['image']) ?>"></div>
        <h3><?= htmlspecialchars($item['name']) ?></h3>
        <div class="price">₱<?= number_format($item['price'],2) ?></div>
        <button class="add-btn" onclick="addToCart(<?= $item['id'] ?>, '<?= addslashes($item['name']) ?>', <?= $item['price'] ?>, '<?= $item['image'] ?>')">Add to Cart</button>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endforeach; ?>
</section>

<footer>© 2026 LUNAR MOON JEWELRY</footer>

<div class="modal" id="loginModal">
  <div class="modal-content">
    <span class="close-modal" onclick="closeModal('loginModal')">✕</span>
    <h2>Login</h2>
    <form action="login.php" method="POST">
        <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
        <div class="form-group"><label>Password</label><input type="password" name="password" id="lP" required><i class="fa-regular fa-eye toggle-password" onclick="togglePassVisibility('lP', this)"></i></div>
        <button type="submit">Sign In</button>
    </form>
    <div style="margin-top:20px; font-size:13px;">New? <span style="cursor:pointer; text-decoration:underline;" onclick="closeModal('loginModal'); openModal('registerModal')">Register</span></div>
  </div>
</div>

<div class="modal" id="registerModal">
  <div class="modal-content">
    <span class="close-modal" onclick="closeModal('registerModal')">✕</span>
    <h2>Register</h2>
    <form action="registration.php" method="POST" onsubmit="return validatePasswords()">
        <div class="form-group"><label>Full Name</label><input type="text" name="fullname" required></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
        <div class="form-group"><label>Password</label><input type="password" name="password" id="rP" required><i class="fa-regular fa-eye toggle-password" onclick="togglePassVisibility('rP', this)"></i></div>
        <div class="form-group"><label>Confirm</label><input type="password" id="cP" required><i class="fa-regular fa-eye toggle-password" onclick="togglePassVisibility('cP', this)"></i></div>
        <button type="submit">Create Account</button>
    </form>
  </div>
</div>

<script>
// --- ADDED: THEME TOGGLE SCRIPT ---
function toggleTheme() {
    const html = document.documentElement;
    const icon = document.getElementById('theme-toggle');
    const isDark = html.getAttribute('data-theme') === 'dark';
    const newTheme = isDark ? 'light' : 'dark';
    
    html.setAttribute('data-theme', newTheme);
    localStorage.setItem('lunar-theme', newTheme);
    icon.className = isDark ? 'fa-solid fa-moon' : 'fa-solid fa-sun';
}

// Initial check for saved theme
(function() {
    const saved = localStorage.getItem('lunar-theme');
    if (saved === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('theme-toggle').className = 'fa-solid fa-sun';
        });
    }
})();


const allProducts = <?php 
    $flat = [];
    foreach($products as $cat=>$items) {
        foreach($items as $i) { $i['category'] = $cat; $flat[] = $i; }
    }
    echo json_encode($flat); 
?>;

function addToCart(id, name, price, image) {
    const formData = new FormData();
    formData.append('product_id', id);
    formData.append('name', name);
    formData.append('price', price);
    formData.append('image', image);

    fetch('add_to_cart.php', { method: 'POST', body: formData })
    .then(response => response.text())
    .then(count => {
        document.getElementById('cart-count').innerText = count;
        alert(name + " added to cart!");
    });
}

function filterProducts(){
    const query = document.getElementById('searchInput').value.toLowerCase();
    const list = document.getElementById('searchResultsList');
    list.innerHTML = "";
    if(query.length < 1) return;
    const filtered = allProducts.filter(p => p.name.toLowerCase().includes(query));
    filtered.forEach(p => {
        const item = document.createElement('div');
        item.className = 'search-result-item';
        item.onclick = () => { window.location.href = 'search.php?q=' + encodeURIComponent(p.name); };
        item.innerHTML = `<img src="${p.image}"><div><span>${p.name}</span><br><small>₱${p.price.toLocaleString()}</small></div>`;
        list.appendChild(item);
    });
}

window.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    if (params.has('registered')) showNote('note-reg');
    if (params.has('login')) showNote('note-login');

    if (params.get('error') === 'emailexists') {
        alert("This email is already registered! Please try logging in instead.");
        openModal('loginModal');
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});

function showNote(id) {
    const note = document.getElementById(id);
    if(note){
        setTimeout(() => note.classList.add('show'), 500);
        setTimeout(() => note.classList.remove('show'), 4500);
    }
}

function toggleSearch(){
  let box = document.getElementById('searchBox');
  box.style.display = (box.style.display === 'block') ? 'none' : 'block';
}

window.onscroll = () => {
    let header = document.getElementById("mainHeader");
    if (window.scrollY > 20) header.classList.add("scrolled");
    else header.classList.remove("scrolled");
};

const tabs=document.querySelectorAll('.tab-btn');
const grids=document.querySelectorAll('.products-grid');
tabs.forEach(btn=>{
  btn.onclick=()=>{
    tabs.forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    grids.forEach(g=>g.style.display=g.id===btn.dataset.target?'grid':'none');
  }
});

function switchCategory(cat) {
    const btn = document.querySelector(`[data-target="${cat}"]`);
    if(btn) btn.click();
}

function openModal(id){ document.getElementById(id).style.display='flex'; }
function closeModal(id){ document.getElementById(id).style.display='none'; }

function togglePassVisibility(id, icon) {
  let input = document.getElementById(id);
  input.type = (input.type === "password") ? "text" : "password";
  icon.classList.toggle('fa-eye'); icon.classList.toggle('fa-eye-slash');
}

function validatePasswords() {
  if (document.getElementById('rP').value !== document.getElementById('cP').value) {
    alert("Passwords do not match!"); return false;
  } 
  return true;
}

window.onclick = (e) => { if (e.target.className === 'modal') closeModal(e.target.id); }
</script>

</body>
</html>