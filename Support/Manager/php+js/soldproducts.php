<?php
session_start();
include("connection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sell"])) {
    $buyerName = $_POST["buyer_name"];
    $buyerEmail = $_POST["buyer_email"];
    $buyerContact = $_POST["buyer_contact"];
    $companyName = $_POST["company_name"];
    $productType = $_POST["product_type"];
    $productName = $_POST["product_name"];
    $productId = $_POST["product_id"];
    $quantity = (int)$_POST["quantity"];
    $bookedDate = $_POST["booked_date"];
    $sellDate = date("Y-m-d");
    $prize = $_POST["prize"];

    $tableMap = [
        "Bike" => "bike",
        "Scooter" => "scooter",
        "Helmet" => "helmet",
        "Engine_Oil" => "engine_oil"
    ];

    $productTable = isset($tableMap[$productType]) ? $tableMap[$productType] : "";

    if (!empty($productTable)) {
        $checkQuery = "SELECT available_qnty FROM $productTable WHERE Product_name = ?";
        $checkStmt = mysqli_prepare($conn, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, "s", $productName);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_bind_result($checkStmt, $availableQty);

        if (mysqli_stmt_fetch($checkStmt)) {
            mysqli_stmt_close($checkStmt);

            if ($availableQty >= $quantity) {
                $query = "INSERT INTO sell (Buyer_Name, Buyer_Email, Buyer_Contact_no, Company_name, Product_type, Product_name, Product_id, Quantity, Booked_date, Sell_date, Prize)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "sssssssissd", $buyerName, $buyerEmail, $buyerContact, $companyName, $productType, $productName, $productId, $quantity, $bookedDate, $sellDate, $prize);

                if (mysqli_stmt_execute($stmt)) {
                    $newQty = $availableQty - $quantity;

                    $updateQuery = "UPDATE $productTable SET available_qnty = ? WHERE product_name = ?";
                    $updateStmt = mysqli_prepare($conn, $updateQuery);
                    mysqli_stmt_bind_param($updateStmt, "is", $newQty, $productName);
                    mysqli_stmt_execute($updateStmt);
                    mysqli_stmt_close($updateStmt);

                    $deleteBookingQuery = "DELETE FROM booked WHERE Product_name = ? AND Email = ? AND Product_id = ? AND Booked_date = ?";
                    $deleteBookingStmt = mysqli_prepare($conn, $deleteBookingQuery);
                    mysqli_stmt_bind_param($deleteBookingStmt, "ssss", $productName, $buyerEmail, $productId, $bookedDate);
                    mysqli_stmt_execute($deleteBookingStmt);
                    mysqli_stmt_close($deleteBookingStmt);

                    $message = "Sell record inserted successfully, stock updated, and booking removed!";
                } else {
                    $message = "Error inserting sell record: " . mysqli_error($conn);
                }

                mysqli_stmt_close($stmt);
            } else {
                $message = "Error: Only $availableQty items are in stock. Cannot complete the sale.";
            }
        } else {
            $message = "Error: Product not found.";
        }
    } else {
        $message = "Error: Product type not recognized.";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <link rel="icon" type="image/png" href="../image/logo2.png">
    <title>Manager Dashboard - Dream Ride</title>
    <link rel="stylesheet" href="../css/soldproducts.css">
    <script src="https://kit.fontawesome.com/7139f829c6.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php include("sidebar.php"); ?>

    <main>
        <div class="dropdown">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <div class="content-separator"></div>
            <u><h2>Add to Sell</h2></u>
            <div class="options">
                <form method="POST">
                    <table>
                        <tr>
                            <td><label for="buyer_name">Buyer Name:</label></td>
                            <td><input type="text" name="buyer_name" placeholder="Buyer Name" required></td>
                        </tr>
                        <tr>
                            <td><label for="buyer_email">Buyer Email:</label></td>
                            <td><input type="email" name="buyer_email" placeholder="Buyer Email" required></td>
                        </tr>
                        <tr>
                            <td><label for="buyer_contact">Buyer Contact No:</label></td>
                            <td><input type="text" name="buyer_contact" placeholder="Buyer Contact No" required></td>
                        </tr>
                        <tr>
                            <td><label for="company_name">Company Name:</label></td>
                            <td><input type="text" name="company_name" placeholder="Company Name" required></td>
                        </tr>
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
                            <td><label for="product_name">Product Name:</label></td>
                            <td><input type="text" name="product_name" placeholder="Product Name" required></td>
                        </tr>
                        <tr>
                            <td><label for="product_id">Product ID:</label></td>
                            <td><input type="text" name="product_id" placeholder="Product ID" required></td>
                        </tr>
                        <tr>
                            <td><label for="quantity">Quantity:</label></td>
                            <td><input type="number" name="quantity" min="1" placeholder="Quantity" required></td>
                        </tr>
                        <tr>
                            <td><label for="booked_date">Booked Date:</label></td>
                            <td><input type="date" name="booked_date" required></td>
                        </tr>
                        <tr>
                            <td><label for="prize">Price (৳):</label></td>
                            <td><input type="number" name="prize" step="0.01" placeholder="Price (in ৳)" required></td>
                        </tr>
                    </table>

                    <button type="submit" class="atl" name="sell">Confirm Sell</button>
                </form>
                <?php if (isset($message)) echo "<p>$message</p>"; ?>
            </div>
        </div>
    </main>
</body>
</html>
