<?php
require_once '../includes/auth.php';
requireRole('admin');

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'] === 'approve' ? 'approved' : 'rejected';

    mysqli_query($conn, "UPDATE shops SET status = '$action' WHERE id = $id");
    header('Location: /admin/shops.php');
    exit;
}

$shops = mysqli_query($conn, "SELECT shops.*, users.name AS owner_name, users.email, categories.name AS category_name
                              FROM shops
                              JOIN users ON shops.vendor_id = users.id
                              JOIN categories ON shops.category_id = categories.id
                              ORDER BY shops.id DESC");

include '../includes/header.php';
?>

<div class="container">
    <h2>Restaurant Requests</h2>
    <br>

    <table>
        <tr>
            <th>ID</th>
            <th>Restaurant</th>
            <th>Owner</th>
            <th>Category</th>
            <th>Address</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php while ($shop = mysqli_fetch_assoc($shops)): ?>
            <tr>
                <td><?php echo $shop['id']; ?></td>
                <td><?php echo htmlspecialchars($shop['name']); ?></td>
                <td><?php echo htmlspecialchars($shop['owner_name']); ?><br><?php echo htmlspecialchars($shop['email']); ?></td>
                <td><?php echo htmlspecialchars($shop['category_name']); ?></td>
                <td><?php echo htmlspecialchars($shop['address']); ?></td>
                <td><?php echo $shop['status']; ?></td>
                <td>
                    <?php if ($shop['status'] === 'pending'): ?>
                        <a class="btn btn-green" href="/admin/shops.php?action=approve&id=<?php echo $shop['id']; ?>">Approve</a>
                        <a class="btn btn-red" href="/admin/shops.php?action=reject&id=<?php echo $shop['id']; ?>">Reject</a>
                    <?php else: ?>
                        Done
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
