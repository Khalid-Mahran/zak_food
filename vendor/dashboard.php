<?php
require_once '../includes/auth.php';
requireRole('vendor');

$vendor_id = $_SESSION['user_id'];

$shops = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM shops WHERE vendor_id = $vendor_id"))['total'];
$products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products WHERE vendor_id = $vendor_id"))['total'];
$pending_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products WHERE vendor_id = $vendor_id AND status = 'pending'"))['total'];

include '../includes/header.php';
?>

<div class="container">
    <h2>Restaurant Dashboard</h2>
    <br>

    <div class="grid">
        <div class="card"><h3>My Restaurants</h3><p><?php echo $shops; ?></p></div>
        <div class="card"><h3>My Products</h3><p><?php echo $products; ?></p></div>
        <div class="card"><h3>Waiting Admin Approval</h3><p><?php echo $pending_products; ?></p></div>
    </div>

    <br>
    <a class="btn" href="/vendor/create_shop.php">Create Restaurant</a>
    <a class="btn" href="/vendor/add_product.php">Add Food Item</a>
    <a class="btn" href="/vendor/my_products.php">My Menu</a>
    <a class="btn" href="/vendor/vendor_orders.php">Orders</a>
    <a class="btn" href="/vendor/profits.php">Profits</a>
</div>

<?php include '../includes/footer.php'; ?>
