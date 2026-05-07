<?php
require_once 'includes/auth.php';
include 'includes/header.php';
$id = intval($_GET['id'] ?? 0);
$result = mysqli_query($conn, "SELECT products.*, shops.name AS shop_name, shops.address AS shop_address, categories.name AS category_name, subcategories.name AS subcategory_name FROM products JOIN shops ON products.shop_id = shops.id JOIN categories ON products.category_id = categories.id JOIN subcategories ON products.subcategory_id = subcategories.id WHERE products.id = $id AND products.status = 'approved'");
$product = mysqli_fetch_assoc($result);
$addons = mysqli_query($conn, "SELECT * FROM product_addons WHERE product_id = $id");
?>
<div class="container">
    <?php if ($product): ?>
        <div class="card" style="max-width:700px;margin:0 auto;">
            <?php if (!empty($product['image'])): ?>
                <img class="product-img" src="/<?php echo htmlspecialchars($product['image']); ?>">
            <?php else: ?>
                <div class="product-img" style="display:flex;align-items:center;justify-content:center;font-size:52px;">🍽️</div>
            <?php endif; ?>
            <span class="badge"><?php echo htmlspecialchars($product['category_name']); ?> / <?php echo htmlspecialchars($product['subcategory_name']); ?></span>
            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
            <p><?php echo htmlspecialchars($product['description']); ?></p>
            <p class="price"><?php echo number_format($product['price'], 2); ?> EGP</p>
            <p class="small">🏪 <?php echo htmlspecialchars($product['shop_name']); ?></p>
            <p class="small">📍 <?php echo htmlspecialchars($product['shop_address']); ?></p>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'customer'): ?>
                <form method="POST" action="/cart.php" style="margin-top:20px;">
                    <input type="hidden" name="action_type" value="add_to_cart">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <label>Quantity</label>
                    <input type="number" name="quantity" value="1" min="1" style="max-width:120px;">
                    <?php if (mysqli_num_rows($addons) > 0): ?>
                        <label style="margin-top:8px;">Extras</label>
                        <?php while ($addon = mysqli_fetch_assoc($addons)): ?>
                            <p style="margin-bottom:8px;">
                                <label style="font-weight:400;display:flex;align-items:center;gap:8px;cursor:pointer;">
                                    <input type="checkbox" name="addons[]" value="<?php echo $addon['id']; ?>">
                                    <?php echo htmlspecialchars($addon['addon_name']); ?>
                                    <span class="small">+<?php echo number_format($addon['addon_price'],2); ?> EGP</span>
                                </label>
                            </p>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="small" style="margin-bottom:12px;">No extras for this item.</p>
                    <?php endif; ?>
                    <button class="btn btn-green" type="submit">🛒 Add To Cart</button>
                    <a class="btn btn-dark" href="/index.php">← Back</a>
                </form>
            <?php else: ?>
                <div class="alert" style="margin-top:16px;">Login as a customer to order.</div>
                <a class="btn" href="/login.php">Login</a>
                <a class="btn btn-dark" href="/index.php">← Back</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="empty-state"><span class="icon">❌</span><h3>Product not found</h3><p>Unavailable or not approved.</p><a class="btn" href="/index.php">← Back</a></div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
