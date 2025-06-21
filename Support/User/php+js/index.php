<?php
    session_start();

    include("connection.php");

    $bike_brands = [];
    $scooter_brands = [];

    $bike_result = mysqli_query($conn, "SELECT DISTINCT Company_name FROM bike");
    while ($row = mysqli_fetch_assoc($bike_result)) {
        $bike_brands[] = $row['Company_name'];
    }

    $scooter_result = mysqli_query($conn, "SELECT DISTINCT Company_name FROM scooter");
    while ($row = mysqli_fetch_assoc($scooter_result)) {
        $scooter_brands[] = $row['Company_name'];
    }
?>

<!DOCTYPE html>
 <html>
 <head>
       <link rel="icon" type="image/png" href="../Images/logo2.png">
      <title>Dream Ride</title> 
      <link rel = "stylesheet" href="indec.css">
      <link rel="stylesheet" href="bbrrnds.css">
      <script src="https://kit.fontawesome.com/7139f829c6.js" crossorigin="anonymous"></script>
   </head>

    <body>

        <?php include("navbar.php"); ?>

        <main>
            <img src="../Images/hmeback2.png" width="1256px">
            <h2 style="text-align: center;">Welcome to dream Ride,</h2>

            <p class="pr" style="margin-left: 15px;"> Your trusted destination for premium bikes and scooters in Dhaka, Bangladesh. We take pride in being one of the leading retailers of two-wheelers, offering an extensive range of motorcycles, scooters, and accessories to cater to every rider’s needs. Whether you are a daily commuter, an adventure enthusiast, or a first-time rider, we have the perfect ride for you!</p>

            <br>

            <u><h2 style="text-align: center;">Our Proud Partners</h2></u>

            <?php
                $companies = [];

                function fetch_companies($conn, $table, &$companies) {
                    $sql = "SELECT DISTINCT Company_name, Company_pic FROM $table";
                    $result = mysqli_query($conn, $sql);

                    while ($row = mysqli_fetch_assoc($result)) {
                        $name = $row['Company_name'];
                        $pic = $row['Company_pic'];

                        if (!isset($companies[$name])) {
                            $companies[$name] = $pic;
                        }
                    }
                }

                fetch_companies($conn, 'bike', $companies);
                fetch_companies($conn, 'scooter', $companies);
            ?>

            <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; margin: 20px;">
                <?php foreach ($companies as $name => $pic): ?>
                    <?php
                        $category = ''; 
                        $category = 'Bikes';
                    ?>
                    <div class="bik">
                        <a target="_blank">
                            <img src="../<?php echo htmlspecialchars($pic); ?>" alt="<?php echo htmlspecialchars($name); ?>">
                        </a>
                        <div class="description"><?php echo htmlspecialchars($name); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <br>

            <u><h2 style="text-align: center;">Our Products</h2></u>

            
    <div class="bik">
        <a href="shwprdcts.php?category=Bikes">
            <img src="../Images/bike.jpg">
        </a>
        <div class="description">Bike</div>
    </div>

    <div class="bik">
        <a href="shwprdcts.php?category=Scooters">
            <img src="../Images/scooter.jpg">
        </a>
        <div class="description">Scooter</div>
    </div>

    <div class="bik">
        <a href="shwprdcts.php?category=Engine_oil">
            <img src="../Images/castrol.jpg">
        </a>
        <div class="description">Engine Oil</div>
    </div>

    <div class="bik">
        <a href="shwprdcts.php?category=Helmet">
            <img src="../Images/helmet.jpg">
        </a>
        <div class="description">Helmet</div>
    </div>



            <br><br><br><br><br>

        </main>

        <?php include("footer.html"); ?>

    </body>
 </html>