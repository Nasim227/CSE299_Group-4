<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("connection.php");
if (!$conn) {
    die("❌ Connection to database failed!");
}


if (!isset($_SESSION['email'])) {
    echo "<p style='text-align:center; color:darkred;'>Please login to see your recommendations.</p>";
    exit();
}

$email = $_SESSION['email']; 

$stmt = $conn->prepare("
    SELECT p.Product_name, p.Company_name, pr.Score
    FROM product_recommendations pr
    JOIN product p ON pr.Recommended_Product_id = p.Product_id
    WHERE pr.Email = ?
    ORDER BY pr.Score DESC
    LIMIT 5
");


$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p style='text-align:center;'>No recommendations available yet.</p>";
} else {
    echo '<link rel="stylesheet" href="../css/recommend.css">';
    echo "<h2 style='text-align:center; margin-top:40px;'>🔥 Recommended For You</h2>";
    echo '<div class="product-container" style="margin-bottom: 40px;">';

    while ($row = $result->fetch_assoc()) {
      $productName = $row["Product_name"];
        $escapedProduct = mysqli_real_escape_string($conn, $productName);

     $category = ''; 
$tables = ['bike', 'scooter', 'engine_oil', 'helmet'];
foreach ($tables as $table) {
    $q = mysqli_query($conn, "SELECT Product_name FROM $table WHERE Product_name = '$escapedProduct' LIMIT 1");
    if (mysqli_num_rows($q) > 0) {
        $category = $table;
        break;
    }
}

if (!$category) $category = 'bike'; // fallback if still empty
$productPic = ''; // fallback image path

if ($category) {
    $imgQuery = $conn->prepare("SELECT Product_pic FROM $category WHERE Product_name = ?");
    $imgQuery->bind_param("s", $productName);
    $imgQuery->execute();
    $imgResult = $imgQuery->get_result();
    if ($imgResult->num_rows > 0) {
        $imgRow = $imgResult->fetch_assoc();
        $productPic = htmlspecialchars($imgRow['Product_pic']);
    }
}
  


        echo '
        <div class="bik">
            <a href="prdctinfo.php?product=' . urlencode($productName) . '&category=' . $category . '">
            <img src="../' . $productPic . '" alt="' . htmlspecialchars($productName) . '" class="rec-img">
            </a>
            <div class="description">' . htmlspecialchars($productName) . '<br><small>(' . htmlspecialchars($row["Company_name"]) . ')</small></div>
        </div>';
    }

    echo '</div>';
}
?>
