<?php
require_once '../includes/auth.php';
requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['type'] === 'category') {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        mysqli_query($conn, "INSERT INTO categories (name) VALUES ('$name')");
    }

    if ($_POST['type'] === 'subcategory') {
        $category_id = intval($_POST['category_id']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        mysqli_query($conn, "INSERT INTO subcategories (category_id, name) VALUES ($category_id, '$name')");
    }
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY id DESC");
$subcategories = mysqli_query($conn, "SELECT subcategories.*, categories.name AS category_name
                                     FROM subcategories
                                     JOIN categories ON subcategories.category_id = categories.id
                                     ORDER BY subcategories.id DESC");

include '../includes/header.php';
?>

<div class="container">
    <h2>Manage Categories</h2>
    <br>

    <form method="POST">
        <input type="hidden" name="type" value="category">
        <label>New Category</label>
        <input type="text" name="name" placeholder="Example: Desserts" required>
        <button class="btn" type="submit">Add Category</button>
    </form>

    <form method="POST">
        <input type="hidden" name="type" value="subcategory">
        <label>New Topic / Subcategory</label>
        <input type="text" name="name" placeholder="Example: Sushi" required>

        <label>Parent Category</label>
        <select name="category_id" required>
            <?php mysqli_data_seek($categories, 0); while ($category = mysqli_fetch_assoc($categories)): ?>
                <option value="<?php echo $category['id']; ?>">
                    <?php echo htmlspecialchars($category['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button class="btn" type="submit">Add Topic</button>
    </form>

    <h3>Categories</h3>
    <table>
        <tr><th>ID</th><th>Name</th></tr>
        <?php mysqli_data_seek($categories, 0); while ($category = mysqli_fetch_assoc($categories)): ?>
            <tr>
                <td><?php echo $category['id']; ?></td>
                <td><?php echo htmlspecialchars($category['name']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <br>
    <h3>Topics</h3>
    <table>
        <tr><th>ID</th><th>Topic</th><th>Category</th></tr>
        <?php while ($sub = mysqli_fetch_assoc($subcategories)): ?>
            <tr>
                <td><?php echo $sub['id']; ?></td>
                <td><?php echo htmlspecialchars($sub['name']); ?></td>
                <td><?php echo htmlspecialchars($sub['category_name']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
