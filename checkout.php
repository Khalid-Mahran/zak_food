<?php
require_once 'includes/auth.php';
requireRole('customer');

$error = '';
$success = '';
$user_id = $_SESSION['user_id'];

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$addresses = mysqli_query($conn, "SELECT * FROM user_addresses WHERE user_id = $user_id ORDER BY id DESC");
$user_result = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($user_result);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['cart'])) {
    $address_id = intval($_POST['address_id']);

    $address_result = mysqli_query($conn, "SELECT * FROM user_addresses WHERE id = $address_id AND user_id = $user_id");
    $address = mysqli_fetch_assoc($address_result);

    if (!$address) {
        $error = 'Please choose a valid address';
    } else {
        $total = 0;
        $pickup_locations = [];

        foreach ($_SESSION['cart'] as $item) {
            $product_id = intval($item['product_id']);
            $qty = intval($item['quantity']);

            $result = mysqli_query($conn, "SELECT products.*, shops.name AS shop_name, shops.address AS shop_address
                                           FROM products
                                           JOIN shops ON products.shop_id = shops.id
                                           WHERE products.id = $product_id");
            $product = mysqli_fetch_assoc($result);

            if ($product) {
                $addons_total = 0;

                foreach ($item['addons'] as $addon_id) {
                    $addon_id = intval($addon_id);
                    $addon_result = mysqli_query($conn, "SELECT * FROM product_addons WHERE id = $addon_id AND product_id = $product_id");
                    $addon = mysqli_fetch_assoc($addon_result);

                    if ($addon) {
                        $addons_total += $addon['addon_price'];
                    }
                }

                $total += ($product['price'] + $addons_total) * $qty;
                $pickup_locations[] = $product['shop_name'] . ' - ' . $product['shop_address'];
            }
        }

        $delivery_fee = 40;
        $cash_to_collect = $total + $delivery_fee;

        $pickup_location = mysqli_real_escape_string($conn, implode(' | ', array_unique($pickup_locations)));
        $dropoff_address = mysqli_real_escape_string($conn, $address['label'] . ' - ' . $address['full_address'] . ' - ' . $address['area'] . ' - ' . $address['city']);
        $customer_phone = mysqli_real_escape_string($conn, $user['phone'] ?? '');

        $order_query = "INSERT INTO orders
                        (customer_id, address_id, total_price, delivery_fee, cash_to_collect, pickup_location, dropoff_address, customer_phone, status)
                        VALUES
                        ($user_id, $address_id, $total, $delivery_fee, $cash_to_collect, '$pickup_location', '$dropoff_address', '$customer_phone', 'pending')";

        if (mysqli_query($conn, $order_query)) {
            $order_id = mysqli_insert_id($conn);

            foreach ($_SESSION['cart'] as $item) {
                $product_id = intval($item['product_id']);
                $qty = intval($item['quantity']);

                $result = mysqli_query($conn, "SELECT * FROM products WHERE id = $product_id");
                $product = mysqli_fetch_assoc($result);

                if ($product) {
                    $price = $product['price'];

                    mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, price)
                                         VALUES ($order_id, $product_id, $qty, $price)");

                    $order_item_id = mysqli_insert_id($conn);

                    foreach ($item['addons'] as $addon_id) {
                        $addon_id = intval($addon_id);
                        $addon_result = mysqli_query($conn, "SELECT * FROM product_addons WHERE id = $addon_id AND product_id = $product_id");
                        $addon = mysqli_fetch_assoc($addon_result);

                        if ($addon) {
                            $addon_name = mysqli_real_escape_string($conn, $addon['addon_name']);
                            $addon_price = floatval($addon['addon_price']);

                            mysqli_query($conn, "INSERT INTO order_item_addons (order_item_id, addon_name, addon_price)
                                                 VALUES ($order_item_id, '$addon_name', $addon_price)");
                        }
                    }

                    mysqli_query($conn, "UPDATE products SET quantity = quantity - $qty WHERE id = $product_id");
                }
            }

            $_SESSION['cart'] = [];
            $success = 'Order sent to restaurant. Waiting for preparation.';
        } else {
            $error = 'Checkout failed';
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <h2>Checkout</h2>
    <br>

    <?php if ($error): ?><div class="alert"><?php echo $error; ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert success"><?php echo $success; ?></div><?php endif; ?>

    <?php if (empty($_SESSION['cart']) && !$success): ?>
        <div class="alert">Your cart is empty</div>
    <?php elseif (mysqli_num_rows($addresses) === 0): ?>
        <div class="alert">You need to add an address first.</div>
        <a class="btn" href="/addresses.php">Add Address</a>
    <?php else: ?>
        <form method="POST">
            <label>Choose Delivery Address</label>
            <select name="address_id" required>
                <?php mysqli_data_seek($addresses, 0); while ($address = mysqli_fetch_assoc($addresses)): ?>
                    <option value="<?php echo $address['id']; ?>">
                        <?php echo htmlspecialchars($address['label'] . ' - ' . $address['full_address']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <p class="small">Delivery fee: 40 EGP</p>
            <button class="btn btn-green" type="submit">Place Order</button>
        </form>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
