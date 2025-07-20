<?php
session_start();
include("connection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$valid_product_types = ['Bike', 'Scooter', 'Helmet', 'Engine_Oil'];
$all_attributes = [
    'Company_name', 'Product_type', 'Product_name', 'Product_id',
    'Release_date', 'Engine', 'Mileage', 'Prize', 'Available_qnty',
    'Company_pic', 'Product_pic'
];
$primary_keys = ['Product_id', 'Product_name', 'Company_name', 'Product_type'];

$update_message = "";
$delete_message = "";

$selected_product_type = $_POST['product_type'] ?? 'Bike';
$selected_attribute = $_POST['update_attribute'] ?? '';
$new_value = $_POST['new_value'] ?? '';
$reference = $_POST['reference'] ?? '';
$reference_value = $_POST['reference_value'] ?? '';

$attribute_options_by_type = [
    'Bike' => $all_attributes,
    'Scooter' => $all_attributes,
    'Helmet' => [
        'Company_name', 'Product_type', 'Product_name', 'Product_id',
        'Prize', 'Available_qnty', 'Company_pic', 'Product_pic'
    ],
    'Engine_Oil' => [
        'Company_name', 'Product_type', 'Product_name', 'Product_id',
        'Prize', 'Available_qnty', 'Company_pic', 'Product_pic'
    ],
];

$valid_attributes = $attribute_options_by_type[$selected_product_type] ?? [];

if (isset($_POST['updateprdct'])) {
    if (
        in_array($selected_product_type, $valid_product_types) &&
        in_array($selected_attribute, $valid_attributes) &&
        in_array($reference, ['Product_name', 'Product_id'])
    ) {
        $success_updates = 0;
        $error_messages = "";

        $specific_table = strtolower($selected_product_type);
        $tables_to_check = ['Product', $specific_table];

        foreach ($tables_to_check as $table) {
            $selected_attribute_escaped = mysqli_real_escape_string($conn, $selected_attribute);
            $column_check_query = "SHOW COLUMNS FROM `$table` LIKE '$selected_attribute_escaped'";
            $column_result = $conn->query($column_check_query);


            if ($column_result->num_rows > 0) {
                $update_sql = "UPDATE `$table` SET `$selected_attribute` = ? WHERE `$reference` = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("ss", $new_value, $reference_value);

                if ($update_stmt->execute()) {
                    if ($update_stmt->affected_rows > 0) {
                        $success_updates++;
                    }
                } else {
                    $error_messages .= "<p class='error-msg'>Error updating $table: " . $update_stmt->error . "</p>";
                }

                $update_stmt->close();
            }
        }

        if ($success_updates > 0) {
            $update_message = "<p class='success-msg'>Product updated successfully in $success_updates table(s)!</p>";
        } elseif (!empty($error_messages)) {
            $update_message = $error_messages;
        } else {
            $update_message = "<p class='error-msg'>No updates made. Please check inputs.</p>";
        }
    } else {
        $update_message = "<p class='error-msg'>Invalid input. Please try again.</p>";
    }
}

if (isset($_POST['dltprdct'])) {
    $product_type = $_POST['product_type'] ?? '';
    $delete_id = $_POST['dltid'] ?? '';

    if (in_array($product_type, $valid_product_types) && !empty($delete_id)) {
        $sql = "DELETE FROM Product WHERE Product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $delete_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $delete_message = "<p class='success-msg'>Product deleted successfully!</p>";
            } else {
                $delete_message = "<p class='error-msg'>No product found with the provided Product ID. Deletion failed.</p>";
            }
        } else {
            $delete_message = "<p class='error-msg'>Error deleting product: " . $stmt->error . "</p>";
        }

        $stmt->close();
    } else {
        $delete_message = "<p class='error-msg'>Invalid delete input. Please try again.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="icon" type="image/png" href="../image/logo2.png">
    <title>Manager Dashboard - Dream Ride</title>
    <link rel="stylesheet" href="../css/updelete.css">
    <script src="https://kit.fontawesome.com/7139f829c6.js" crossorigin="anonymous"></script>
</head>
<body>

<?php include("sidebar.php"); ?>

<main>
    <div class="dropdown">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <div class="content-separator"></div>
        <u><h2>Update Products Information</h2></u>
        <div class="options">
            <form method="POST">
                <table>
                    <tr>
                        <td><label for="product_type">Product Type:</label></td>
                        <td>
                            <select name="product_type" onchange="this.form.submit()" required>
                                <?php foreach ($valid_product_types as $type): ?>
                                    <option value="<?= $type ?>" <?= $selected_product_type === $type ? 'selected' : '' ?>>
                                        <?= $type === 'Engine_Oil' ? 'Engine Oil' : $type ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td><label for="update_attribute">Update attribute:</label></td>
                        <td>
                            <select name="update_attribute" required>
                                <?php foreach ($valid_attributes as $attr): ?>
                                    <option value="<?= $attr ?>" <?= $selected_attribute === $attr ? 'selected' : '' ?>>
                                        <?= $attr ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td><label for="new_value">New Value:</label></td>
                        <td><input type="text" name="new_value" value="<?= htmlspecialchars($new_value) ?>" required></td>
                    </tr>

                    <tr>
                        <td><label for="reference">Reference:</label></td>
                        <td>
                            <select name="reference" required>
                                <option value="Product_name" <?= $reference === 'Product_name' ? 'selected' : '' ?>>Product_name</option>
                                <option value="Product_id" <?= $reference === 'Product_id' ? 'selected' : '' ?>>Product_id</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td><label for="reference_value">Reference Value:</label></td>
                        <td><input type="text" name="reference_value" value="<?= htmlspecialchars($reference_value) ?>" required></td>
                    </tr>
                </table>

                <button type="submit" class="atl" name="updateprdct">Update</button>
            </form>

            <?php if (!empty($update_message)) echo $update_message; ?>
        </div>
    </div>

    <div class="dropdown2">
        <u><h2>Delete Products from the Shop</h2></u>
        <div class="options">
            <form method="POST">
                <table>
                    <tr>
                        <td><label for="product_type">Product Type:</label></td>
                        <td>
                            <select name="product_type" required>
                                <option value="Bike">Bike</option>
                                <option value="Scooter">Scooter</option>
                                <option value="Helmet">Helmet</option>
                                <option value="Engine_Oil">Engine Oil</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td><label for="dltid">Product Id:</label></td>
                        <td><input type="text" name="dltid" required></td>
                    </tr>
                </table>

                <button type="submit" class="atl" name="dltprdct">Delete</button>
            </form>

            <?php if (!empty($delete_message)) echo $delete_message; ?>
        </div>
    </div>
</main>

</body>
</html>
