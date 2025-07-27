<?php
session_start();
include("connection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

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
    $prize = (float)$_POST["prize"];

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
        if ($checkStmt) {
            mysqli_stmt_bind_param($checkStmt, "s", $productName);
            mysqli_stmt_execute($checkStmt);
            mysqli_stmt_bind_result($checkStmt, $availableQty);

            if (mysqli_stmt_fetch($checkStmt)) {
                mysqli_stmt_close($checkStmt);

                if ($availableQty >= $quantity) {
                    $query = "INSERT INTO sell (Buyer_Name, Buyer_Email, Buyer_Contact_no, Company_name, Product_type, Product_name, Product_id, Quantity, Booked_date, Sell_date, Prize)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = mysqli_prepare($conn, $query);
                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "sssssssissd", $buyerName, $buyerEmail, $buyerContact, $companyName, $productType, $productName, $productId, $quantity, $bookedDate, $sellDate, $prize);

                        if (mysqli_stmt_execute($stmt)) {
                            $newQty = $availableQty - $quantity;

                            $updateQuery = "UPDATE $productTable SET available_qnty = ? WHERE Product_name = ?";
                            $updateStmt = mysqli_prepare($conn, $updateQuery);
                            if ($updateStmt) {
                                mysqli_stmt_bind_param($updateStmt, "is", $newQty, $productName);
                                mysqli_stmt_execute($updateStmt);
                                mysqli_stmt_close($updateStmt);
                            } else {
                                $message .= " (Error preparing stock update query: " . mysqli_error($conn) . ")";
                            }


                            $deleteBookingQuery = "DELETE FROM booked WHERE Product_name = ? AND Email = ? AND Product_id = ? AND Booked_date = ?";
                            $deleteBookingStmt = mysqli_prepare($conn, $deleteBookingQuery);
                            if ($deleteBookingStmt) {
                                mysqli_stmt_bind_param($deleteBookingStmt, "ssss", $productName, $buyerEmail, $productId, $bookedDate);
                                mysqli_stmt_execute($deleteBookingStmt);
                                mysqli_stmt_close($deleteBookingStmt);
                            } else {
                                $message .= " (Error preparing booking deletion query: " . mysqli_error($conn) . ")";
                            }

                            $message = "Sell record inserted successfully, stock updated, and booking removed!";
                        } else {
                            $message = "Error inserting sell record: " . mysqli_error($conn);
                        }
                        mysqli_stmt_close($stmt);
                    } else {
                        $message = "Database query failed to prepare (insert sell): " . mysqli_error($conn);
                    }
                } else {
                    $message = "Error: Only $availableQty items are in stock. Cannot complete the sale.";
                }
            } else {
                mysqli_stmt_close($checkStmt);
                $message = "Error: Product '$productName' not found or no quantity available.";
            }
        } else {
            $message = "Database query failed to prepare (check stock): " . mysqli_error($conn);
        }
    } else {
        $message = "Error: Product type not recognized.";
    }
}


$salesData = [];
$where_clauses = [];
$bind_params = [];
$types = '';

$buyer_name_val = $_GET['buyer_name_filter'] ?? '';
$product_name_val = $_GET['product_name_filter'] ?? '';
$buyer_email_val = $_GET['buyer_email_filter'] ?? '';
$sell_date_val = $_GET['sell_date_filter'] ?? '';
$product_id_val = $_GET['product_id_filter'] ?? '';

$query_sales = "SELECT Buyer_Name, Buyer_Email, Product_name, Product_id, Quantity, Sell_date, Prize FROM sell";

if (!empty($buyer_name_val)) {
    $where_clauses[] = "Buyer_Name LIKE ?";
    $bind_params[] = '%' . $buyer_name_val . '%';
    $types .= 's';
}
if (!empty($product_name_val)) {
    $where_clauses[] = "Product_name LIKE ?";
    $bind_params[] = '%' . $product_name_val . '%';
    $types .= 's';
}
if (!empty($buyer_email_val)) {
    $where_clauses[] = "Buyer_Email LIKE ?";
    $bind_params[] = '%' . $buyer_email_val . '%';
    $types .= 's';
}
if (!empty($sell_date_val)) {
    $where_clauses[] = "Sell_date = ?";
    $bind_params[] = $sell_date_val;
    $types .= 's';
}
if (!empty($product_id_val)) {
    $where_clauses[] = "Product_id LIKE ?";
    $bind_params[] = '%' . $product_id_val . '%';
    $types .= 's';
}

