<?php
  session_start();
  include("connection.php");

  if (!isset($_SESSION['user_id'])) {
      header("Location: login.php");
      exit();
  }


  $result_sold = null;

      $sold_query = "SELECT * FROM sold_products";
      $result_sold = mysqli_query($conn, $sold_query);
?>
<!DOCTYPE html>
<html>
<head>
  <link rel="icon" type="image/png" href="../image/logo2.png">
  <title>Manager Dashboard - Dream Ride</title>
  <link rel="stylesheet" href="../css/sold.css">
  <script src="https://kit.fontawesome.com/7139f829c6.js" crossorigin="anonymous"></script>
</head>
  <body>
      <?php include("sidebar.php"); ?>
      <main>
          <div class="sold-content-container">
              <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
              <div class="content-separator"></div>
              <u><h2>Sold Products</h2></u>
              <div class="sold-table-wrapper">
                  <table border="2">
                      <tr>
                          <th width = "120">Buyer_Name</th>
                          <th width = "150">Buyer_Email</th>
                          <th width = "120">Buyer_Contact_no </th>
                          <th width = "120">Company_name</th>
                          <th width = "120">Product Type</th>
                          <th width = "150">Product Name</th>
                          <th width = "120">Product ID</th>
                          <th width = "80">Quantity</th>
                          <th width = "150">Booked Date</th>
                          <th width = "150">Sell_date</th>
                          <th width = "150">Prize</th>
                      </tr>
                      <?php
                          if ($result_sold) {
                              while ($row = mysqli_fetch_assoc($result_sold)) {
                                  echo "<tr>";
                                  echo "<td>" . htmlspecialchars($row['Buyer_Name']) . "</td>";
                                  echo "<td>" . htmlspecialchars($row['Buyer_Email']) . "</td>";
                                  echo "<td>" . htmlspecialchars($row['Buyer_Contact_no']) . "</td>";
                                  echo "<td>" . htmlspecialchars($row['Company_name']) . "</td>";
                                  echo "<td>" . htmlspecialchars($row['Product_type']) . "</td>";
                                  echo "<td>" . htmlspecialchars($row['Product_name']) . "</td>";
                                  echo "<td>" . htmlspecialchars($row['Product_id']) . "</td>";
                                  echo "<td>" . htmlspecialchars($row['Quantity']) . "</td>";
                                  echo "<td>" . htmlspecialchars($row['Booked_date']) . "</td>";
                                  echo "<td>" . htmlspecialchars($row['Sell_date']) . "</td>";
                                  echo "<td>" . htmlspecialchars($row['Prize']) . "</td>";
                                  echo "</tr>";
                              }
                          } else {
                              echo "<tr><td colspan='11'>No sold products found or an error occurred.</td></tr>";
                          }
                      ?>
                  </table>
              </div>
          </div>
      </main>
  </body>
</html>
