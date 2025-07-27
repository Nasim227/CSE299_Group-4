<?php
session_start();
include("connection.php");
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="icon" type="image/png" href="../Images/logo2.png">
    <title>Dream Ride</title>
    <link rel="stylesheet" href="../css/prdcts.css">
</head>
<body>

<?php include("navbar.php"); ?>

<main style="display: flex;">
    <div class="sidebar-container">
        <?php 
        $currentPage = 'products';
        include("sidebar.php"); 
        ?>
    </div>
    <div class="content-container" style="flex: 1;">
<?php
$sidebarFiltering = false;
$selectedCategories = [];
$selectedBrands = [];
$minPrice = null;
$maxPrice = null;

if (isset($_GET['category']) && is_array($_GET['category'])) {
    $sidebarFiltering = true;
    $selectedCategories = array_map('htmlspecialchars', $_GET['category']);
    $selectedCategories = array_filter($selectedCategories, function($cat) {
        return !empty($cat) && $cat !== '';
    });
}

if (isset($_GET['brands']) && is_array($_GET['brands'])) {
    $sidebarFiltering = true;
    $selectedBrands = array_map('htmlspecialchars', $_GET['brands']);
    $selectedBrands = array_filter($selectedBrands, function($brand) {
        return !empty($brand) && $brand !== '';
    });
}

if (isset($_GET['min_price']) && !empty($_GET['min_price'])) {
    $sidebarFiltering = true;
    $minPrice = (float)$_GET['min_price'];
}

if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
    $sidebarFiltering = true;
    $maxPrice = (float)$_GET['max_price'];
}

if ($sidebarFiltering) {
    
    $categoriesToSearch = [];
    if (empty($selectedCategories) || in_array('All', $selectedCategories)) {
        $categoriesToSearch = [
            ['table' => 'bike', 'name' => 'Bikes'],
            ['table' => 'scooter', 'name' => 'Scooters'],
            ['table' => 'engine_oil', 'name' => 'Engine Oil'],
            ['table' => 'helmet', 'name' => 'Helmets']
        ];
    } else {
        foreach ($selectedCategories as $cat) {
            switch ($cat) {
                case 'Bikes':
                    $categoriesToSearch[] = ['table' => 'bike', 'name' => 'Bikes'];
                    break;
                case 'Scooters':
                    $categoriesToSearch[] = ['table' => 'scooter', 'name' => 'Scooters'];
                    break;
                case 'Engine_oil':
                    $categoriesToSearch[] = ['table' => 'engine_oil', 'name' => 'Engine Oil'];
                    break;
                case 'Helmet':
                    $categoriesToSearch[] = ['table' => 'helmet', 'name' => 'Helmets'];
                    break;
            }
        }
    }
    
    $hasAnyProducts = false;
    
    foreach ($categoriesToSearch as $categoryInfo) {
        $table = $categoryInfo['table'];
        $categoryName = $categoryInfo['name'];
        
        $query = "SELECT Product_name, Product_pic, Company_name";
        
        $prizeColumn = '';
        switch ($table) {
            case 'bike':
            case 'scooter':
                $prizeColumn = 'Prize';
                break;
            case 'engine_oil':
            case 'helmet':
                $prizeColumn = 'Prize';
                break;
        }
        
        if ($prizeColumn) {
            $query .= ", $prizeColumn";
        }
        
        $query .= " FROM $table WHERE 1=1";
        
        if (!empty($selectedBrands)) {
            $brandConditions = [];
            foreach ($selectedBrands as $brand) {
                $escapedBrand = mysqli_real_escape_string($conn, $brand);
                $brandConditions[] = "Company_name = '$escapedBrand'";
            }
            $query .= " AND (" . implode(' OR ', $brandConditions) . ")";
        }
        
        if ($prizeColumn && ($minPrice !== null || $maxPrice !== null)) {
            if ($minPrice !== null) {
                $query .= " AND $prizeColumn >= $minPrice";
            }
            if ($maxPrice !== null) {
                $query .= " AND $prizeColumn <= $maxPrice";
            }
        }
        
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $hasAnyProducts = true;
            
            echo "<h1 style='text-align:center; margin-top:40px;'>$categoryName</h1>";
            
            echo '<div class="product-container" style="margin-bottom: 40px;">';
            
            while ($row = mysqli_fetch_assoc($result)) {
                echo '
                <div class="bik">
                    <a href="prdctinfo.php?product=' . urlencode($row["Product_name"]) . '&category=' . $table . '">
                        <img src="../' . htmlspecialchars($row["Product_pic"]) . '" alt="' . htmlspecialchars($row["Product_name"]) . '">
                    </a>
                    <div class="description">' . htmlspecialchars($row["Product_name"]) . '</div>
                </div>';
            }
            
            echo '</div>';
        }
    }
    
    if (!$hasAnyProducts) {
        echo "<p style='text-align:center; margin-top: 40px;'>No products found matching your filters.</p>";
    }
    
} 

