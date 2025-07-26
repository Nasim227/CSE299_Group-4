<?php
    session_start();
    include("connection.php");

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }


    $result_booked = null;

        $booked_query = "SELECT * FROM booked_products";
        $result_booked = mysqli_query($conn, $booked_query);
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="icon" type="image/png" href="../image/logo2.png">
    <title>Manager Dashboard - Dream Ride</title>
    <link rel="stylesheet" href="../css/booked.css">
    <script src="https://kit.fontawesome.com/7139f829c6.js" crossorigin="anonymous"></script>
</head>
    <body>
        <?php include("sidebar.php"); ?>
        <main>
            <div class="booked-content-container">
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                <div class="content-separator"></div>
                <u><h2>Booked Products</h2></u>
                <div class="booked-table-wrapper">
                    <table border="2">
                        <tr>
                            <th width = "120">Name</th>
                            <th width = "150">Email</th>
                            <th width = "120">Contact No</th>
                            <th width = "150">Company Name</th>
                            <th width = "120">Product Type</th>
                            <th width = "150">Product Name</th>
                            <th width = "120">Product ID</th>
                            <th width = "80">Quantity</th>
                            <th width = "150">Booked Date</th>
                        </tr>
                        <?php
                            if ($result_booked) {
                                while ($row = mysqli_fetch_assoc($result_booked)) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['Name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['Email']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['Contact_no']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['Company_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['Product_type']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['Product_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['Product_id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['Quantity']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['Booked_date']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9'>No booked products found or an error occurred.</td></tr>";
                            }
                        ?>
                    </table>
                </div>
            </div>
        </main>
    </body>
</html>
