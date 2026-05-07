<?php
require_once 'config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if ($role === 'admin') {
        $error = 'Admin registration is not allowed';
    } else {
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");

        if (mysqli_num_rows($check) > 0) {
            $error = 'Email already exists';
        } else {
            $approved = $role === 'vendor' ? 0 : 1;

            $query = "INSERT INTO users (name, email, password, role, phone, is_approved)
                      VALUES ('$name', '$email', '$password', '$role', '$phone', $approved)";

            if (mysqli_query($conn, $query)) {
                if ($role === 'vendor') {
                    $success = 'Restaurant request sent. ZAK admin will review and approve your account.';
                } else {
                    $success = 'Account created successfully. You can login now.';
                }
            } else {
                $error = 'Registration failed';
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <h2>Register</h2>
    <br>

    <?php if ($error): ?><div class="alert"><?php echo $error; ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert success"><?php echo $success; ?></div><?php endif; ?>

    <form method="POST">
        <label>Name</label>
        <input type="text" name="name" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Phone</label>
        <input type="text" name="phone">

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Role</label>
        <select name="role" required>
            <option value="customer">Customer</option>
            <option value="vendor">Restaurant / Vendor</option>
            <option value="delivery">Delivery Worker</option>
        </select>

        <button class="btn" type="submit">Create Account</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
