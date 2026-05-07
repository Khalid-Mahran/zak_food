<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$home_link = '/index.php';

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        $home_link = '/admin/dashboard.php';
    } elseif ($_SESSION['role'] === 'vendor') {
        $home_link = '/vendor/dashboard.php';
    } elseif ($_SESSION['role'] === 'delivery') {
        $home_link = '/delivery/assigned_orders.php';
    } elseif ($_SESSION['role'] === 'customer') {
        $home_link = '/index.php';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>ZAK Food</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<div class="navbar">
    <div class="logo">ZAK Food</div>

    <div class="nav-links">
        <a href="<?php echo $home_link; ?>">Home</a>

        <?php if (isset($_SESSION['user_id'])): ?>

            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="/admin/dashboard.php">Dashboard</a>
                <a href="/admin/categories.php">Categories</a>
                <a href="/admin/shops.php">Restaurant Requests</a>
                <a href="/admin/product_requests.php">Food Requests</a>
                <a href="/admin/orders.php">Orders</a>

            <?php elseif ($_SESSION['role'] === 'vendor'): ?>
                <a href="/vendor/dashboard.php">Restaurant Panel</a>
                <a href="/vendor/add_product.php">Add Food</a>
                <a href="/vendor/vendor_orders.php">Orders</a>
                <a href="/vendor/profits.php">Profits</a>

            <?php elseif ($_SESSION['role'] === 'customer'): ?>
                <a href="/index.php">Menu</a>
                <a href="/profile.php">Profile</a>
                <a href="/addresses.php">Addresses</a>
                <a href="/cart.php">Cart</a>
                <a href="/customer/my_orders.php">My Orders</a>

            <?php elseif ($_SESSION['role'] === 'delivery'): ?>
                <a href="/profile.php">Profile</a>
                <a href="/delivery/assigned_orders.php">Delivery Requests</a>
            <?php endif; ?>

            <a href="/logout.php">Logout</a>
        <?php else: ?>
            <a href="/index.php">Menu</a>
            <a href="/login.php">Login</a>
            <a href="/register.php">Register</a>
        <?php endif; ?>
    </div>
</div>
