<?php
require_once 'config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: /admin/dashboard.php');
        exit;
    }

    if ($_SESSION['role'] === 'vendor') {
        header('Location: /vendor/dashboard.php');
        exit;
    }

    if ($_SESSION['role'] === 'delivery') {
        header('Location: /delivery/assigned_orders.php');
        exit;
    }
}

include 'includes/header.php';

$cat = intval($_GET['cat'] ?? 0);
$sub = intval($_GET['sub'] ?? 0);
$shop = intval($_GET['shop'] ?? 0);

$where = "WHERE products.status = 'approved' AND shops.status = 'approved'";

if ($cat > 0) {
    $where .= " AND products.category_id = $cat";
}

if ($sub > 0) {
    $where .= " AND products.subcategory_id = $sub";
}

if ($shop > 0) {
    $where .= " AND products.shop_id = $shop";
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
$subcategories = mysqli_query($conn, "SELECT * FROM subcategories ORDER BY name");
$shops = mysqli_query($conn, "SELECT shops.*, categories.name AS category_name
                              FROM shops
                              JOIN categories ON shops.category_id = categories.id
                              WHERE shops.status = 'approved'
                              ORDER BY shops.name");

$products = mysqli_query($conn, "SELECT products.*,
                                shops.name AS shop_name,
                                shops.address AS shop_address,
                                categories.name AS category_name,
                                subcategories.name AS subcategory_name
                                FROM products
                                JOIN shops ON products.shop_id = shops.id
                                JOIN categories ON products.category_id = categories.id
                                JOIN subcategories ON products.subcategory_id = subcategories.id
                                $where
                                ORDER BY products.id DESC");
?>

<div class="container">
    <div class="hero">
        <h1>ZAK Food Menu</h1>
        <p>Choose your restaurant, select your food, add extras, and place your order.</p>
    </div>

    <div class="filter-box">
        <form method="GET" style="max-width:100%; padding:0;">
            <label>Filter Menu</label>

            <select name="cat">
                <option value="0">All Categories</option>
                <?php while ($c = mysqli_fetch_assoc($categories)): ?>
                    <option value="<?php echo $c['id']; ?>" <?php if ($cat == $c['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($c['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <select name="sub">
                <option value="0">All Food Topics</option>
                <?php while ($s = mysqli_fetch_assoc($subcategories)): ?>
                    <option value="<?php echo $s['id']; ?>" <?php if ($sub == $s['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($s['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <select name="shop">
                <option value="0">All Restaurants</option>
                <?php mysqli_data_seek($shops, 0); while ($sh = mysqli_fetch_assoc($shops)): ?>
                    <option value="<?php echo $sh['id']; ?>" <?php if ($shop == $sh['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($sh['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button class="btn" type="submit">Search</button>
        </form>
    </div>

    <h2>Restaurants</h2>
    <br>

    <div class="grid">
        <?php mysqli_data_seek($shops, 0); while ($shop_row = mysqli_fetch_assoc($shops)): ?>
            <div class="card">
                <span class="badge"><?php echo htmlspecialchars($shop_row['category_name']); ?></span>
                <h3><?php echo htmlspecialchars($shop_row['name']); ?></h3>
                <p><?php echo htmlspecialchars($shop_row['description']); ?></p>
                <p class="small"><?php echo htmlspecialchars($shop_row['address']); ?></p>
                <a class="btn btn-dark" href="/index.php?shop=<?php echo $shop_row['id']; ?>">View Menu</a>
            </div>
        <?php endwhile; ?>
    </div>

    <br>
    <h2>Available Food</h2>
    <br>

    <div class="grid">
        <?php while ($product = mysqli_fetch_assoc($products)): ?>
            <div class="card">
                <?php if (!empty($product['image'])): ?>
                    <img class="product-img" src="/<?php echo htmlspecialchars($product['image']); ?>">
                <?php else: ?>
                    <div class="product-img"></div>
                <?php endif; ?>

                <span class="badge">
                    <?php echo htmlspecialchars($product['category_name']); ?> /
                    <?php echo htmlspecialchars($product['subcategory_name']); ?>
                </span>

                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                <p class="price"><?php echo $product['price']; ?> EGP</p>
                <p>Restaurant: <?php echo htmlspecialchars($product['shop_name']); ?></p>

                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a class="btn" href="/login.php">Login To Order</a>
                <?php elseif ($_SESSION['role'] === 'customer'): ?>
                    <a class="btn btn-green" href="/product.php?id=<?php echo $product['id']; ?>">Order Now</a>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
