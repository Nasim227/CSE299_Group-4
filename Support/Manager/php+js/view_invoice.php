<?php
session_start();
include("connection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$invoiceData = null;
$error = "";
$totalPrice = 0;

$buyerName = $_GET['buyer_name'] ?? '';
$sellDate = $_GET['sell_date'] ?? '';
$productName = $_GET['product_name'] ?? '';

if (!empty($buyerName) && !empty($sellDate) && !empty($productName)) {
    $query = "SELECT Buyer_Name, Buyer_Email, Buyer_Contact_no, Company_name, Product_type, Product_name, Product_id, Quantity, Booked_date, Sell_date, Prize FROM sell WHERE Buyer_Name = ? AND Sell_date = ? AND Product_name = ?";
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sss", $buyerName, $sellDate, $productName);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            $invoiceData = mysqli_fetch_assoc($result);
            if ($invoiceData) {
                $totalPrice = $invoiceData['Quantity'] * $invoiceData['Prize'];
            }
        } else {
            $error = "Invoice not found or multiple entries matched for these details. (Consider unique Sale ID for better precision)";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Error preparing invoice query: " . mysqli_error($conn);
    }
} else {
    $error = "Invalid invoice request. Missing buyer name, sell date, or product name.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Invoice Details</title>
    <link rel="stylesheet" href="../css/viewinvoice.css">
</head>
<body>
    <main>
        <div class="invoice-container">
            <?php if ($invoiceData): ?>
                <div class="invoice-header">
                    <img src="../image/logo2.png" alt="Dream Ride Logo" class="invoice-logo">
                    <h1>Sales Invoice</h1>
                    <p>Date: <?php echo htmlspecialchars($invoiceData['Sell_date']); ?></p>
                </div>
                <div class="invoice-details">
                    <table>
                        <tr>
                            <td><strong>Buyer Name:</strong> <?php echo htmlspecialchars($invoiceData['Buyer_Name']); ?></td>
                            <td><strong>Company:</strong> <?php echo htmlspecialchars($invoiceData['Company_name']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Buyer Email:</strong> <?php echo htmlspecialchars($invoiceData['Buyer_Email']); ?></td>
                            <td><strong>Contact:</strong> <?php echo htmlspecialchars($invoiceData['Buyer_Contact_no']); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="invoice-items">
                    <table>
                        <thead>
                            <tr>
                                <th>Product Type</th>
                                <th>Product Name</th>
                                <th>Product ID</th>
                                <th>Quantity</th>
                                <th>Unit Price (৳)</th>
                                <th>Line Total (৳)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo htmlspecialchars($invoiceData['Product_type']); ?></td>
                                <td><?php echo htmlspecialchars($invoiceData['Product_name']); ?></td>
                                <td><?php echo htmlspecialchars($invoiceData['Product_id']); ?></td>
                                <td><?php echo htmlspecialchars($invoiceData['Quantity']); ?></td>
                                <td><?php echo htmlspecialchars(number_format($invoiceData['Prize'], 2)); ?></td>
                                <td><?php echo htmlspecialchars(number_format($invoiceData['Quantity'] * $invoiceData['Prize'], 2)); ?></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5"><strong>Total Amount:</strong></td>
                                <td><strong>৳<?php echo htmlspecialchars(number_format($totalPrice, 2)); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="invoice-actions">
                    <button onclick="window.print()">Print Invoice</button>
                </div>
            <?php else: ?>
                <p style="color: red; text-align: center;"><?php echo $error ?: "No invoice data found."; ?></p>
            <?php endif; ?>
                        
            <p class="back-to-products-link" style="text-align: center; margin-top: 20px;"><a href="printinvoice.php">Go Back</a></p>
        </div>
    </main>
</body>
</html>