<?php
session_start();
include("connection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

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
    <link rel="stylesheet" href="../css/invoice.css">
    <script src="https://kit.fontawesome.com/7139f829c6.js" crossorigin="anonymous"></script>
</head>
<body>
    <?php include("sidebar.php"); ?>
    <main>
        <div class="sold-products-container">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <div class="content-separator"></div>
            
            <u><h2>Sold Products Records</h2></u>
            <div class="filter-section">
                <form method="GET" action="printinvoice.php">
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
                    <a href="printinvoice.php" class="action-button clear-filters-button">Clear Filters</a>
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