if (!empty($where_clauses)) {
    $query_sales .= " WHERE " . implode(" AND ", $where_clauses);
}

$query_sales .= " ORDER BY Sell_date DESC, Buyer_Name ASC";

$stmt_sales = mysqli_prepare($conn, $query_sales);

if ($stmt_sales) {
    if (!empty($bind_params)) {
        mysqli_stmt_bind_param($stmt_sales, $types, ...$bind_params);
    }
    mysqli_stmt_execute($stmt_sales);
    $result_sales = mysqli_stmt_get_result($stmt_sales);

    while ($row = mysqli_fetch_assoc($result_sales)) {
        $salesData[] = $row;
    }
    mysqli_stmt_close($stmt_sales);
} else {
    $message .= "Error preparing sales query for display: " . mysqli_error($conn);
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
        <div class="sold-products-container">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <div class="content-separator"></div>
            <u><h2>Record a New Sale</h2></u>
            <div class="options">
                <form method="POST" action="">
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
                                    <option value="">Select Product Type</option>
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
                <?php if (!empty($message)) echo "<p>" . htmlspecialchars($message) . "</p>"; ?>
            </div>

            <div class="content-separator"></div>
            <u><h2>Sold Products Records</h2></u>

            <div class="filter-section">
                <form method="GET" action="soldproducts.php">
                    <label for="buyer_name_filter">Buyer Name:</label>
                    <input type="text" name="buyer_name_filter" id="buyer_name_filter" placeholder="Buyer Name" value="<?php echo htmlspecialchars($buyer_name_val); ?>">

                    <label for="product_name_filter">Product Name:</label>
                    <input type="text" name="product_name_filter" id="product_name_filter" placeholder="Product Name" value="<?php echo htmlspecialchars($product_name_val); ?>">

                    <label for="buyer_email_filter">Buyer Email:</label>
                    <input type="email" name="buyer_email_filter" id="buyer_email_filter" placeholder="Buyer Email" value="<?php echo htmlspecialchars($buyer_email_val); ?>">

                    <label for="sell_date_filter">Sell Date:</label>
                    <input type="date" name="sell_date_filter" id="sell_date_filter" value="<?php echo htmlspecialchars($sell_date_val); ?>">

                    <label for="product_id_filter">Product ID:</label>
                    <input type="text" name="product_id_filter" id="product_id_filter" placeholder="Product ID" value="<?php echo htmlspecialchars($product_id_val); ?>">

                    <button type="submit" id="apply_filters">Apply Filters</button>
                    <a href="soldproducts.php" class="action-button clear-filters-button">Clear Filters</a>
                </form>
            </div>

            <div class="sold-table-wrapper">
                <table border="2" id="sales_table">
                    <thead>
                        <tr>
                            <th>Sell Date</th>
                            <th>Buyer Name</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Total Price (৳)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($salesData)): ?>
                            <?php foreach ($salesData as $sale): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($sale['Sell_date']); ?></td>
                                    <td><?php echo htmlspecialchars($sale['Buyer_Name']); ?></td>
                                    <td><?php echo htmlspecialchars($sale['Product_name']); ?></td>
                                    <td><?php echo htmlspecialchars($sale['Quantity']); ?></td>
                                    <td><?php echo htmlspecialchars(number_format($sale['Prize'] * $sale['Quantity'], 2)); ?></td>
                                    <td>
                                        <a href="view_invoice.php?buyer_name=<?php echo urlencode($sale['Buyer_Name']); ?>&sell_date=<?php echo urlencode($sale['Sell_date']); ?>&product_name=<?php echo urlencode($sale['Product_name']); ?>&product_id=<?php echo urlencode($sale['Product_id']); ?>" class="action-button view-invoice">
                                            View Invoice
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6">No sales records found for the applied filters.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>
</body>
</html>