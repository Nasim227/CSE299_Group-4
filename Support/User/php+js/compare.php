<?php
session_start();
include("connection.php");

function hasColumn($conn, $table, $column) {
    $query = "SHOW COLUMNS FROM `$table` LIKE '$column'";
    $result = mysqli_query($conn, $query);
    return mysqli_num_rows($result) > 0;
}


function getProductData($conn, $productName, $category = null) {
    $tables = $category ? [$category] : ['bike', 'scooter', 'engine_oil', 'helmet'];
    
    foreach ($tables as $table) {
        $escapedProduct = mysqli_real_escape_string($conn, $productName);
        $query = "SELECT * FROM $table WHERE Product_name = '$escapedProduct'";
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_assoc($result);
            $data['table_name'] = $table;
            return $data;
        }
    }
    return null;
}


$compareProducts = [];
$maxCompare = 3;

for ($i = 1; $i <= $maxCompare; $i++) {
    if (isset($_GET["product$i"]) && !empty($_GET["product$i"])) {
        $category = isset($_GET["category$i"]) ? $_GET["category$i"] : null;
        $productData = getProductData($conn, $_GET["product$i"], $category);
        if ($productData) {
            $compareProducts[] = $productData;
        }
    }
}


$searchResults = [];
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = htmlspecialchars(trim($_GET['search']));
    $escaped_search = mysqli_real_escape_string($conn, $search);
    
    $tables = ['bike', 'scooter', 'engine_oil', 'helmet'];
    foreach ($tables as $table) {
        
        $searchConditions = [
            "Product_name LIKE '%$escaped_search%'",
            "Company_name LIKE '%$escaped_search%'"
        ];
        
      
        if (hasColumn($conn, $table, 'Product_type')) {
            $searchConditions[] = "Product_type LIKE '%$escaped_search%'";
        }
        
      
        $searchLower = strtolower($escaped_search);
        if (($searchLower == 'bike' && $table == 'bike') ||
            ($searchLower == 'scooter' && $table == 'scooter') ||
            (($searchLower == 'oil' || $searchLower == 'engine oil' || $searchLower == 'engineoil') && $table == 'engine_oil') ||
            ($searchLower == 'helmet' && $table == 'helmet')) {
           
            $query = "SELECT Product_name, Company_name, Product_pic, Prize, '$table' as table_name FROM $table 
                      ORDER BY Product_name";
        } else {
          
            $whereClause = implode(' OR ', $searchConditions);
            $query = "SELECT Product_name, Company_name, Product_pic, Prize, '$table' as table_name FROM $table 
                      WHERE $whereClause 
                      ORDER BY Product_name";
        }
        
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $searchResults[] = $row;
            }
        }
    }
}


function isProductInComparison($productName, $compareProducts) {
    foreach ($compareProducts as $product) {
        if ($product['Product_name'] === $productName) {
            return true;
        }
    }
    return false;
}


