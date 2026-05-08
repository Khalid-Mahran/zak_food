<?php
require_once '../includes/auth.php';
requireRole('customer');

include '../includes/header.php';
?>

<div class="container">
    <h2>Customer Dashboard</h2>
    <br>

    <div class="grid">
        <div class="card">
            <h3>Browse ZAK Food</h3>
            <p>Order food, grocery, and products from restaurants and shops.</p>
            <a class="btn" href="/index.php">Browse</a>
        </div>

        <div class="card">
            <h3>My Addresses</h3>
            <p>Add Home, Work, or any delivery address.</p>
            <a class="btn" href="/addresses.php">Manage Addresses</a>
        </div>

        <div class="card">
            <h3>My Orders</h3>
            <p>Track your placed orders.</p>
            <a class="btn" href="/customer/my_orders.php">View Orders</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
