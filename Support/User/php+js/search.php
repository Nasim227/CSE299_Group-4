<?php
session_start();
include("connection.php");
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="icon" type="image/png" href="images/logo2.png">
    <title>Search Results</title>
    <link rel="stylesheet" href="../css/prdcts.css">
</head>
<body>

<?php include("navbar.php"); ?>

<main style="display: flex;">
    <div class="sidebar-container">
        <?php 
        $currentPage = 'search'; 
        include("sidebar.php"); 
        ?>
    </div>

    <div class="content-container" style="flex: 1;">
        <?php
        if (isset($_GET['query']) && trim($_GET['query']) !== '') {
            $search = htmlspecialchars(trim($_GET['query']));
            $escaped_search = mysqli_real_escape_string($conn, $search);

            $selected_categories = [];
            if (isset($_GET['category'])) {
                $selected_categories = is_array($_GET['category']) ? $_GET['category'] : [$_GET['category']];
                $selected_categories = array_filter($selected_categories, fn($cat) => !empty(trim($cat)));
            }

            if (empty($selected_categories) || in_array('All', $selected_categories)) {
                $selected_categories = ['Bikes', 'Scooters', 'Engine_oil', 'Helmet'];
            }

            $selected_brands = [];
            if (isset($_GET['brands'])) {
                $selected_brands = is_array($_GET['brands']) ? $_GET['brands'] : [$_GET['brands']];
                $selected_brands = array_filter($selected_brands, fn($brand) => !empty(trim($brand)));
            }

            $min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (int)$_GET['min_price'] : null;
            $max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (int)$_GET['max_price'] : null;

            echo "<h1 style='text-align:center; margin-top:20px;'>Search Results for: <strong>$search</strong></h1>";
            echo '<div class="product-container">';

            $categoryMap = [
                'Bikes' => ['table' => 'bike', 'label' => 'Bikes'],
                'Scooters' => ['table' => 'scooter', 'label' => 'Scooters'],
                'Engine_oil' => ['table' => 'engine_oil', 'label' => 'Engine Oil'],
                'Helmet' => ['table' => 'helmet', 'label' => 'Helmets']
            ];

            function hasColumn($conn, $table, $column) {
                $query = "SHOW COLUMNS FROM `$table` LIKE '$column'";
                $result = mysqli_query($conn, $query);
                return mysqli_num_rows($result) > 0;
            }

            function displayResults($conn, $table, $label, $searchTerm, $brands, $min, $max) {
                $escapedSearchTerm = mysqli_real_escape_string($conn, $searchTerm);

                $query = "SELECT Product_name, Product_pic, Prize";

                $hasCompany = hasColumn($conn, $table, 'Company_name');
                $hasType = hasColumn($conn, $table, 'Product_type');

                if ($hasCompany) $query .= ", Company_name";
                if ($hasType) $query .= ", Product_type";

                $query .= " FROM $table WHERE (";
                $conditions = ["Product_name LIKE '%$escapedSearchTerm%'"];
                if ($hasCompany) $conditions[] = "Company_name LIKE '%$escapedSearchTerm%'";
                if ($hasType) $conditions[] = "Product_type LIKE '%$escapedSearchTerm%'";
                $query .= implode(" OR ", $conditions) . ")";

                if (!empty($brands) && $hasCompany) {
                    $brand_conditions = array_map(function($brand) use ($conn) {
                        return "Company_name = '" . mysqli_real_escape_string($conn, $brand) . "'";
                    }, $brands);
                    $query .= " AND (" . implode(' OR ', $brand_conditions) . ")";
                }

                if ($min !== null) $query .= " AND Prize >= $min";
                if ($max !== null) $query .= " AND Prize <= $max";

                $query .= " ORDER BY Product_name ASC";

                $res = mysqli_query($conn, $query);
                $found = false;

                if ($res && mysqli_num_rows($res) > 0) {
                    echo "<h2 style='text-align:center; margin-top:30px; color:#333;'>$label (" . mysqli_num_rows($res) . " results)</h2>";

                    while ($row = mysqli_fetch_assoc($res)) {
                        $found = true;
                        echo '
                        <div class="bik">
                          <a href="prdctinfo.php?product=' . urlencode($row["Product_name"]) . '&category=' . $table . '">
                            <img src=" ../' . htmlspecialchars($row["Product_pic"]) . '" alt="' . htmlspecialchars($row["Product_name"]) . '../">
                          </a>
                          <div class="description">
                            <strong>' . htmlspecialchars($row["Product_name"]) . '</strong><br>
                          </div>
                        </div>';
                    }
                }

                return $found;
            }

            $anyFound = false;

            foreach ($selected_categories as $cat) {
                if (isset($categoryMap[$cat])) {
                    $table = $categoryMap[$cat]['table'];
                    $label = $categoryMap[$cat]['label'];
                    if (displayResults($conn, $table, $label, $escaped_search, $selected_brands, $min_price, $max_price)) {
                        $anyFound = true;
                    }
                }
            }

            if (!$anyFound) {
                echo "<div style='text-align:center; margin-top:40px; padding:20px;'>";
                echo "<h2 style='color:#666; margin-bottom:10px;'>No products found</h2>";
                echo "<p style='color:#888;'>No products match your search criteria. Try:</p>";
                echo "<ul style='color:#888; text-align:left; display:inline-block;'>";
                echo "<li>Using different keywords</li>";
                echo "<li>Removing some filters</li>";
                echo "<li>Checking spelling</li>";
                echo "<li>Searching in all categories</li>";
                echo "</ul></div>";
            }

            echo '</div>';

            if (isset($_GET['debug'])) {
                echo "<div style='margin-top:20px; padding:10px; background:#f0f0f0; border:1px solid #ccc;'>";
                echo "<h3>Debug Information:</h3>";
                echo "<p><strong>Selected Categories:</strong> " . implode(', ', $selected_categories) . "</p>";
                echo "<p><strong>Selected Brands:</strong> " . implode(', ', $selected_brands) . "</p>";
                echo "<p><strong>Price Range:</strong> ";
                if ($min_price !== null) echo "Min: ₹$min_price ";
                if ($max_price !== null) echo "Max: ₹$max_price";
                if ($min_price === null && $max_price === null) echo "No price filter";
                echo "</p>";
                echo "<p><strong>GET Parameters:</strong> " . htmlspecialchars(http_build_query($_GET)) . "</p>";
                echo "</div>";
            }

        } else {
            echo "<div style='text-align:center; margin-top:40px; padding:20px;'>";
            echo "<h2 style='color:#666;'>No search query provided</h2>";
            echo "<p style='color:#888;'>Please enter a search term to find products.</p>";
            echo "</div>";
        }
        ?>
    </div>
</main>

<?php include("footer.html"); ?>

</body>
</html>
