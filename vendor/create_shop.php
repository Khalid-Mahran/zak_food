<?php
require_once '../includes/auth.php';
requireRole('vendor');

$vendor_id = $_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = intval($_POST['category_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $query = "INSERT INTO shops (vendor_id, category_id, name, address, phone, description, status)
              VALUES ($vendor_id, $category_id, '$name', '$address', '$phone', '$description', 'pending')";

    if (mysqli_query($conn, $query)) {
        $success = 'Restaurant request sent. ZAK admin must approve it.';
    } else {
        $error = 'Failed to create restaurant';
    }
}

$categories = mysqli_query($conn, "SELECT * FROM categories");

include '../includes/header.php';
?>

<div class="container">
    <h2>Create Restaurant</h2>
    <br>

    <?php if ($error): ?><div class="alert"><?php echo $error; ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert success"><?php echo $success; ?></div><?php endif; ?>

    <form method="POST">
        <label>Restaurant Type</label>
        <select name="category_id" required>
            <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                <option value="<?php echo $category['id']; ?>">
                    <?php echo htmlspecialchars($category['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Restaurant Name</label>
        <input type="text" name="name" required>

        <label>Pickup Address</label>
        <textarea name="address" required></textarea>

        <label>Phone</label>
        <input type="text" name="phone">

        <label>Description</label>
        <textarea name="description"></textarea>

        <button class="btn" type="submit">Send Request</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
