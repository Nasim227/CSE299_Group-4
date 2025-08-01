<?php
session_start();
include('connection.php');
include('sidebar.php');

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Track Records - Dream Ride</title>
    <link rel="stylesheet" href="../css/track_record.css">
    <link rel="stylesheet" href="../css/addproduct.css">
</head>
<body>
    <div class="main-content">
        <h1 class="page-title">Track Records</h1>

        <div class="filter-section">
            <form action="track_record.php" method="get" class="filter-form">
                <div class="form-group">
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                </div>
                <div class="form-group">
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                </div>
                <button type="submit" class="filter-button">Filter Records</button>
                <a href="track_record.php" class="reset-button">Reset Filter</a>
            </form>
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

        <a href="track_record_pdf.php?start_date=<?php echo urlencode($start_date); ?>&end_date=<?php echo urlencode($end_date); ?>" class="download-button" target="_blank">Download Records</a>
    </div>
</body>
</html>