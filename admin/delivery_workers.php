<?php
require_once '../includes/auth.php';
requireRole('admin');

$workers = mysqli_query($conn, "SELECT * FROM users WHERE role = 'delivery' ORDER BY id DESC");

include '../includes/header.php';
?>

<div class="container">
    <h2>Delivery Workers</h2>
    <br>

    <table>
        <tr>
            <th>ID</th>
            <th>Worker Name</th>
            <th>Email</th>
        </tr>

        <?php while ($worker = mysqli_fetch_assoc($workers)): ?>
            <tr>
                <td><?php echo $worker['id']; ?></td>
                <td><?php echo htmlspecialchars($worker['name']); ?></td>
                <td><?php echo htmlspecialchars($worker['email']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
