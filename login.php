<?php
require_once 'includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' LIMIT 1");

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        if (!password_verify($password, $user['password'])) {
            $error = 'Wrong password';
        } elseif ($user['role'] === 'vendor' && intval($user['is_approved']) !== 1) {
            $error = 'ZAK admin has not approved your restaurant account yet.';
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            redirectByRole($user['role']);
        }
    } else {
        $error = 'Email not found';
    }
}

include 'includes/header.php';
?>

<div class="container">
    <h2>Login</h2>
    <br>

    <?php if ($error): ?>
        <div class="alert"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button class="btn" type="submit">Login</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
