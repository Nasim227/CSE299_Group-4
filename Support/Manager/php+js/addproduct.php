<?php
session_start();
include("connection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$product_type = isset($_POST['product_type']) ? $_POST['product_type'] : 'Bike';

if (isset($_POST['add_product'])) {
    $product_type = $_POST['product_type'];
    $table_name = strtolower($product_type);
    $company_name = $_POST['company_name'];
    $product_name = $_POST['product_name'];
    $product_id = $_POST['product_id'];
    $product_pic = $_POST['product_pic'];
    $company_pic = $_POST['company_pic'];
    $available_qnty = $_POST['available_qnty'];
    $prize = $_POST['prize'];

    $check_query = "SELECT * FROM product WHERE Product_id = ?";
    $stmt_check = $conn->prepare($check_query);
    $stmt_check->bind_param("s", $product_id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        $addedMessage = "Product already exists.";
    } else {
        $query_product = "INSERT INTO product (Company_name, Product_type, Product_name, Product_id) VALUES (?, ?, ?, ?)";
        $stmt_product = $conn->prepare($query_product);
        $stmt_product->bind_param("ssss", $company_name, $product_type, $product_name, $product_id);

        if ($stmt_product->execute()) {
            if ($product_type === 'Bike' || $product_type === 'Scooter') {
                $release_date = $_POST['release_date'];
                $engine = $_POST['engine'];
                $mileage = $_POST['mileage'];

                $query_product_type = "INSERT INTO $table_name 
                (Company_name, Product_type, Product_name, Product_id, Release_date, Engine, Mileage, Prize, Available_qnty, Company_pic, Product_pic) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt_product_type = $conn->prepare($query_product_type);
                $stmt_product_type->bind_param("ssssssssiss", 
                    $company_name, $product_type, $product_name, $product_id, 
                    $release_date, $engine, $mileage, $prize, 
                    $available_qnty, $company_pic, $product_pic);
            } else {
                $query_product_type = "INSERT INTO $table_name 
                (Company_name, Product_type, Product_name, Product_id, Prize, Available_qnty, Product_pic) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt_product_type = $conn->prepare($query_product_type);
                $stmt_product_type->bind_param("ssssiss", 
                    $company_name, $product_type, $product_name, $product_id, 
                    $prize, $available_qnty, $product_pic);
            }

            if ($stmt_product_type->execute()) {
                $addedMessage = "$product_type added successfully.";
            } else {
                $addedMessage = "Error adding $product_type details.";
            }
        } else {
            $addedMessage = "Error adding product.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="icon" type="image/png" href="../image/logo2.png">
    <title>Manager Dashboard - Dream Ride</title>
    <link rel="stylesheet" href="../css/addproduct.css">
    <script src="https://kit.fontawesome.com/7139f829c6.js" crossorigin="anonymous"></script>
</head>

<body>
<?php include("sidebar.php"); ?>

<main>
    <div class="dropdown">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <div class="content-separator"></div>
        <u><h2>Add New Products to the Shop</h2></u>
        <div class="options">
            <form method="POST">
                <table>
                    <tr>
                        <td><label for="product_type">Product Type:</label></td>
                        <td>
                            <select name="product_type" id="product_type" onchange="toggleFields()" required>
                                <option value="Bike" <?php if ($product_type === 'Bike') echo 'selected'; ?>>Bike</option>
                                <option value="Scooter" <?php if ($product_type === 'Scooter') echo 'selected'; ?>>Scooter</option>
                                <option value="Helmet" <?php if ($product_type === 'Helmet') echo 'selected'; ?>>Helmet</option>
                                <option value="Engine_Oil" <?php if ($product_type === 'Engine_Oil') echo 'selected'; ?>>Engine Oil</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="company_name">Company Name:</label></td>
                        <td><input type="text" name="company_name" placeholder="Company Name" required></td>
                    </tr>
                    <tr>
                        <td><label for="product_name">Product Name:</label></td>
                        <td><input type="text" name="product_name" placeholder="Product Name" required></td>
                    </tr>
                    <tr>
                        <td><label for="product_id">Product ID:</label></td>
                        <td><input type="text" name="product_id" placeholder="Product ID" required></td>
                    </tr>
                    <tr>
                        <td><label for="company_pic">Company Pic URL:</label></td>
                        <td><input type="text" name="company_pic" placeholder="Company Pic URL"></td>
                    </tr>
                    <tr>
                        <td><label for="product_pic">Product Pic URL:</label></td>
                        <td><input type="text" name="product_pic" placeholder="Product Pic URL"></td>
                    </tr>
                    <tr>
                        <td><label for="available_qnty">Available Quantity:</label></td>
                        <td><input type="number" name="available_qnty" placeholder="Available Quantity" required></td>
                    </tr>
                    <tr>
                        <td><label for="prize">Prize:</label></td>
                        <td><input type="number" step="0.01" name="prize" placeholder="Prize" required></td>
                    </tr>
                </table>

                <table id="bikeScooterFields" style="display: none;">
                    <tr>
                        <td><label for="release_date">Release Date:</label></td>
                        <td><input type="date" name="release_date" placeholder="Release Date"></td>
                    </tr>
                    <tr>
                        <td><label for="engine">Engine:</label></td>
                        <td><input type="text" name="engine" placeholder="Engine"></td>
                    </tr>
                    <tr>
                        <td><label for="mileage">Mileage:</label></td>
                        <td><input type="text" name="mileage" placeholder="Mileage"></td>
                    </tr>
                </table>

                <button type="submit" class="atl" style="margin-left: 133px;" name="add_product">Add Product</button>

            </form>
            <?php if (!empty($addedMessage)) echo "<p style='color: black; font-weight: bold;'>$addedMessage</p>"; ?>
        </div>
    </div>
</main>

<script>
function toggleFields() {
    const productType = document.getElementById('product_type').value;
    const bikeFields = document.getElementById('bikeScooterFields');
    if (productType === 'Bike' || productType === 'Scooter') {
        bikeFields.style.display = 'table';
    } else {
        bikeFields.style.display = 'none';
    }
}

window.onload = toggleFields;
</script>

</body>
</html>
