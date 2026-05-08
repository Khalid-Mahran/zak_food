<?php
require_once 'includes/auth.php';
requireLogin();
$user_id = $_SESSION['user_id'];
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM user_addresses WHERE id = $id AND user_id = $user_id");
    header('Location: /addresses.php?deleted=1'); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $label        = mysqli_real_escape_string($conn, trim($_POST['label']));
    $full_address = mysqli_real_escape_string($conn, trim($_POST['full_address']));
    $city         = mysqli_real_escape_string($conn, trim($_POST['city']));
    $area         = mysqli_real_escape_string($conn, trim($_POST['area']));
    $notes        = mysqli_real_escape_string($conn, trim($_POST['notes']));
    mysqli_query($conn, "INSERT INTO user_addresses (user_id, label, full_address, city, area, notes) VALUES ($user_id, '$label', '$full_address', '$city', '$area', '$notes')");
    header('Location: /addresses.php?added=1'); exit;
}
$addresses = mysqli_query($conn, "SELECT * FROM user_addresses WHERE user_id = $user_id ORDER BY id DESC");
include 'includes/header.php';
?>
<div class="container">
    <div class="page-header"><h2>📍 My Addresses</h2></div>
    <?php if (isset($_GET['added'])): ?><div class="alert success">Address added successfully.</div><?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?><div class="alert success">Address deleted.</div><?php endif; ?>
    <form method="POST">
        <label>Address Label</label>
        <input type="text" name="label" placeholder="Home / Work / Shop" required>
        <label>Full Address</label>
        <textarea name="full_address" placeholder="Street, building, floor..." required></textarea>
        <label>City</label>
        <input type="text" name="city" placeholder="Cairo">
        <label>Area</label>
        <input type="text" name="area" placeholder="Nasr City">
        <label>Notes for Delivery</label>
        <textarea name="notes" placeholder="Floor, apartment, landmark..."></textarea>
        <button class="btn" type="submit">+ Add Address</button>
    </form>
    <?php if (mysqli_num_rows($addresses) === 0): ?>
        <div class="empty-state"><span class="icon">📭</span><h3>No addresses yet</h3><p>Add your delivery address above.</p></div>
    <?php else: ?>
        <div class="grid">
            <?php while ($address = mysqli_fetch_assoc($addresses)): ?>
                <div class="card">
                    <span class="badge"><?php echo htmlspecialchars($address['label']); ?></span>
                    <h3><?php echo htmlspecialchars($address['area']); ?><?php if ($address['city']): ?>, <?php echo htmlspecialchars($address['city']); ?><?php endif; ?></h3>
                    <p><?php echo htmlspecialchars($address['full_address']); ?></p>
                    <?php if ($address['notes']): ?><p class="small">📝 <?php echo htmlspecialchars($address['notes']); ?></p><?php endif; ?>
                    <a class="btn btn-red btn-sm" href="/addresses.php?delete=<?php echo $address['id']; ?>" onclick="return confirmAction('Delete this address?')">Delete</a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
