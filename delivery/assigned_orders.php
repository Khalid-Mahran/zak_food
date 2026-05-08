<?php
require_once '../includes/auth.php';
requireRole('delivery');

$delivery_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id']);
    $action = $_POST['action_type'] ?? '';

    if ($action === 'accept') {
        mysqli_query($conn, "UPDATE orders SET delivery_id = $delivery_id, status = 'accepted_by_delivery'
                             WHERE id = $order_id AND delivery_id IS NULL AND status = 'ready_for_pickup'");
    }

    if ($action === 'picked_up') {
        mysqli_query($conn, "UPDATE orders SET status = 'picked_up'
                             WHERE id = $order_id AND delivery_id = $delivery_id");
    }

    if ($action === 'delivered') {
        mysqli_query($conn, "UPDATE orders SET status = 'delivered'
                             WHERE id = $order_id AND delivery_id = $delivery_id");
    }
}

$orders = mysqli_query($conn, "SELECT orders.*, users.name AS customer_name
                              FROM orders
                              JOIN users ON orders.customer_id = users.id
                              WHERE (orders.status = 'ready_for_pickup' AND orders.delivery_id IS NULL)
                                 OR orders.delivery_id = $delivery_id
                              ORDER BY orders.id DESC");

include '../includes/header.php';
?>

<div class="container">
    <h2>Delivery Requests</h2>
    <p class="small">Delivery sees only ready orders after the restaurant marks them as ready.</p>
    <br>

    <div class="grid">
        <?php while ($order = mysqli_fetch_assoc($orders)): ?>
            <div class="card request-card">
                <span class="badge"><?php echo htmlspecialchars($order['status']); ?></span>
                <h3>Request #<?php echo $order['id']; ?></h3>

                <p><b>Customer:</b> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                <p><b>Customer Phone:</b> <?php echo htmlspecialchars($order['customer_phone']); ?></p>

                <br>
                <p><b>Pickup From:</b></p>
                <p><?php echo htmlspecialchars($order['pickup_location']); ?></p>

                <br>
                <p><b>Dropoff To:</b></p>
                <p><?php echo htmlspecialchars($order['dropoff_address']); ?></p>

                <br>
                <p><b>Delivery Fee:</b> <?php echo $order['delivery_fee']; ?> EGP</p>
                <p><b>Cash To Collect:</b> <?php echo $order['cash_to_collect']; ?> EGP</p>

                <form method="POST" style="padding:0;background:none;">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">

                    <?php if ($order['status'] === 'ready_for_pickup' && $order['delivery_id'] == null): ?>
                        <input type="hidden" name="action_type" value="accept">
                        <button class="btn btn-green" type="submit">Accept Request</button>
                    <?php elseif ($order['status'] === 'accepted_by_delivery'): ?>
                        <input type="hidden" name="action_type" value="picked_up">
                        <button class="btn" type="submit">Mark Picked Up</button>
                    <?php elseif ($order['status'] === 'picked_up'): ?>
                        <input type="hidden" name="action_type" value="delivered">
                        <button class="btn btn-green" type="submit">Mark Delivered</button>
                    <?php else: ?>
                        <p class="small">No action now.</p>
                    <?php endif; ?>
                </form>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
