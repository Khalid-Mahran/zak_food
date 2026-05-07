<?php
require_once '../includes/auth.php';
requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    mysqli_query($conn, "UPDATE orders SET status = '$status' WHERE id = $order_id");
}

$orders = mysqli_query($conn, "SELECT orders.*, users.name AS customer_name
                              FROM orders
                              JOIN users ON orders.customer_id = users.id
                              ORDER BY orders.id DESC");

include '../includes/header.php';
?>

<div class="container">
    <h2>All Orders</h2>
    <br>

    <table>
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Pickup</th>
            <th>Dropoff</th>
            <th>Cash</th>
            <th>Status</th>
            <th>Change Status</th>
        </tr>

        <?php while ($order = mysqli_fetch_assoc($orders)): ?>
            <tr>
                <td>#<?php echo $order['id']; ?></td>
                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($order['pickup_location']); ?></td>
                <td><?php echo htmlspecialchars($order['dropoff_address']); ?></td>
                <td><?php echo $order['cash_to_collect']; ?> EGP</td>
                <td><?php echo $order['status']; ?></td>
                <td>
                    <form method="POST" style="padding:0;background:none;">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <select name="status">
                            <option value="pending">pending</option>
                            <option value="preparing">preparing</option>
                            <option value="ready_for_pickup">ready_for_pickup</option>
                            <option value="accepted_by_delivery">accepted_by_delivery</option>
                            <option value="picked_up">picked_up</option>
                            <option value="delivered">delivered</option>
                            <option value="cancelled">cancelled</option>
                        </select>
                        <button class="btn" type="submit">Save</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