function getNextAvailableSlot($maxCompare) {
    for ($i = 1; $i <= $maxCompare; $i++) {
        if (!isset($_GET["product$i"]) || empty($_GET["product$i"])) {
            return $i;
        }
    }
    return null; 
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="icon" type="image/png" href="../images/logo2.png">
    <title>Compare Products - Dream Ride</title>
    <link rel="stylesheet" href="../css/brands.css">
    <link rel="stylesheet" href="../css/compare.css">
</head>
<body>

<?php include("navbar.php"); ?>

<main>
    <div class="compare-container">
        
        <h1 class="compare-title">Compare Products</h1>
        <p class="compare-subtitle">Compare up to <?php echo $maxCompare; ?> products side by side</p>


        <div class="search-section">
            <h3>Add Products to Compare</h3>
            <form method="GET" class="search-form">
                <?php
              
                for ($i = 1; $i <= $maxCompare; $i++) {
                    if (isset($_GET["product$i"])) {
                        echo "<input type='hidden' name='product$i' value='" . htmlspecialchars($_GET["product$i"]) . "'>";
                        if (isset($_GET["category$i"])) {
                            echo "<input type='hidden' name='category$i' value='" . htmlspecialchars($_GET["category$i"]) . "'>";
                        }
                    }
                }
                ?>
                <input type="text" name="search" placeholder="Search for products (try: bike, scooter, oil, helmet)..." 
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" 
                       class="search-input">
                <button type="submit" class="search-button">Search</button>
            </form>

            <?php if (!empty($searchResults)): ?>
                <h4>Search Results:</h4>
                <div class="search-results-grid">
                    <?php foreach ($searchResults as $result): ?>
                        <?php 
                        $isAlreadyAdded = isProductInComparison($result['Product_name'], $compareProducts);
                        $nextSlot = getNextAvailableSlot($maxCompare);
                        ?>
                        <div class="search-result-card">
                            <img src="../<?php echo htmlspecialchars($result['Product_pic']); ?>" 
                                 alt="<?php echo htmlspecialchars($result['Product_name']); ?>"
                                 class="search-result-image">
                            <h4 class="search-result-title"><?php echo htmlspecialchars($result['Product_name']); ?></h4>
                            <p class="search-result-company"><?php echo htmlspecialchars($result['Company_name']); ?></p>
                            <p class="search-result-price"><strong>৳<?php echo number_format($result['Prize']); ?></strong></p>
                            <p class="search-result-category">
                                <?php echo ucfirst(str_replace('_', ' ', $result['table_name'])); ?>
                            </p>
                            
                            <?php if ($isAlreadyAdded): ?>
                                <button class="btn btn-disabled" disabled>
                                    Already Added
                                </button>
                            <?php elseif ($nextSlot === null): ?>
                                <button class="btn btn-danger" disabled>
                                    Maximum Reached (<?php echo $maxCompare; ?>/<?php echo $maxCompare; ?>)
                                </button>
                            <?php else: ?>
                                <a href="?<?php 
                                    $params = $_GET;
                                    $params["product$nextSlot"] = $result['Product_name'];
                                    $params["category$nextSlot"] = $result['table_name'];
                                    unset($params['search']);
                                    echo http_build_query($params);
                                ?>" class="btn btn-success">
                                    Add to Compare
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php elseif (isset($_GET['search'])): ?>
                <p class="no-results">No products found matching your search: "<strong><?php echo htmlspecialchars($_GET['search']); ?></strong>"</p>
            <?php endif; ?>
        </div>

      
        <?php if (!empty($compareProducts)): ?>
            <div class="comparison-table-container">
                <table class="comparison-table">
                    <colgroup>
                        <col class="spec-column">
                        <?php 
                        $productWidth = 80 / count($compareProducts);
                        foreach ($compareProducts as $product): 
                        ?>
                            <col style="width: <?php echo $productWidth; ?>%;">
                        <?php endforeach; ?>
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="spec-header">Specification</th>
                            <?php foreach ($compareProducts as $index => $product): ?>
                                <th class="product-header">
                                    <a href="?<?php 
                                        $params = $_GET;
                                        
                                        $slotToRemove = $index + 1;
                                        unset($params["product$slotToRemove"]);
                                        unset($params["category$slotToRemove"]);
                                        
                                        
                                        $remainingProducts = [];
                                        $remainingCategories = [];
                                        
                                        for ($j = 1; $j <= $maxCompare; $j++) {
                                            if ($j != $slotToRemove && isset($params["product$j"])) {
                                                $remainingProducts[] = $params["product$j"];
                                                $remainingCategories[] = $params["category$j"];
                                            }
                                            unset($params["product$j"]);
                                            unset($params["category$j"]);
                                        }
                                        
                                   
                                        foreach ($remainingProducts as $k => $prodName) {
                                            $params["product" . ($k + 1)] = $prodName;
                                            $params["category" . ($k + 1)] = $remainingCategories[$k];
                                        }
                                        
                                        echo http_build_query($params);
                                    ?>" class="remove-product">[×]</a>
                                    <img src="../<?php echo htmlspecialchars($product['Product_pic']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['Product_name']); ?>"
                                         class="product-image">
                                    <h4 class="product-name"><?php echo htmlspecialchars($product['Product_name']); ?></h4>
                                    <p class="product-company"><?php echo htmlspecialchars($product['Company_name']); ?></p>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="spec-cell">Price</td>
                            <?php foreach ($compareProducts as $product): ?>
                                <td class="data-cell">
                                    <?php if (isset($_SESSION["username"]) && ($product['table_name'] == 'bike' || $product['table_name'] == 'scooter')): ?>
                                        <del class="original-price">৳<?php echo number_format($product['Prize']); ?></del><br>
                                        <strong class="discounted-price">৳<?php echo number_format($product['Prize'] * 0.98); ?></strong>
                                        <br><small class="discount-text">(2% discount)</small>
                                    <?php else: ?>
                                        <strong class="price">৳<?php echo number_format($product['Prize']); ?></strong>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>

                        <?php if (!empty($compareProducts) && hasColumn($conn, $compareProducts[0]['table_name'], 'Product_type')): ?>
                        <tr>
                            <td class="spec-cell">Product Type</td>
                            <?php foreach ($compareProducts as $product): ?>
                                <td class="data-cell">
                                    <?php echo isset($product['Product_type']) ? htmlspecialchars($product['Product_type']) : 'N/A'; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endif; ?>

                        <?php if (!empty($compareProducts) && hasColumn($conn, $compareProducts[0]['table_name'], 'Engine')): ?>
                        <tr>
                            <td class="spec-cell">Engine</td>
                            <?php foreach ($compareProducts as $product): ?>
                                <td class="data-cell">
                                    <?php echo isset($product['Engine']) ? htmlspecialchars($product['Engine']) : 'N/A'; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endif; ?>

                        <?php if (!empty($compareProducts) && hasColumn($conn, $compareProducts[0]['table_name'], 'Mileage')): ?>
                        <tr>
                            <td class="spec-cell">Mileage</td>
                            <?php foreach ($compareProducts as $product): ?>
                                <td class="data-cell mileage-cell">
                                    <?php echo isset($product['Mileage']) ? htmlspecialchars($product['Mileage']) : 'N/A'; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endif; ?>

                        <?php if (!empty($compareProducts) && hasColumn($conn, $compareProducts[0]['table_name'], 'Release_date')): ?>
                        <tr>
                            <td class="spec-cell">Release Date</td>
                            <?php foreach ($compareProducts as $product): ?>
                                <td class="data-cell">
                                    <?php echo isset($product['Release_date']) ? date('M d, Y', strtotime($product['Release_date'])) : 'N/A'; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endif; ?>

                        <tr>
                            <td class="spec-cell">Available Quantity</td>
                            <?php foreach ($compareProducts as $product): ?>
                                <td class="data-cell">
                                    <span class="quantity <?php echo $product['Available_qnty'] > 10 ? 'in-stock' : 'low-stock'; ?>">
                                        <?php echo $product['Available_qnty']; ?> units
                                    </span>
                                </td>
                            <?php endforeach; ?>
                        </tr>

                        <tr>
                            <td class="spec-cell">Actions</td>
                            <?php foreach ($compareProducts as $product): ?>
                                <td class="data-cell">
                                    <a href="prdctinfo.php?product=<?php echo urlencode($product['Product_name']); ?>&category=<?php echo $product['table_name']; ?>" 
                                       class="btn btn-primary">
                                       View Details
                                    </a><br>
                                    <?php if (isset($_SESSION["username"])): ?>
                                        <a href="prdctinfo.php?product=<?php echo urlencode($product['Product_name']); ?>&category=<?php echo $product['table_name']; ?>" 
                                           class="btn btn-success">
                                           Book Now
                                        </a>
                                    <?php else: ?>
                                        <a href="login.php" 
                                           class="btn btn-success">
                                           Login to Book
                                        </a>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-products">
                <h3>No Products Selected</h3>
                <p>Search and add products above to start comparing</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include("footer.html"); ?>

</body>
</html>

