<?php
require_once '../includes/auth.php';
requireRole('customer');

$customer_id = $_SESSION['user_id'];

$orders = mysqli_query($conn, "SELECT * FROM orders WHERE customer_id = $customer_id ORDER BY id DESC");

include '../includes/header.php';
?>

<div class="container">
    <h2>My Orders</h2>
    <br>

    <div class="grid">
        <?php while ($order = mysqli_fetch_assoc($orders)): ?>
            <div class="card request-card">
                <h3>Order #<?php echo $order['id']; ?></h3>
                <p><b>Status:</b> <?php echo $order['status']; ?></p>
                <p><b>Pickup:</b> <?php echo htmlspecialchars($order['pickup_location']); ?></p>
                <p><b>Dropoff:</b> <?php echo htmlspecialchars($order['dropoff_address']); ?></p>
                <p><b>Items Total:</b> <?php echo $order['total_price']; ?> EGP</p>
                <p><b>Delivery Fee:</b> <?php echo $order['delivery_fee']; ?> EGP</p>
                <p><b>Total Cash:</b> <?php echo $order['cash_to_collect']; ?> EGP</p>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
