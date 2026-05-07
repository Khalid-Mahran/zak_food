<?php
require_once 'config/db.php';
include 'includes/header.php';

$id = intval($_GET['id'] ?? 0);

$result = mysqli_query($conn, "SELECT products.*,
                              shops.name AS shop_name,
                              shops.address AS shop_address,
                              categories.name AS category_name,
                              subcategories.name AS subcategory_name
                              FROM products
                              JOIN shops ON products.shop_id = shops.id
                              JOIN categories ON products.category_id = categories.id
                              JOIN subcategories ON products.subcategory_id = subcategories.id
                              WHERE products.id = $id AND products.status = 'approved'");

$product = mysqli_fetch_assoc($result);
$addons = mysqli_query($conn, "SELECT * FROM product_addons WHERE product_id = $id");
?>

<div class="container">
    <?php if ($product): ?>
        <div class="card">
            <?php if (!empty($product['image'])): ?>
                <img class="product-img" src="/<?php echo htmlspecialchars($product['image']); ?>">
            <?php else: ?>
                <div class="product-img"></div>
            <?php endif; ?>

            <span class="badge"><?php echo htmlspecialchars($product['category_name']); ?> / <?php echo htmlspecialchars($product['subcategory_name']); ?></span>
            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
            <p><?php echo htmlspecialchars($product['description']); ?></p>
            <p class="price"><?php echo $product['price']; ?> EGP</p>
            <p>Restaurant: <?php echo htmlspecialchars($product['shop_name']); ?></p>
            <p class="small">Pickup: <?php echo htmlspecialchars($product['shop_address']); ?></p>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'customer'): ?>
                <form method="POST" action="/cart.php">
                    <input type="hidden" name="action_type" value="add_to_cart">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                    <label>Quantity</label>
                    <input type="number" name="quantity" value="1" min="1">

                    <label>Additions</label>
                    <?php if (mysqli_num_rows($addons) === 0): ?>
                        <p class="small">No additions available.</p>
                    <?php else: ?>
                        <?php while ($addon = mysqli_fetch_assoc($addons)): ?>
                            <p>
                                <label>
                                    <input type="checkbox" name="addons[]" value="<?php echo $addon['id']; ?>">
                                    <?php echo htmlspecialchars($addon['addon_name']); ?>
                                    + <?php echo $addon['addon_price']; ?> EGP
                                </label>
                            </p>
                        <?php endwhile; ?>
                    <?php endif; ?>

                    <button class="btn btn-green" type="submit">Add To Cart</button>
                </form>
            <?php else: ?>
                <div class="alert">Login as customer to order.</div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="alert">Product not found or not approved yet.</div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
