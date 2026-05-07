<?php
require_once '../includes/auth.php';
requireRole('admin');

$vendors = mysqli_query($conn, "SELECT * FROM users WHERE role = 'vendor' ORDER BY id DESC");

include '../includes/header.php';
?>

<div class="container">
    <h2>Vendors</h2>
    <br>

    <table>
        <tr>
            <th>ID</th>
            <th>Vendor Name</th>
            <th>Email</th>
        </tr>

        <?php while ($vendor = mysqli_fetch_assoc($vendors)): ?>
            <tr>
                <td><?php echo $vendor['id']; ?></td>
                <td><?php echo htmlspecialchars($vendor['name']); ?></td>
                <td><?php echo htmlspecialchars($vendor['email']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
