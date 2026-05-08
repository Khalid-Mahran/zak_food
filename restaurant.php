<?php
require_once 'includes/auth.php';

$shop_id = intval($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && $_SESSION['role'] === 'customer') {
    $rating = max(1, min(5, intval($_POST['rating'] ?? 5)));
    $comment = mysqli_real_escape_string($conn, trim($_POST['comment']));
    $user_id = intval($_SESSION['user_id']);

    $exists = mysqli_query($conn, "SELECT id FROM shop_reviews WHERE shop_id = $shop_id AND user_id = $user_id");

    if (mysqli_num_rows($exists) == 0) {
        mysqli_query($conn, "INSERT INTO shop_reviews (shop_id, user_id, rating, comment)
                             VALUES ($shop_id, $user_id, $rating, '$comment')");
    }

    header("Location: /restaurant.php?id=$shop_id");
    exit;
}

$shop_result = mysqli_query($conn, "SELECT shops.*, categories.name AS category_name
                                    FROM shops
                                    JOIN categories ON shops.category_id = categories.id
                                    WHERE shops.id = $shop_id AND shops.status = 'approved'");

$shop = mysqli_fetch_assoc($shop_result);

if (!$shop) {
    include 'includes/header.php';
    echo '<div class="container"><div class="alert">Restaurant not found.</div><a class="btn" href="/index.php">Back</a></div>';
    include 'includes/footer.php';
    exit;
}

$products = mysqli_query($conn, "SELECT products.*, subcategories.name AS subcategory_name
                                FROM products
                                JOIN subcategories ON products.subcategory_id = subcategories.id
                                WHERE products.shop_id = $shop_id AND products.status = 'approved'
                                ORDER BY products.id DESC");

$reviews = mysqli_query($conn, "SELECT shop_reviews.*, users.name AS user_name
                               FROM shop_reviews
                               JOIN users ON shop_reviews.user_id = users.id
                               WHERE shop_reviews.shop_id = $shop_id
                               ORDER BY shop_reviews.id DESC");

$avg_result = mysqli_query($conn, "SELECT ROUND(AVG(rating), 1) AS avg_rating, COUNT(*) AS total_reviews
                                  FROM shop_reviews
                                  WHERE shop_id = $shop_id");

$avg_data = mysqli_fetch_assoc($avg_result);
$avg_rating = $avg_data['avg_rating'] ?? 0;
$total_reviews = $avg_data['total_reviews'] ?? 0;

include 'includes/header.php';
?>

<div class="container">
    <div class="hero">
        <h1><?php echo htmlspecialchars($shop['name']); ?></h1>
        <p><?php echo htmlspecialchars($shop['description']); ?></p>
        <br>
        <p>Category: <?php echo htmlspecialchars($shop['category_name']); ?></p>
        <p>Address: <?php echo htmlspecialchars($shop['address']); ?></p>
        <p>Phone: <?php echo htmlspecialchars($shop['phone']); ?></p>
        <p>Rating: ⭐ <?php echo $avg_rating ?: 'No rating yet'; ?> / 5 — <?php echo $total_reviews; ?> reviews</p>
    </div>

    <h2>Restaurant Menu</h2>
    <br>

    <div class="grid">
        <?php while ($product = mysqli_fetch_assoc($products)): ?>
            <div class="card">
                <?php if (!empty($product['image'])): ?>
                    <img class="product-img" src="/<?php echo htmlspecialchars($product['image']); ?>">
                <?php else: ?>
                    <div class="product-img" style="display:flex;align-items:center;justify-content:center;font-size:50px;">🍽️</div>
                <?php endif; ?>

                <span class="badge"><?php echo htmlspecialchars($product['subcategory_name']); ?></span>
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                <p class="price"><?php echo number_format($product['price'], 2); ?> EGP</p>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'customer'): ?>
                    <a class="btn btn-green" href="/product.php?id=<?php echo $product['id']; ?>">Add To Cart</a>
                <?php else: ?>
                    <a class="btn btn-dark" href="/login.php">Login To Order</a>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>

    <br>
    <h2>Reviews</h2>
    <br>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'customer'): ?>
        <form method="POST">
            <label>Rating</label>
            <select name="rating" required>
                <option value="5">5 Stars</option>
                <option value="4">4 Stars</option>
                <option value="3">3 Stars</option>
                <option value="2">2 Stars</option>
                <option value="1">1 Star</option>
            </select>

            <label>Comment</label>
            <textarea name="comment" required></textarea>

            <button class="btn" type="submit">Add Review</button>
        </form>
    <?php endif; ?>

    <div class="grid">
        <?php while ($review = mysqli_fetch_assoc($reviews)): ?>
            <div class="card">
                <h3><?php echo htmlspecialchars($review['user_name']); ?></h3>
                <p>⭐ <?php echo $review['rating']; ?> / 5</p>
                <p><?php echo htmlspecialchars($review['comment']); ?></p>
                <p class="small"><?php echo $review['created_at']; ?></p>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
