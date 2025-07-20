<?php
session_start();
include("connection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

function getDateCondition($column, $filter) {
    switch ($filter) {
        case 'week':
            return "AND $column >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
        case 'month':
            return "AND $column >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        case 'year':
            return "AND $column >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
        default:
            return "";
    }
}

$filter = $_GET['filter'] ?? 'overall';

$bookedQuery = "SELECT SUM(Quantity) AS total_quantity, 
                SUM(CASE 
                    WHEN Product_type IN ('bike', 'scooter') THEN Quantity * (SELECT ROUND(Prize * 0.98, 2) FROM {$conn->real_escape_string('bike')} WHERE Product_id = b.Product_id)
                    ELSE Quantity * (SELECT Prize FROM {$conn->real_escape_string('engine_oil')} WHERE Product_id = b.Product_id)
                END) AS total_amount
                FROM booked b 
                WHERE 1 " . getDateCondition("Booked_date", $filter);

$bookedResult = mysqli_query($conn, $bookedQuery);
$bookedData = mysqli_fetch_assoc($bookedResult);

$soldQuery = "SELECT SUM(Quantity) AS total_quantity, SUM(Prize) AS total_amount FROM sell WHERE 1 " . getDateCondition("Sell_date", $filter);
$soldResult = mysqli_query($conn, $soldQuery);
$soldData = mysqli_fetch_assoc($soldResult);
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="icon" type="image/png" href="../image/logo2.png">
    <title>Manager Dashboard - Dream Ride</title>
    <link rel="stylesheet" href="../css/index.css">
    <script src="https://kit.fontawesome.com/7139f829c6.js" crossorigin="anonymous"></script>
</head>
<body>

<?php include("sidebar.php"); ?>

<div class="content">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <div class="content-separator"></div>
    <p>This is the Manager Dashboard of Dream Ride.</p>

    <div class="filter-links">
        <a href="?filter=overall" class="<?= $filter == 'overall' ? 'active' : '' ?>">Overall</a>
        <a href="?filter=week" class="<?= $filter == 'week' ? 'active' : '' ?>">Last Week</a>
        <a href="?filter=month" class="<?= $filter == 'month' ? 'active' : '' ?>">Last Month</a>
        <a href="?filter=year" class="<?= $filter == 'year' ? 'active' : '' ?>">Last Year</a>
    </div>

    <div class="stats-container">
        <div class="stat-card">
            <h3>Total Booked Products</h3>
            <div class="number"><?= $bookedData['total_quantity'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
            <h3>Total Booking Amount</h3>
            <div class="number">৳<?= number_format($bookedData['total_amount'] ?? 0, 2) ?></div>
        </div>
        <div class="stat-card">
            <h3>Total Sold Products</h3>
            <div class="number"><?= $soldData['total_quantity'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
            <h3>Total Sales Amount</h3>
            <div class="number">৳<?= number_format($soldData['total_amount'] ?? 0, 2) ?></div>
        </div>
    </div>
</div>

</body>
</html>
