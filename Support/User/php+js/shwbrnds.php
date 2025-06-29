<?php
    session_start();

    include("connection.php");
?>

<!DOCTYPE html>
 <html>
 <head>
       <link rel="icon" type="image/png" href="../Images/logo2.png">
      <title>Dream Ride</title> 
      <link rel="stylesheet" href="../css/brands.css">
      <script src="https://kit.fontawesome.com/7139f829c6.js" crossorigin="anonymous"></script>
   </head>

    <body>
        <?php include("navbar.php"); ?>

       <main>
    <?php
    if (isset($_GET['category'])) {
        $category = htmlspecialchars($_GET['category']);
        
        $displayCategory = $category;
        if ($category === 'Bikes') {
            $displayCategory = 'Bike';
        } elseif ($category === 'Scooters') {
            $displayCategory = 'Scooter';
        }
        
        echo "<h2 style='text-align:center; margin-top:20px;'>Available <strong>$displayCategory</strong> Brands</h2>";

        $shownCompanies = [];
        $tables = [];

        if ($category === 'Bikes') {
            $tables[] = 'bike';
        } elseif ($category === 'Scooters') {
            $tables[] = 'scooter';
        }

        echo '<div class="brand-container">';

        foreach ($tables as $table) {
            $query = "SELECT Company_name, Company_pic FROM $table";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $name = $row['Company_name'];
                    $pic = $row['Company_pic'];

                    if (!in_array($name, $shownCompanies)) {
                        $shownCompanies[] = $name;

                        echo '
                        <div class="bik">
                            <a href="shwprdcts.php?category=' . urlencode($category) . '&brand=' . urlencode($name) . '">
                                <img src="../' . htmlspecialchars($pic) . '" alt="' . htmlspecialchars($name) . '">
                            </a>
                            <div class="description">' . htmlspecialchars($name) . '</div>
                        </div>';
                    }
                }
            }
        }

        echo '</div>';
        echo '<br><br>';

        echo "<h2 style='text-align:center; margin-top:20px;'>Other <strong>$displayCategory</strong> Brands(Available SOON!!!)</h2>";

        if ($category === 'Bikes'){
            echo '<div class="brand-container">
            <div class="bik">
                <a target="_blank">
                    <img src="../Images/royal.png">
                </a>
                <div class="description">Royal Enfield</div>
            </div>
            
            <div class="bik">
            <a target="_blank">
                <img src="../Images/bmw.png">
            </a>
            <div class="description">BMW</div>
            
        </div>

        <div class="bik">
            <a target="_blank">
                <img src="../Images/hero.png">
            </a>
            <div class="description">Hero</div>
            
        </div>

        <div class="bik">
            <a target="_blank">
                <img src="../Images/bajaj.png">
            </a>
            <div class="description">Bajaj</div>
            
        </div>
        </div>';
        }
        

        elseif ($category === 'Scooters'){
            echo '<div class="brand-container">
            <div class="bik">
            <a target="_blank">
                <img src="../Images/vespa.png">
            </a>
            <div class="description">Vespa</div>
            
        </div>

        <div class="bik">
            <a target="_blank">
                <img src="../Images/hero.png">
            </a>
            <div class="description">Hero</div>
            
        </div>

        <div class="bik">
            <a target="_blank">
                <img src="../Images/bajaj.png">
            </a>
            <div class="description">Bajaj</div>
            
        </div>
        </div>';

        }

        if (empty($shownCompanies)) {
            echo "<p style='text-align:center;'>No brands found in the selected category.</p>";
        }
    } else {
        echo "<h2 style='text-align:center; margin-top:20px;'>No category selected.</h2>";
    }
    ?>
</main>

<?php include("footer.html"); ?>

</body>
</html>

