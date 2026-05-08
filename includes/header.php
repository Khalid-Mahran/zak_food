<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$home_link = '/index.php';
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') $home_link = '/admin/dashboard.php';
    elseif ($_SESSION['role'] === 'vendor') $home_link = '/vendor/dashboard.php';
    elseif ($_SESSION['role'] === 'delivery') $home_link = '/delivery/assigned_orders.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZAK Food</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="navbar">
    <div class="logo">ZAK Food</div>
    <div class="hamburger" id="hamburger"><span></span><span></span><span></span></div>
    <div class="nav-links">
        <a href="<?php echo $home_link; ?>">Home</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="/admin/dashboard.php">Dashboard</a>
                <a href="/admin/categories.php">Categories</a>
                <a href="/admin/shops.php">Restaurants</a>
                <a href="/admin/product_requests.php">Products</a>
                <a href="/admin/orders.php">Orders</a>
                <a href="/admin/users.php">Users</a>
            <?php elseif ($_SESSION['role'] === 'vendor'): ?>
                <a href="/vendor/dashboard.php">Dashboard</a>
                <a href="/vendor/add_product.php">Add Food</a>
                <a href="/vendor/my_products.php">My Menu</a>
                <a href="/vendor/vendor_orders.php">Orders</a>
                <a href="/vendor/profits.php">Profits</a>
            <?php elseif ($_SESSION['role'] === 'customer'): ?>
                <a href="/index.php">Menu</a>
                <a href="/cart.php">🛒 Cart</a>
                <a href="/customer/my_orders.php">My Orders</a>
                <a href="/addresses.php">Addresses</a>
                <a href="/profile.php">Profile</a>
            <?php elseif ($_SESSION['role'] === 'delivery'): ?>
                <a href="/delivery/assigned_orders.php">Requests</a>
                <a href="/profile.php">Profile</a>
            <?php endif; ?>
            <a href="/logout.php" class="logout-link">Logout</a>
        <?php else: ?>
            <a href="/index.php">Menu</a>
            <a href="/login.php">Login</a>
            <a href="/register.php">Register</a>
        <?php endif; ?>
    </div>
</div>
