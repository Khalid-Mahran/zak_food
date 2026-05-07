<?php
require_once 'includes/auth.php';
requireRole('customer');

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action_type'] ?? '') === 'add_to_cart') {
    $product_id = intval($_POST['product_id']);
    $quantity = max(1, intval($_POST['quantity'] ?? 1));
    $addons = $_POST['addons'] ?? [];

    $clean_addons = [];

    foreach ($addons as $addon_id) {
        $clean_addons[] = intval($addon_id);
    }

    $_SESSION['cart'][] = [
        'product_id' => $product_id,
        'quantity' => $quantity,
        'addons' => $clean_addons
    ];

    header('Location: /cart.php');
    exit;
}

if (isset($_GET['remove'])) {
    $index = intval($_GET['remove']);

    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }

    header('Location: /cart.php');
    exit;
}

if (isset($_GET['clear'])) {
    $_SESSION['cart'] = [];
    header('Location: /cart.php');
    exit;
}

include 'includes/header.php';

$total = 0;
?>

<div class="container">
    <h2>My Cart</h2>
    <br>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="alert">Cart is empty</div>
        <a class="btn" href="/index.php">Back To Menu</a>
    <?php else: ?>
        <div class="table-wrap"><table>
            <tr>
                <th>Item</th>
                <th>Restaurant</th>
                <th>Additions</th>
                <th>Qty</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>

            <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                <?php
                if (!is_array($item)) {
                    continue;
                }

                $product_id = intval($item['product_id'] ?? 0);
                $qty = intval($item['quantity'] ?? 1);

                $result = mysqli_query($conn, "SELECT products.*, shops.name AS shop_name
                                               FROM products
                                               JOIN shops ON products.shop_id = shops.id
                                               WHERE products.id = $product_id");

                $product = mysqli_fetch_assoc($result);

                if (!$product) {
                    continue;
                }

                $addons_total = 0;
                $addons_text = [];

                foreach (($item['addons'] ?? []) as $addon_id) {
                    $addon_id = intval($addon_id);

                    $addon_result = mysqli_query($conn, "SELECT * FROM product_addons 
                                                         WHERE id = $addon_id AND product_id = $product_id");

                    $addon = mysqli_fetch_assoc($addon_result);

                    if ($addon) {
                        $addons_total += floatval($addon['addon_price']);
                        $addons_text[] = $addon['addon_name'] . ' +' . $addon['addon_price'] . ' EGP';
                    }
                }

                $subtotal = (floatval($product['price']) + $addons_total) * $qty;
                $total += $subtotal;
                ?>

                <tr>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['shop_name']); ?></td>
                    <td><?php echo empty($addons_text) ? 'No additions' : htmlspecialchars(implode(', ', $addons_text)); ?></td>
                    <td><?php echo $qty; ?></td>
                    <td><?php echo $subtotal; ?> EGP</td>
                    <td>
                        <a class="btn btn-red" href="/cart.php?remove=<?php echo $index; ?>">Remove</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table></div>

        <br>
        <h3>Items Total: <?php echo $total; ?> EGP</h3>
        <h3>Delivery Fee: 40 EGP</h3>
        <h3>Total Cash: <?php echo $total + 40; ?> EGP</h3>

        <a class="btn btn-green" href="/checkout.php">Checkout</a>
        <a class="btn btn-red" href="/cart.php?clear=1">Clear Cart</a>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
