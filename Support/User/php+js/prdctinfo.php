<?php
    session_start();

    include("connection.php");
?>

<!DOCTYPE html>
 <html>
 <head>
       <link rel="icon" type="image/png" href="../Images/logo2.png">
      <title>Dream Ride</title> 
      <link rel = "stylesheet" href="../css/prinfo.css">
   </head>

    <body>
        <?php include("navbar.php"); ?>

       <main >
<?php
if (isset($_GET['product'])) {
    $product = mysqli_real_escape_string($conn, $_GET['product']);
    $category = isset($_GET['category']) ? $_GET['category'] : null;

    $tables = $category ? [$category] : ['bike', 'scooter', 'engine_oil', 'helmet'];

    $found = false;
    $productData = null;
    $productTable = '';

    foreach ($tables as $table) {
        $query = "SELECT * FROM $table WHERE Product_name = '$product'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $productData = $row;
            $productTable = $table;
        
            echo '
            <div class="bik">
                <div class="product-image">
                    <img src="' . htmlspecialchars($row['Product_pic']) . '" alt="' . htmlspecialchars($row['Product_name']) . '">
                </div>
                <div class="description">
                    <h2>' . htmlspecialchars($row['Product_name']) . '</h2>';
        
            if ($table === 'bike' || $table === 'scooter') {
                echo "Release Date: " . htmlspecialchars($row['Release_date']) . "<br>";
                echo "Engine: " . htmlspecialchars($row['Engine']) . "<br>";
                echo "Mileage: " . htmlspecialchars($row['Mileage']) . "<br>";
                echo "Available Quantity: " . htmlspecialchars($row['Available_qnty']) . "<br>";

                if (isset($_SESSION["username"])) {
                    $discountPrice = round($row['Prize'] * 0.98, 2);
                    echo "<del>Price: ৳" . htmlspecialchars($row['Prize']) . "</del><br><br>";
                    echo "Discount Price: ৳" . $discountPrice . "<br>";
                } else {
                    echo "Price: ৳" . htmlspecialchars($row['Prize']) . "<br>";
                }
            } 
            elseif ($table === 'engine_oil' || $table === 'helmet') {
                echo "Available Quantity: " . htmlspecialchars($row['Available_qnty']) . "<br>";
                echo "Price: ৳" . htmlspecialchars($row['Prize']) . "<br>";
            }
        
            echo '
                </div>
                <div class="bk">';
        
            if (isset($_SESSION["username"])) {
                $name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Unknown';
                $email = isset($_SESSION['email']) ? $_SESSION['email'] : 'Unknown';
                $contact = isset($_SESSION['contact_no']) ? $_SESSION['contact_no'] : 'Unknown';

                echo '
                    <form method="POST">
                        <label for="quantity">Enter Quantity:</label>
                        <input type="number" name="quantity" min="1" max="' . $row['Available_qnty'] . '" required><br><br>
                        <input type="submit" name="book" value="Book Product">
                    </form>';
            } else {
                echo "<h3><strong>Please <a href='login.php'>Login</a> to book this product.</strong></h3>";
            }
        
            echo '
                </div>
            </div>';
        
            $found = true;
            break;
        }
        
        
    }

    if (!$found) {
        echo "<p>Product not found.</p>";
    }
} else {
    echo "<p>No product selected.</p>";
}
?>
<?php
if (isset($_POST['book']) && isset($productData) && isset($_SESSION["username"])) {
    $quantity = intval($_POST['quantity']);

    if ($quantity > 0 && $quantity <= $productData['Available_qnty']) {
        $name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Unknown';
        $email = isset($_SESSION['email']) ? $_SESSION['email'] : 'Unknown';
        $contact = isset($_SESSION['contact_no']) ? $_SESSION['contact_no'] : 'Unknown';

        $company = $productData['Company_name'];
        $productType = $productTable;
        $productName = $productData['Product_name'];
        $productId = $productData['Product_id'];
        $bookedDate = date("Y-m-d H:i:s");

        $stmt = $conn->prepare("INSERT INTO booked (Name, Email, Contact_no, Company_name, Product_type, Product_name, Product_id, Quantity, Booked_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssis", $name, $email, $contact, $company, $productType, $productName, $productId, $quantity, $bookedDate);

        if ($stmt->execute()) {
            echo "<p style='color:green;'><strong>Product booked successfully!</strong></p>";
        } else {
            echo "<p style='color:red;'>Booking failed: " . htmlspecialchars($stmt->error) . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p style='color:red;'>Invalid quantity.</p>";
    }
}
?>
</main>

<?php include("footer.html"); ?>

</body>
</html>
