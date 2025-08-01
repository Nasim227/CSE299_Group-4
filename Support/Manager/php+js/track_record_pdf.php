<?php
session_start();
include('connection.php');

if (!isset($_SESSION['email'])) {
    die("Access denied. Please log in.");
}

$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

$where_clause_sold = '';
if (!empty($start_date) && !empty($end_date)) {
    $where_clause_sold = "WHERE Sell_date BETWEEN ? AND ?";
}

$where_clause_booked = '';
if (!empty($start_date) && !empty($end_date)) {
    $where_clause_booked = "WHERE Booked_date BETWEEN ? AND ?";
}

$sql_sold_total = "SELECT SUM(Quantity * Prize) AS total_sold_amount FROM sell " . $where_clause_sold;
$stmt_sold_total = $conn->prepare($sql_sold_total);
if (!empty($start_date) && !empty($end_date)) {
    $stmt_sold_total->bind_param("ss", $start_date, $end_date);
}
$stmt_sold_total->execute();
$result_sold_total = $stmt_sold_total->get_result();
$row_sold_total = $result_sold_total->fetch_assoc();
$total_sold_amount = $row_sold_total['total_sold_amount'] ?? 0;

$sql_booked_total = "
    SELECT SUM(b.Quantity * COALESCE(bi.Prize, s.Prize, h.Prize, eo.Prize)) AS total_booked_amount
    FROM booked b
    LEFT JOIN bike bi ON b.Product_id = bi.Product_id
    LEFT JOIN scooter s ON b.Product_id = s.Product_id
    LEFT JOIN helmet h ON b.Product_id = h.Product_id
    LEFT JOIN engine_oil eo ON b.Product_id = eo.Product_id
    " . $where_clause_booked;
$stmt_booked_total = $conn->prepare($sql_booked_total);
if (!empty($start_date) && !empty($end_date)) {
    $stmt_booked_total->bind_param("ss", $start_date, $end_date);
}
$stmt_booked_total->execute();
$result_booked_total = $stmt_booked_total->get_result();
$row_booked_total = $result_booked_total->fetch_assoc();
$total_booked_amount = $row_booked_total['total_booked_amount'] ?? 0;

$sql_sold_products = "SELECT * FROM sold_products " . $where_clause_sold . " ORDER BY Sell_date DESC";
$stmt_sold_products = $conn->prepare($sql_sold_products);
if (!empty($start_date) && !empty($end_date)) {
    $stmt_sold_products->bind_param("ss", $start_date, $end_date);
}
$stmt_sold_products->execute();
$result_sold_products = $stmt_sold_products->get_result();
$sold_products = $result_sold_products->fetch_all(MYSQLI_ASSOC);

$sql_booked_products = "SELECT * FROM booked_products " . $where_clause_booked . " ORDER BY Booked_date DESC";
$stmt_booked_products = $conn->prepare($sql_booked_products);
if (!empty($start_date) && !empty($end_date)) {
    $stmt_booked_products->bind_param("ss", $start_date, $end_date);
}
$stmt_booked_products->execute();
$result_booked_products = $stmt_booked_products->get_result();
$booked_products = $result_booked_products->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dream Ride - Track Record Invoice</title>
    <link rel="stylesheet" href="../css/track_record_pdf.css">
    <script>
        function printPage() {
            window.print();
        }
    </script>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <img src="../image/logo2.png" alt="Company Logo" class="company-logo">
            <div class="header-details">
                <h1 class="company-name">Dream Ride</h1>
                <p class="invoice-details-text">Track Record Generated: <?php echo date('Y-m-d H:i:s'); ?></p>
            </div>
        </div>

        <div class="summary-grid">
            <div class="summary-card">
                <div class="icon-box icon-sales">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="summary-info">
                    <h2>Total Sales Amount</h2>
                    <p class="amount">৳<?php echo number_format($total_sold_amount, 2); ?></p>
                </div>
            </div>
            <div class="summary-card">
                <div class="icon-box icon-booked">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="summary-info">
                    <h2>Total Booked Amount</h2>
                    <p class="amount">৳<?php echo number_format($total_booked_amount, 2); ?></p>
                </div>
            </div>
        </div>

        <div class="table-section">
            <h2>Sold Products History</h2>
            <?php if (!empty($sold_products)): ?>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Buyer Name</th>
                            <th>Product Name</th>
                            <th>Product ID</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Sell Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sold_products as $sold_product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($sold_product['Buyer_Name']); ?></td>
                            <td><?php echo htmlspecialchars($sold_product['Product_name']); ?></td>
                            <td><?php echo htmlspecialchars($sold_product['Product_id']); ?></td>
                            <td><?php echo htmlspecialchars($sold_product['Quantity']); ?></td>
                            <td><?php echo htmlspecialchars($sold_product['Prize']); ?></td>
                            <td><?php echo htmlspecialchars($sold_product['Sell_date']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="no-data">No sold products found.</p>
            <?php endif; ?>
        </div>

        <div class="table-section">
            <h2>Booked Products History</h2>
            <?php if (!empty($booked_products)): ?>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th>Product Name</th>
                            <th>Product ID</th>
                            <th>Quantity</th>
                            <th>Booked Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($booked_products as $booked_product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booked_product['Name']); ?></td>
                            <td><?php echo htmlspecialchars($booked_product['Product_name']); ?></td>
                            <td><?php echo htmlspecialchars($booked_product['Product_id']); ?></td>
                            <td><?php echo htmlspecialchars($booked_product['Quantity']); ?></td>
                            <td><?php echo htmlspecialchars($booked_product['Booked_date']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="no-data">No booked products found.</p>
            <?php endif; ?>
        </div>
        
        <div class="invoice-footer">
            <p>Thank you for your business!</p>
            <button class="print-button" onclick="printPage()">Print</button>
        </div>
    </div>
</body>
</html>