<?php
    session_start();
    include("connection.php");
    if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
    $query = "SELECT name, email, contact_no FROM signup";
    $result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="icon" type="image/png" href="../image/logo2.png">
    <title>Manager Dashboard - Dream Ride</title>
    <link rel="stylesheet" href="../css/customers.css">
    <script src="https://kit.fontawesome.com/7139f829c6.js" crossorigin="anonymous"></script>
</head>
    <body>
        <?php include("sidebar.php"); ?>
        <main>
        <div style="text-align: center;">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <div class="content-separator"></div>
            <u><h2>Customers Information</h2></u>
            <div class="customer-table-container">
                <table border="2">
                    <tr>
                        <th width="150">Name</th>
                        <th width="250">Email</th>
                        <th width="150">Contact No</th>
                    </tr>
                    <?php
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>" . $row['email'] . "</td>";
                            echo "<td>" . $row['contact_no'] . "</td>";
                            echo "</tr>";
                        }
                    ?>
                </table>
            </div>
        </div>
            
        </main>
    </body>
</html>
