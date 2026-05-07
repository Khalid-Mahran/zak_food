<?php
require_once 'includes/auth.php';
requireLogin();

$user_id = $_SESSION['user_id'];

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM user_addresses WHERE id = $id AND user_id = $user_id");
    header('Location: /addresses.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $label = mysqli_real_escape_string($conn, $_POST['label']);
    $full_address = mysqli_real_escape_string($conn, $_POST['full_address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $area = mysqli_real_escape_string($conn, $_POST['area']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    mysqli_query($conn, "INSERT INTO user_addresses (user_id, label, full_address, city, area, notes)
                         VALUES ($user_id, '$label', '$full_address', '$city', '$area', '$notes')");
}

$addresses = mysqli_query($conn, "SELECT * FROM user_addresses WHERE user_id = $user_id ORDER BY id DESC");

include 'includes/header.php';
?>

<div class="container">
    <h2>My Addresses</h2>
    <p class="small">Add Home, Work, Shop, or any delivery address.</p>
    <br>

    <form method="POST">
        <label>Address Label</label>
        <input type="text" name="label" placeholder="Home / Work / Shop" required>

        <label>Full Address</label>
        <textarea name="full_address" required></textarea>

        <label>City</label>
        <input type="text" name="city">

        <label>Area</label>
        <input type="text" name="area">

        <label>Notes</label>
        <textarea name="notes" placeholder="Floor, apartment, landmark..."></textarea>

        <button class="btn" type="submit">Add Address</button>
    </form>

    <div class="grid">
        <?php while ($address = mysqli_fetch_assoc($addresses)): ?>
            <div class="card">
                <span class="badge"><?php echo htmlspecialchars($address['label']); ?></span>
                <h3><?php echo htmlspecialchars($address['area']); ?> - <?php echo htmlspecialchars($address['city']); ?></h3>
                <p><?php echo htmlspecialchars($address['full_address']); ?></p>
                <p class="small"><?php echo htmlspecialchars($address['notes']); ?></p>
                <a class="btn btn-red" href="/addresses.php?delete=<?php echo $address['id']; ?>" onclick="return confirmAction('Delete this address?')">Delete</a>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