elseif (isset($_GET['category']) && !is_array($_GET['category'])) {
    $category = htmlspecialchars($_GET['category']);
    $brand = isset($_GET['brand']) ? htmlspecialchars($_GET['brand']) : null;

    echo "<h1 style='text-align:center; margin-top:20px;'>";
    if ($brand) echo "<strong>$brand</strong> ";
    echo "<strong>$category</strong>";
    echo "</h1>";

    echo '<div class="product-container">';

    if ($category === 'Bikes' || $category === 'Scooters') {
        $table = ($category === 'Bikes') ? 'bike' : 'scooter';


        if ($brand) {
            $escaped_brand = mysqli_real_escape_string($conn, $brand);
            $query = "SELECT Product_name, Product_pic FROM $table WHERE Company_name = '$escaped_brand'";
        } else {
            $query = "SELECT Product_name, Product_pic FROM $table";
        }


        $result = mysqli_query($conn, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo '
                <div class="bik">
                    <a href="prdctinfo.php?product=' . urlencode($row["Product_name"]) . '&category=' . $table . '">
                        <img src="../' . htmlspecialchars($row["Product_pic"]) . '" alt="' . htmlspecialchars($row["Product_name"]) . '">
                    </a>
                    <div class="description">' . htmlspecialchars($row["Product_name"]) . '</div>
                </div>';
            }
        } else {
            echo "<p style='text-align:center;'>No products found for this brand.</p>";
        }

    } elseif ($category === 'Accessories') {

        echo "<h2 style='text-align:center; margin-top:30px;'>Engine Oil</h2>";
        $query = "SELECT Product_name, Product_pic FROM engine_oil";
        $result = mysqli_query($conn, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            echo '
            <div class="bik">
                <a href="prdctinfo.php?product=' . urlencode($row["Product_name"]) . '&category=engine_oil">
                    <img src="../' . htmlspecialchars($row["Product_pic"]) . '" alt="' . htmlspecialchars($row["Product_name"]) . '">
                </a>
                <div class="description">' . htmlspecialchars($row["Product_name"]) . '</div>
            </div>';
        }

        echo "<h2 style='text-align:center; margin-top:40px;'>Helmet</h2>";
        $query = "SELECT Product_name, Product_pic FROM helmet";
        $result = mysqli_query($conn, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            echo '
            <div class="bik">
                <a href="prdctinfo.php?product=' . urlencode($row["Product_name"]) . '&category=helmet">
                    <img src="../' . htmlspecialchars($row["Product_pic"]) . '" alt="' . htmlspecialchars($row["Product_name"]) . '">
                </a>
                <div class="description">' . htmlspecialchars($row["Product_name"]) . '</div>
            </div>';
        }

    } elseif ($category === 'Engine_oil' || $category === 'Helmet') {

        $query = "SELECT Product_name, Product_pic FROM $category";
        $result = mysqli_query($conn, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            echo '
            <div class="bik">
                <a href="prdctinfo.php?product=' . urlencode($row["Product_name"]) . '&category=' . $category . '">
                    <img src="../' . htmlspecialchars($row["Product_pic"]) . '" alt="' . htmlspecialchars($row["Product_name"]) . '">
                </a>
                <div class="description">' . htmlspecialchars($row["Product_name"]) . '</div>
            </div>';
        }
    } else {
        echo "<p style='text-align:center;'>Invalid category selected.</p>";
    }

    echo '</div>';
} else {
    echo "<h2 style='text-align:center; margin-top:20px;'>No category selected.</h2>";
}
?>
</div>
</main>
<?php include("footer.html"); ?>

</body>
</html>

