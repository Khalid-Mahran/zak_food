<?php
require_once '../includes/auth.php';
requireRole('vendor');

$vendor_id = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shop_id = intval($_POST['shop_id']);
    $category_id = intval($_POST['category_id']);
    $subcategory_id = intval($_POST['subcategory_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);

    $image_path = '';

    if (!empty($_FILES['image']['name'])) {
        $dir = '../assets/uploads/products/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $file_name = time() . '_' . basename($_FILES['image']['name']);
        $target = $dir . $file_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $image_path = 'assets/uploads/products/' . $file_name;
        }
    }

    $query = "INSERT INTO products (vendor_id, shop_id, category_id, subcategory_id, name, description, price, quantity, image, status)
              VALUES ($vendor_id, $shop_id, $category_id, $subcategory_id, '$name', '$description', $price, $quantity, '$image_path', 'pending')";

    if (mysqli_query($conn, $query)) {
        $product_id = mysqli_insert_id($conn);

        for ($i = 1; $i <= 5; $i++) {
            $addon_name = trim($_POST["addon_name_$i"] ?? '');
            $addon_price = floatval($_POST["addon_price_$i"] ?? 0);

            if ($addon_name !== '') {
                $safe_addon = mysqli_real_escape_string($conn, $addon_name);
                mysqli_query($conn, "INSERT INTO product_addons (product_id, addon_name, addon_price)
                                     VALUES ($product_id, '$safe_addon', $addon_price)");
            }
        }

        $success = 'Food item sent to admin for approval.';
    } else {
        $error = 'Failed to add item';
    }
}

$shops = mysqli_query($conn, "SELECT * FROM shops WHERE vendor_id = $vendor_id AND status = 'approved'");
$categories = mysqli_query($conn, "SELECT * FROM categories");
$subcategories = mysqli_query($conn, "SELECT * FROM subcategories");

include '../includes/header.php';
?>

<div class="container">
    <h2>Add Food Item</h2>
    <br>

    <?php if ($error): ?><div class="alert"><?php echo $error; ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert success"><?php echo $success; ?></div><?php endif; ?>

    <?php if (mysqli_num_rows($shops) === 0): ?>
        <div class="alert">You need an approved restaurant first.</div>
        <a class="btn" href="/vendor/create_shop.php">Create Restaurant</a>
    <?php else: ?>
        <form method="POST" enctype="multipart/form-data">
            <label>Restaurant</label>
            <select name="shop_id" required>
                <?php while ($shop = mysqli_fetch_assoc($shops)): ?>
                    <option value="<?php echo $shop['id']; ?>">
                        <?php echo htmlspecialchars($shop['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Category</label>
            <select name="category_id" required>
                <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                    <option value="<?php echo $category['id']; ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Topic</label>
            <select name="subcategory_id" required>
                <?php while ($sub = mysqli_fetch_assoc($subcategories)): ?>
                    <option value="<?php echo $sub['id']; ?>">
                        <?php echo htmlspecialchars($sub['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Food Name</label>
            <input type="text" name="name" required>

            <label>Description</label>
            <textarea name="description" required></textarea>

            <label>Food Image</label>
            <input type="file" name="image">

            <label>Price</label>
            <input type="number" step="0.01" name="price" required>

            <label>Quantity</label>
            <input type="number" name="quantity" required>

            <h3>Additions / Checkboxes</h3>
            <p class="small">Example: Pepsi, Water, Salad, Extra Garlic, Extra Cheese</p>

            <?php for ($i = 1; $i <= 5; $i++): ?>
                <label>Addition <?php echo $i; ?></label>
                <input type="text" name="addon_name_<?php echo $i; ?>" placeholder="Addition name">
                <input type="number" step="0.01" name="addon_price_<?php echo $i; ?>" placeholder="Addition price">
            <?php endfor; ?>

            <button class="btn" type="submit">Send To Admin</button>
        </form>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
