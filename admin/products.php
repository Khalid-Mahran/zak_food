<?php
require_once '../includes/auth.php';
requireRole('admin');

$products = mysqli_query($conn, "SELECT products.*, shops.name AS shop_name, categories.name AS category_name, subcategories.name AS subcategory_name
                                FROM products
                                JOIN shops ON products.shop_id = shops.id
                                JOIN categories ON products.category_id = categories.id
                                JOIN subcategories ON products.subcategory_id = subcategories.id
                                ORDER BY products.id DESC");

include '../includes/header.php';
?>

<div class="container">
    <h2>All Menu Items / Products</h2>
    <br>

    <table>
        <tr>
            <th>ID</th>
            <th>Item</th>
            <th>Shop</th>
            <th>Category</th>
            <th>Topic</th>
            <th>Price</th>
        </tr>

        <?php while ($product = mysqli_fetch_assoc($products)): ?>
            <tr>
                <td><?php echo $product['id']; ?></td>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td><?php echo htmlspecialchars($product['shop_name']); ?></td>
                <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                <td><?php echo htmlspecialchars($product['subcategory_name']); ?></td>
                <td><?php echo $product['price']; ?> EGP</td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
