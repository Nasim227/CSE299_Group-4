<?php
    session_start();

    include("connection.php");

    if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

    $bike_query = "SELECT * FROM vwbike";
    $scooter_query = "SELECT * FROM vwscooter";
    $helmet_query = "SELECT * FROM vwhelmet";
    $oil_query = "SELECT * FROM vwengineoil";

    $bike_result = mysqli_query($conn, $bike_query);
    $scooter_result = mysqli_query($conn, $scooter_query);
    $helmet_result = mysqli_query($conn, $helmet_query);
    $oil_result = mysqli_query($conn, $oil_query);


?>

<!DOCTYPE html>
<html>
<head>
    <link rel="icon" type="image/png" href="../image/logo2.png">
    <title>Manager Dashboard - Dream Ride</title>
    <link rel="stylesheet" href="../css/vwproducts.css">
    <script src="https://kit.fontawesome.com/7139f829c6.js" crossorigin="anonymous"></script>
</head>

    <body>
        <?php include("sidebar.php"); ?>

        <main>
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <div class="content-separator"></div>
            <div style="text-align: center;">
            <u><h2>All Products Info</h2></u>

            <br>

            <div class="product-section">
                <u><h3>Bike</h3></u>
                <table border="2">
                    <tr>								
                        <th width="150">Company_name</th>
                        <th width="220">Product_name</th>
                        <th width="120">Product_id</th>
                        <th width="150">Release_date</th>
                        <th width="120">Available_qnty</th>
                        <th width="120">Engine</th>
                        <th width="120">Mileage</th>
                        <th width="120">Prize</th>
                    </tr>

                    <?php while ($row = mysqli_fetch_assoc($bike_result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['Company_name']) ?></td>
                            <td><?= htmlspecialchars($row['Product_name']) ?></td>
                            <td><?= htmlspecialchars($row['Product_id']) ?></td>
                            <td><?= htmlspecialchars($row['Release_date']) ?></td>
                            <td><?= htmlspecialchars($row['Available_qnty']) ?></td>
                            <td><?= htmlspecialchars($row['Engine']) ?></td>
                            <td><?= htmlspecialchars($row['Mileage']) ?></td>
                            <td><?= htmlspecialchars($row['Prize']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>

                <br>

                <u><h3>Scooter</h3></u>
                <table border="2">
                    <tr>								
                        <th width="150">Company_name</th>
                        <th width="220">Product_name</th>
                        <th width="120">Product_id</th>
                        <th width="150">Release_date</th>
                        <th width="120">Available_qnty</th>
                        <th width="120">Engine</th>
                        <th width="120">Mileage</th>
                        <th width="120">Prize</th>
                    </tr>

                    <?php while ($row = mysqli_fetch_assoc($scooter_result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['Company_name']) ?></td>
                            <td><?= htmlspecialchars($row['Product_name']) ?></td>
                            <td><?= htmlspecialchars($row['Product_id']) ?></td>
                            <td><?= htmlspecialchars($row['Release_date']) ?></td>
                            <td><?= htmlspecialchars($row['Available_qnty']) ?></td>
                            <td><?= htmlspecialchars($row['Engine']) ?></td>
                            <td><?= htmlspecialchars($row['Mileage']) ?></td>
                            <td><?= htmlspecialchars($row['Prize']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>

                <br>

                <u><h3>Helmet</h3></u>
                <table border="2">
                    <tr>								
                        <th width="150">Company_name</th>
                        <th width="150">Product_name</th>
                        <th width="120">Product_id</th>
                        <th width="120">Available_qnty</th>
                        <th width="120">Prize</th>
                    </tr>

                    <?php while ($row = mysqli_fetch_assoc($helmet_result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['Company_name']) ?></td>
                            <td><?= htmlspecialchars($row['Product_name']) ?></td>
                            <td><?= htmlspecialchars($row['Product_id']) ?></td>
                            <td><?= htmlspecialchars($row['Available_qnty']) ?></td>
                            <td><?= htmlspecialchars($row['Prize']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>

                <br>

                <u><h3>Engine Oil</h3></u>
                <table border="2">
                    <tr>								
                        <th width="150">Company_name</th>
                        <th width="150">Product_name</th>
                        <th width="120">Product_id</th>
                        <th width="120">Available_qnty</th>
                        <th width="120">Prize</th>
                    </tr>

                    <?php while ($row = mysqli_fetch_assoc($oil_result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['Company_name']) ?></td>
                            <td><?= htmlspecialchars($row['Product_name']) ?></td>
                            <td><?= htmlspecialchars($row['Product_id']) ?></td>
                            <td><?= htmlspecialchars($row['Available_qnty']) ?></td>
                            <td><?= htmlspecialchars($row['Prize']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
            </div>
           
        </main>

    </body>
 </html>