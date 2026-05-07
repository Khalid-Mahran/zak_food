<?php
require_once '../includes/auth.php';
requireRole('vendor');

$vendor_id = $_SESSION['user_id'];

$items_sales = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(order_items.price * order_items.quantity), 0) AS total
                                                       FROM order_items
                                                       JOIN products ON order_items.product_id = products.id
                                                       JOIN orders ON order_items.order_id = orders.id
                                                       WHERE products.vendor_id = $vendor_id AND orders.status = 'delivered'"))['total'];

$addons_sales = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(order_item_addons.addon_price * order_items.quantity), 0) AS total
                                                        FROM order_item_addons
                                                        JOIN order_items ON order_item_addons.order_item_id = order_items.id
                                                        JOIN products ON order_items.product_id = products.id
                                                        JOIN orders ON order_items.order_id = orders.id
                                                        WHERE products.vendor_id = $vendor_id AND orders.status = 'delivered'"))['total'];

$pending_sales = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(order_items.price * order_items.quantity), 0) AS total
                                                         FROM order_items
                                                         JOIN products ON order_items.product_id = products.id
                                                         JOIN orders ON order_items.order_id = orders.id
                                                         WHERE products.vendor_id = $vendor_id AND orders.status != 'delivered'"))['total'];

$total_profit = $items_sales + $addons_sales;

include '../includes/header.php';
?>

<div class="container">
    <h2>Restaurant Profits</h2>
    <br>

    <div class="grid">
        <div class="card">
            <h3>Delivered Items Sales</h3>
            <p class="price"><?php echo $items_sales; ?> EGP</p>
        </div>

        <div class="card">
            <h3>Delivered Additions Sales</h3>
            <p class="price"><?php echo $addons_sales; ?> EGP</p>
        </div>

        <div class="card">
            <h3>Total Profit</h3>
            <p class="price"><?php echo $total_profit; ?> EGP</p>
        </div>

        <div class="card">
            <h3>Pending Revenue</h3>
            <p class="price"><?php echo $pending_sales; ?> EGP</p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
