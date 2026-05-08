<?php
require_once '../includes/auth.php';
requireRole('vendor');

$vendor_id = $_SESSION['user_id'];

$products = mysqli_query($conn, "SELECT products.*, shops.name AS shop_name, categories.name AS category_name, subcategories.name AS subcategory_name
                                FROM products
                                JOIN shops ON products.shop_id = shops.id
                                JOIN categories ON products.category_id = categories.id
                                JOIN subcategories ON products.subcategory_id = subcategories.id
                                WHERE products.vendor_id = $vendor_id
                                ORDER BY products.id DESC");

include '../includes/header.php';
?>

<div class="container">
    <h2>My Menu</h2>
    <br>

    <table>
        <tr>
            <th>Image</th>
            <th>Item</th>
            <th>Restaurant</th>
            <th>Topic</th>
            <th>Price</th>
            <th>Status</th>
        </tr>

        <?php while ($product = mysqli_fetch_assoc($products)): ?>
            <tr>
                <td>
                    <?php if (!empty($product['image'])): ?>
                        <img src="/<?php echo htmlspecialchars($product['image']); ?>" style="width:80px;height:60px;object-fit:cover;border-radius:8px;">
                    <?php else: ?>
                        No image
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td><?php echo htmlspecialchars($product['shop_name']); ?></td>
                <td><?php echo htmlspecialchars($product['subcategory_name']); ?></td>
                <td><?php echo $product['price']; ?> EGP</td>
                <td><?php echo $product['status']; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
