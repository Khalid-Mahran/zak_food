<?php
require_once '../includes/auth.php';
requireRole('vendor');

$vendor_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_item_id = intval($_POST['order_item_id']);
    $status = mysqli_real_escape_string($conn, $_POST['item_status']);

    $check = mysqli_query($conn, "SELECT order_items.order_id
                                  FROM order_items
                                  JOIN products ON order_items.product_id = products.id
                                  WHERE order_items.id = $order_item_id AND products.vendor_id = $vendor_id");

    if ($row = mysqli_fetch_assoc($check)) {
        $order_id = intval($row['order_id']);

        mysqli_query($conn, "UPDATE order_items SET item_status = '$status' WHERE id = $order_item_id");

        if ($status === 'preparing') {
            mysqli_query($conn, "UPDATE orders SET status = 'preparing' WHERE id = $order_id AND status = 'pending'");
        }

        $not_ready = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM order_items WHERE order_id = $order_id AND item_status != 'ready'"))['total'];

        if ($not_ready == 0) {
            mysqli_query($conn, "UPDATE orders SET status = 'ready_for_pickup' WHERE id = $order_id");
        }
    }
}

$items = mysqli_query($conn, "SELECT order_items.id AS order_item_id,
                              order_items.quantity,
                              order_items.price,
                              order_items.item_status,
                              orders.id AS order_id,
                              orders.status AS order_status,
                              orders.dropoff_address,
                              orders.cash_to_collect,
                              users.name AS customer_name,
                              products.name AS product_name,
                              shops.name AS shop_name
                              FROM order_items
                              JOIN orders ON order_items.order_id = orders.id
                              JOIN users ON orders.customer_id = users.id
                              JOIN products ON order_items.product_id = products.id
                              JOIN shops ON products.shop_id = shops.id
                              WHERE products.vendor_id = $vendor_id
                              ORDER BY orders.id DESC");

include '../includes/header.php';
?>

<div class="container">
    <h2>Restaurant Orders</h2>
    <br>

    <div class="table-wrap"><table>
        <tr>
            <th>Order</th>
            <th>Customer</th>
            <th>Restaurant</th>
            <th>Item</th>
            <th>Additions</th>
            <th>Qty</th>
            <th>Order Status</th>
            <th>Item Status</th>
            <th>Update</th>
        </tr>

        <?php while ($item = mysqli_fetch_assoc($items)): ?>
            <?php
            $addon_result = mysqli_query($conn, "SELECT * FROM order_item_addons WHERE order_item_id = " . intval($item['order_item_id']));
            $addons = [];
            while ($addon = mysqli_fetch_assoc($addon_result)) {
                $addons[] = $addon['addon_name'] . ' +' . $addon['addon_price'] . ' EGP';
            }
            ?>
            <tr>
                <td>#<?php echo $item['order_id']; ?></td>
                <td><?php echo htmlspecialchars($item['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($item['shop_name']); ?></td>
                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                <td><?php echo empty($addons) ? 'No additions' : htmlspecialchars(implode(', ', $addons)); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td><?php echo $item['order_status']; ?></td>
                <td><?php echo $item['item_status']; ?></td>
                <td>
                    <form method="POST" style="padding:0;background:none;">
                        <input type="hidden" name="order_item_id" value="<?php echo $item['order_item_id']; ?>">
                        <select name="item_status">
                            <option value="pending" <?php if ($item["item_status"]==="pending") echo "selected"; ?>>Pending</option>
                            <option value="preparing" <?php if ($item["item_status"]==="preparing") echo "selected"; ?>>Preparing</option>
                            <option value="ready" <?php if ($item["item_status"]==="ready") echo "selected"; ?>>Ready</option>
                        </select>
                        <button class="btn" type="submit">Save</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table></div>
</div>

<?php include '../includes/footer.php'; ?>
