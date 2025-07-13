<?php
    session_start();
    include("connection.php");
    
    if (!isset($_SESSION["username"])) {
        header("Location: login.php");
        exit();
    }

    if (!isset($_SESSION['login_time'])) {
        $_SESSION['login_time'] = date('Y-m-d H:i:s');
    }
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="icon" type="image/png" href="../images/logo2.png">
    <title>My Current Cart - Dream Ride</title>
    <link rel="stylesheet" href="../css/prinfo.css">
    <link rel = "stylesheet" href="../css/cart.css">

</head>

<body>
    <?php include("navbar.php"); ?>

    <main>
        <div class="cart-container">
            <div class="cart-header">
                <h1>My Current Session Cart</h1>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['name'] ?? $_SESSION['username']); ?>!</p>
            </div>
            
            <div class="session-info">
                <p>Showing products booked during this login session</p>
                <a href="booking_history.php" class="view-history-btn">View Full Booking History</a>
            </div>

            <?php
            if (isset($_POST['cancel_booking'])) {
                $user_email = $_SESSION['email'];
                $product_id = $_POST['product_id'];
                $product_type = $_POST['product_type'];
                $booked_date = $_POST['booked_date'];
                $quantity = $_POST['quantity'];
                
                $cancel_query = "DELETE FROM booked WHERE Email = ? AND Product_id = ? AND Product_type = ? AND Booked_date = ? AND Quantity = ? LIMIT 1";
                $stmt = $conn->prepare($cancel_query);
                $stmt->bind_param("ssssi", $user_email, $product_id, $product_type, $booked_date, $quantity);
                
                if ($stmt->execute() && $stmt->affected_rows > 0) {
                    echo '<div class="success-message">Booking cancelled successfully!</div>';
                } else {
                    echo '<div class="error-message">Failed to cancel booking. Please try again.</div>';
                }
                $stmt->close();
            }

            $user_email = $_SESSION['email'];
            $login_time = $_SESSION['login_time'];
            
            $query = "SELECT * FROM booked WHERE Email = ? AND (
                        (CHAR_LENGTH(Booked_date) = 10 AND Booked_date >= DATE(?)) OR 
                        (CHAR_LENGTH(Booked_date) > 10 AND Booked_date >= ?)
                      ) ORDER BY Booked_date DESC";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sss", $user_email, $login_time, $login_time);
            $stmt->execute();
            $result = $stmt->get_result();

            if (mysqli_num_rows($result) > 0) {
                $total_items = 0;
                $total_bookings = mysqli_num_rows($result);
                $booking_counter = 0;
                
                while ($booking = mysqli_fetch_assoc($result)) {
                    $booking_counter++;
                    $total_items += $booking['Quantity'];
                    
                    $product_table = $booking['Product_type'];
                    $product_id = $booking['Product_id'];
                    
                    $product_query = "SELECT * FROM $product_table WHERE Product_id = ?";
                    $product_stmt = $conn->prepare($product_query);
                    $product_stmt->bind_param("s", $product_id);
                    $product_stmt->execute();
                    $product_result = $product_stmt->get_result();
                    
                    if ($product_data = mysqli_fetch_assoc($product_result)) {
                        echo '<div class="cart-item">';
                        
                        echo '<img src="../' . htmlspecialchars($product_data['Product_pic']) . '" alt="' . htmlspecialchars($product_data['Product_name']) . '" class="item-image">';
                        
                        echo '<div class="item-details">';
                        echo '<h3>' . htmlspecialchars($booking['Product_name']) . '</h3>';
                        echo '<p><strong>Company:</strong> ' . htmlspecialchars($booking['Company_name']) . '</p>';
                        echo '<p><strong>Product Type:</strong> ' . ucfirst(htmlspecialchars($booking['Product_type'])) . '</p>';
                        echo '<p><strong>Quantity Booked:</strong> ' . htmlspecialchars($booking['Quantity']) . '</p>';
                        echo '<p><strong>Booked Date:</strong> ' . htmlspecialchars($booking['Booked_date']) . '</p>';
                        
                        if ($product_table === 'bike' || $product_table === 'scooter') {
                            $discountPrice = round($product_data['Prize'] * 0.98, 2);
                            $totalPrice = $discountPrice * $booking['Quantity'];
                            echo '<p><strong>Unit Price:</strong> ৳' . number_format($discountPrice, 2) . ' (2% discount applied)</p>';
                            echo '<p><strong>Total Price:</strong> ৳' . number_format($totalPrice, 2) . '</p>';
                        } else {
                            $totalPrice = $product_data['Prize'] * $booking['Quantity'];
                            echo '<p><strong>Unit Price:</strong> ৳' . number_format($product_data['Prize'], 2) . '</p>';
                            echo '<p><strong>Total Price:</strong> ৳' . number_format($totalPrice, 2) . '</p>';
                        }
                        
                        echo '</div>';
                        
                        echo '<div class="item-actions">';
                        echo '<form method="POST" onsubmit="return confirm(\'Are you sure you want to cancel this booking?\')">';
                        echo '<input type="hidden" name="product_id" value="' . htmlspecialchars($booking['Product_id']) . '">';
                        echo '<input type="hidden" name="product_type" value="' . htmlspecialchars($booking['Product_type']) . '">';
                        echo '<input type="hidden" name="booked_date" value="' . htmlspecialchars($booking['Booked_date']) . '">';
                        echo '<input type="hidden" name="quantity" value="' . htmlspecialchars($booking['Quantity']) . '">';
                        echo '<button type="submit" name="cancel_booking" class="cancel-btn">Cancel Booking</button>';
                        echo '</form>';
                        echo '</div>';
                        
                        echo '</div>';
                    }
                    $product_stmt->close();
                }
                
                echo '<div class="cart-summary">';
                echo '<h3>Current Session Summary</h3>';
                echo '<p><strong>Bookings This Session:</strong> ' . $total_bookings . '</p>';
                echo '<p><strong>Total Items This Session:</strong> ' . $total_items . '</p>';
                echo '<p><em>Note: These are items booked during your current login session.</em></p>';
                echo '</div>';
                
            } else {
                echo '<div class="empty-cart">';
                echo '<h2>No items in current session</h2>';
                echo '<p>You haven\'t booked any products during this login session.</p>';
                echo '<p><a href="index.php">Continue Shopping</a></p>';
                echo '</div>';
            }
            
            $stmt->close();
            ?>
        </div>
    </main>

    <?php include("footer.html"); ?>
</body>
</html>