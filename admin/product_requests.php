<?php
require_once '../includes/auth.php';
requireRole('admin');

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'] === 'approve' ? 'approved' : 'rejected';

    mysqli_query($conn, "UPDATE products SET status = '$action' WHERE id = $id");
    header('Location: /admin/product_requests.php');
    exit;
}

$products = mysqli_query($conn, "SELECT products.*, shops.name AS shop_name, users.name AS owner_name
                                FROM products
                                JOIN shops ON products.shop_id = shops.id
                                JOIN users ON products.vendor_id = users.id
                                ORDER BY products.id DESC");

include '../includes/header.php';
?>

<div class="container">
    <h2>Product Approval Requests</h2>
    <br>

    <table>
        <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Product</th>
            <th>Restaurant</th>
            <th>Owner</th>
            <th>Price</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php while ($product = mysqli_fetch_assoc($products)): ?>
            <tr>
                <td><?php echo $product['id']; ?></td>
                <td>
                    <?php if (!empty($product['image'])): ?>
                        <img src="/<?php echo htmlspecialchars($product['image']); ?>" style="width:80px;height:60px;object-fit:cover;border-radius:8px;">
                    <?php else: ?>
                        No image
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($product['name']); ?><br><span class="small"><?php echo htmlspecialchars($product['description']); ?></span></td>
                <td><?php echo htmlspecialchars($product['shop_name']); ?></td>
                <td><?php echo htmlspecialchars($product['owner_name']); ?></td>
                <td><?php echo $product['price']; ?> EGP</td>
                <td><?php echo $product['status']; ?></td>
                <td>
                    <?php if ($product['status'] === 'pending'): ?>
                        <a class="btn btn-green" href="/admin/product_requests.php?action=approve&id=<?php echo $product['id']; ?>">Approve</a>
                        <a class="btn btn-red" href="/admin/product_requests.php?action=reject&id=<?php echo $product['id']; ?>">Reject</a>
                    <?php else: ?>
                        Done
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
