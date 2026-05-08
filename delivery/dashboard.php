<?php
require_once '../includes/auth.php';
requireRole('delivery');

include '../includes/header.php';
?>

<div class="container">
    <h2>Delivery Dashboard</h2>
    <br>

    <div class="card">
        <h3>Delivery Requests</h3>
        <p>View pickup and dropoff requests assigned to you.</p>
        <a class="btn" href="/delivery/assigned_orders.php">Open Requests</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
