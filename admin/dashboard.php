<?php
require_once '../includes/auth.php';
requireRole('admin');

$users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"))['total'];
$pending_vendors = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'vendor' AND is_approved = 0"))['total'];
$pending_shops = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM shops WHERE status = 'pending'"))['total'];
$pending_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products WHERE status = 'pending'"))['total'];
$orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders"))['total'];

include '../includes/header.php';
?>

<div class="container">
    <h2>Admin Dashboard</h2>
    <br>

    <div class="grid">
        <div class="card stat"><span class="stat-icon">👥</span><h3>Total Users</h3><p><?php echo $users; ?></p></div>
        <div class="card stat"><span class="stat-icon">🏪</span><h3>Pending Restaurants</h3><p><?php echo $pending_vendors + $pending_shops; ?></p></div>
        <div class="card stat"><span class="stat-icon">🍔</span><h3>Pending Products</h3><p><?php echo $pending_products; ?></p></div>
        <div class="card stat"><span class="stat-icon">📦</span><h3>Total Orders</h3><p><?php echo $orders; ?></p></div>
    </div>

    <br>
    <a class="btn" href="/admin/users.php">Users</a>
    <a class="btn" href="/admin/categories.php">Categories</a>
    <a class="btn" href="/admin/shops.php">Restaurant Requests</a>
    <a class="btn" href="/admin/product_requests.php">Product Requests</a>
    <a class="btn" href="/admin/orders.php">Orders</a>
</div>

<?php include '../includes/footer.php'; ?>
