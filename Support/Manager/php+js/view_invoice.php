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
    <link rel="stylesheet" href="../css/soldproducts.css">
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f4e3;
            color: #3e2723;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }

        main {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: calc(100vh - 40px);
            margin-left: 0;
            width: 100%;
        }

        .invoice-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            border: 1px solid #ddd;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            border-radius: 10px;
        }
        .invoice-header {
            text-align: center;
            border-bottom: 2px solid #a0522d;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .invoice-header h1 {
            margin: 0;
            color: #8b4513;
            font-size: 2.2em;
        }
        .invoice-header p {
            color: #5a3d2b;
            font-size: 0.9em;
            margin-top: 5px;
        }
        .invoice-details table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .invoice-details table td {
            padding: 8px 0;
            vertical-align: top;
            font-size: 0.95em;
        }
        .invoice-details table td strong {
            color: #5a3d2b;
        }
        .invoice-items table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            border: 1px solid #c7b8a0;
            border-radius: 8px;
            overflow: hidden;
        }
        .invoice-items th, .invoice-items td {
            border: 1px solid #eee;
            padding: 12px 15px;
            text-align: left;
        }
        .invoice-items th {
            background-color: #a0522d;
            color: #f5f5dc;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .invoice-items tbody tr:nth-child(even) {
            background-color: #f0e8d9;
        }
        .invoice-items tbody tr:hover {
            background-color: #e8dfcc;
        }
        .invoice-items tfoot td {
            font-weight: bold;
            text-align: right;
            border-top: 2px solid #a0522d;
            padding-top: 15px;
            padding-bottom: 15px;
            background-color: #fdfaf5;
            color: #3e2723;
        }
        .invoice-items tfoot td:last-child {
            font-size: 1.2em;
            color: #8b4513;
        }
        .invoice-actions {
            text-align: center;
            margin-top: 30px;
        }
        .invoice-actions button {
            padding: 12px 25px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .invoice-actions button:hover {
            background-color: #0056b3;
        }
        .invoice-container > p {
            text-align: center;
            margin-top: 30px;
        }
        .invoice-container > p a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        .invoice-container > p a:hover {
            text-decoration: underline;
        }
        .invoice-logo {
            max-width: 250px;
            height: auto;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .invoice-container { padding: 20px; }
            .invoice-header h1 { font-size: 1.8em; }
            .invoice-items th, .invoice-items td { padding: 10px 12px; font-size: 0.9em; }
            .invoice-items tfoot td { padding-top: 10px; padding-bottom: 10px; font-size: 1em; }
            .invoice-items tfoot td:last-child { font-size: 1.1em; }
            .invoice-actions button { padding: 10px 20px; font-size: 1em; }
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 20mm;
            }
            body {
                background-color: #fff;
                padding: 0;
                margin: 0;
            }
            main {
                display: block;
            }
            .invoice-container {
                box-shadow: none;
                border: 1px solid #ccc;
                width: 100%;
                max-width: 100%;
                padding: 0;
                border-radius: 0;
            }
            .invoice-actions,
            .back-to-products-link {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <main>
        <div class="invoice-container">
            <?php if ($invoiceData): ?>
                <div class="invoice-header">
                    <img src="../image/logo2.png" alt="Dream Ride Logo" class="invoice-logo">
                    <h3>Sales Invoice</h3>
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
            
            <p class="back-to-products-link" style="text-align: center; margin-top: 20px;"><a href="soldproducts.php">Back to Sold Products</a></p>
        </div>
    </main>
</body>
</html>