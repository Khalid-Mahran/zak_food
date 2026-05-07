<?php
require_once '../includes/auth.php';
requireRole('admin');

if (isset($_GET['approve_vendor'])) {
    $id = intval($_GET['approve_vendor']);
    mysqli_query($conn, "UPDATE users SET is_approved = 1 WHERE id = $id AND role = 'vendor'");
    header('Location: /admin/users.php');
    exit;
}

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");

include '../includes/header.php';
?>

<div class="container">
    <h2>Users</h2>
    <br>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Approved</th>
            <th>Action</th>
        </tr>

        <?php while ($user = mysqli_fetch_assoc($users)): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo $user['role']; ?></td>
                <td><?php echo $user['is_approved'] ? 'Yes' : 'No'; ?></td>
                <td>
                    <?php if ($user['role'] === 'vendor' && !$user['is_approved']): ?>
                        <a class="btn btn-green" href="/admin/users.php?approve_vendor=<?php echo $user['id']; ?>">Approve Restaurant Account</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
