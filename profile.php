<?php
require_once 'includes/auth.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    $image_sql = '';

    if (!empty($_FILES['profile_image']['name'])) {
        $dir = 'assets/uploads/profiles/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $file_name = time() . '_' . basename($_FILES['profile_image']['name']);
        $target = $dir . $file_name;

        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target)) {
            $image_sql = ", profile_image = '$target'";
        }
    }

    $query = "UPDATE users SET name = '$name', phone = '$phone' $image_sql WHERE id = $user_id";

    if (mysqli_query($conn, $query)) {
        $_SESSION['name'] = $name;
        $success = 'Profile updated successfully';
    } else {
        $error = 'Profile update failed';
    }
}

$result = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($result);

include 'includes/header.php';
?>

<div class="container">
    <h2>My Profile</h2>
    <br>

    <?php if ($error): ?><div class="alert"><?php echo $error; ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert success"><?php echo $success; ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <?php if (!empty($user['profile_image'])): ?>
            <img class="profile-img" src="/<?php echo htmlspecialchars($user['profile_image']); ?>">
        <?php else: ?>
            <div class="profile-img"></div>
        <?php endif; ?>

        <label>Profile Photo</label>
        <input type="file" name="profile_image">

        <label>Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

        <label>Email</label>
        <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>

        <label>Phone</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">

        <label>Role</label>
        <input type="text" value="<?php echo htmlspecialchars($user['role']); ?>" disabled>

        <button class="btn" type="submit">Save Profile</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
