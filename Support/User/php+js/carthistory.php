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
    <title>My Complete History - Dream Ride</title>
    <link rel="stylesheet" href="../css/prinfo.css">
    <link rel="stylesheet" href="../css/carthistory.css">
</head>
<body>
    <?php include("navbar.php"); ?>
    <main>
        <div class="cart-container">
            <div class="cart-header">
                <h1>My Complete Purchase & Booking History</h1>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['name'] ?? $_SESSION['username']); ?>!</p>
            </div>
            
            <div class="user-info">
                <p>Showing all your purchases and bookings</p>
            </div>

            <div class="filter-section">
                <p><strong>Filter by Time:</strong></p>
                <a href="?filter=all&tab=<?php echo $_GET['tab'] ?? 'all'; ?>" class="filter-btn <?php echo (!isset($_GET['filter']) || $_GET['filter'] == 'all') ? 'active' : ''; ?>">All Time</a>
                <a href="?filter=today&tab=<?php echo $_GET['tab'] ?? 'all'; ?>" class="filter-btn <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'today') ? 'active' : ''; ?>">Today</a>
                <a href="?filter=week&tab=<?php echo $_GET['tab'] ?? 'all'; ?>" class="filter-btn <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'week') ? 'active' : ''; ?>">This Week</a>
                <a href="?filter=month&tab=<?php echo $_GET['tab'] ?? 'all'; ?>" class="filter-btn <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'month') ? 'active' : ''; ?>">This Month</a>
                <a href="?filter=session&tab=<?php echo $_GET['tab'] ?? 'all'; ?>" class="filter-btn <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'session') ? 'active' : ''; ?>">Current Session</a>
            </div>

            <div class="tab-section">
                <button class="tab-btn <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'all') ? 'active' : ''; ?>" onclick="showTab('all')">All Items</button>
                <button class="tab-btn <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'booked') ? 'active' : ''; ?>" onclick="showTab('booked')">Bookings Only</button>
                <button class="tab-btn <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'bought') ? 'active' : ''; ?>" onclick="showTab('bought')">Purchases Only</button>
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
            $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
            $tab = isset($_GET['tab']) ? $_GET['tab'] : 'all';
            
            function getDateFilter($filter, $login_time) {
                switch($filter) {
                    case 'today':
                        return " AND DATE(Booked_date) = CURDATE()";
                    case 'week':
                        return " AND Booked_date >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
                    case 'month':
                        return " AND Booked_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
                    case 'session':
                        return " AND (
                            (CHAR_LENGTH(Booked_date) = 10 AND Booked_date >= DATE('$login_time')) OR 
                            (CHAR_LENGTH(Booked_date) > 10 AND Booked_date >= '$login_time')
                        )";
                    default:
                        return "";
                }
            }
            
            function getSellDateFilter($filter, $login_time) {
                switch($filter) {
                    case 'today':
                        return " AND DATE(Sell_date) = CURDATE()";
                    case 'week':
                        return " AND Sell_date >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
                    case 'month':
                        return " AND Sell_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
                    case 'session':
                        return " AND (
                            (CHAR_LENGTH(Sell_date) = 10 AND Sell_date >= DATE('$login_time')) OR 
                            (CHAR_LENGTH(Sell_date) > 10 AND Sell_date >= '$login_time')
                        )";
                    default:
                        return "";
                }
            }
            
            $booked_items = [];
            $booked_stats = ['count' => 0, 'items' => 0, 'amount' => 0];
            
            if ($tab == 'all' || $tab == 'booked') {
                $booked_query = "SELECT *, 'booked' as item_type FROM booked WHERE Email = ?" . getDateFilter($filter, $login_time) . " ORDER BY Booked_date DESC";
                $stmt = $conn->prepare($booked_query);
                $stmt->bind_param("s", $user_email);
                $stmt->execute();
                $booked_result = $stmt->get_result();
                
                while ($booking = mysqli_fetch_assoc($booked_result)) {
                    $booked_items[] = $booking;
                    $booked_stats['count']++;
                    $booked_stats['items'] += $booking['Quantity'];
                    
                    $product_table = $booking['Product_type'];
                    $product_id = $booking['Product_id'];
                    
                    $product_query = "SELECT Prize FROM $product_table WHERE Product_id = ?";
                    $product_stmt = $conn->prepare($product_query);
                    $product_stmt->bind_param("s", $product_id);
                    $product_stmt->execute();
                    $product_result = $product_stmt->get_result();
                    
                    if ($product_data = mysqli_fetch_assoc($product_result)) {
                        if ($product_table === 'bike' || $product_table === 'scooter') {
                            $discountPrice = round($product_data['Prize'] * 0.98, 2);
                            $booked_stats['amount'] += $discountPrice * $booking['Quantity'];
                        } else {
                            $booked_stats['amount'] += $product_data['Prize'] * $booking['Quantity'];
                        }
                    }
                    $product_stmt->close();
                }
                $stmt->close();
            }
            
            $bought_items = [];
            $bought_stats = ['count' => 0, 'items' => 0, 'amount' => 0];
            
            if ($tab == 'all' || $tab == 'bought') {
                $bought_query = "SELECT *, 'bought' as item_type FROM sell WHERE Buyer_Email = ?" . getSellDateFilter($filter, $login_time) . " ORDER BY Sell_date DESC";
                $stmt = $conn->prepare($bought_query);
                $stmt->bind_param("s", $user_email);
                $stmt->execute();
                $bought_result = $stmt->get_result();
                
                while ($purchase = mysqli_fetch_assoc($bought_result)) {
                    $bought_items[] = $purchase;
                    $bought_stats['count']++;
                    $bought_stats['items'] += $purchase['Quantity'];
                    $bought_stats['amount'] += $purchase['Prize'];
                }
                $stmt->close();
            }
            
            $all_items = array_merge($booked_items, $bought_items);
            usort($all_items, function($a, $b) {
                $date_a = isset($a['Booked_date']) ? $a['Booked_date'] : $a['Sell_date'];
                $date_b = isset($b['Booked_date']) ? $b['Booked_date'] : $b['Sell_date'];
                return strtotime($date_b) - strtotime($date_a);
            });
            
            echo '<div class="stats-grid">';
            
            if ($tab == 'all' || $tab == 'booked') {
                echo '<div class="stat-card booked">';
                echo '<div class="stat-number">' . $booked_stats['count'] . '</div>';
                echo '<div class="stat-label">Total Bookings</div>';
                echo '</div>';
                echo '<div class="stat-card booked">';
                echo '<div class="stat-number">' . $booked_stats['items'] . '</div>';
                echo '<div class="stat-label">Booked Items</div>';
                echo '</div>';
                echo '<div class="stat-card booked">';
                echo '<div class="stat-number">৳' . number_format($booked_stats['amount'], 2) . '</div>';
                echo '<div class="stat-label">Booking Amount</div>';
                echo '</div>';
            }
            
            if ($tab == 'all' || $tab == 'bought') {
                echo '<div class="stat-card bought">';
                echo '<div class="stat-number bought">' . $bought_stats['count'] . '</div>';
                echo '<div class="stat-label">Total Purchases</div>';
                echo '</div>';
                echo '<div class="stat-card bought">';
                echo '<div class="stat-number bought">' . $bought_stats['items'] . '</div>';
                echo '<div class="stat-label">Purchased Items</div>';
                echo '</div>';
                echo '<div class="stat-card bought">';
                echo '<div class="stat-number bought">৳' . number_format($bought_stats['amount'], 2) . '</div>';
                echo '<div class="stat-label">Purchase Amount</div>';
                echo '</div>';
            }
            
            if ($tab == 'all') {
                $total_amount = $booked_stats['amount'] + $bought_stats['amount'];
                echo '<div class="stat-card">';
                echo '<div class="stat-number" style="color: #6f42c1;">৳' . number_format($total_amount, 2) . '</div>';
                echo '<div class="stat-label">Grand Total</div>';
                echo '</div>';
            }
            
            echo '</div>';
            
            if (count($all_items) > 0) {
                $current_date = '';
                
                foreach ($all_items as $item) {
                    $item_date = isset($item['Booked_date']) ? $item['Booked_date'] : $item['Sell_date'];
                    $display_date = date('Y-m-d', strtotime($item_date));
                    
                    if ($current_date !== $display_date) {
                        $current_date = $display_date;
                        echo '<div class="date-group">';
                        echo 'Items from ' . date('F j, Y', strtotime($display_date));
                        echo '</div>';
                    }
                    
                    $is_booked = $item['item_type'] == 'booked';
                    $product_table = $is_booked ? $item['Product_type'] : $item['Product_type'];
                    $product_id = $is_booked ? $item['Product_id'] : $item['Product_id'];
                    
                    $product_query = "SELECT * FROM $product_table WHERE Product_id = ?";
                    $product_stmt = $conn->prepare($product_query);
                    $product_stmt->bind_param("s", $product_id);
                    $product_stmt->execute();
                    $product_result = $product_stmt->get_result();
                    
                    if ($product_data = mysqli_fetch_assoc($product_result)) {
                        echo '<div class="cart-item ' . ($is_booked ? 'booked' : 'bought') . '">';
                        
                        echo '<img src="../' . htmlspecialchars($product_data['Product_pic']) . '" alt="' . htmlspecialchars($product_data['Product_name']) . '" class="item-image">';
                        
                        echo '<div class="item-details">';
                        echo '<h3>' . htmlspecialchars($is_booked ? $item['Product_name'] : $item['Product_name']) . ' ';
                        echo '<span class="status-badge status-' . ($is_booked ? 'booked' : 'bought') . '">' . ($is_booked ? 'Booked' : 'Purchased') . '</span></h3>';
                        echo '<p><strong>Company:</strong> ' . htmlspecialchars($is_booked ? $item['Company_name'] : $item['Company_name']) . '</p>';
                        echo '<p><strong>Product Type:</strong> ' . ucfirst(htmlspecialchars($item['Product_type'])) . '</p>';
                        echo '<p><strong>Quantity:</strong> ' . htmlspecialchars($item['Quantity']) . '</p>';
                        echo '<p><strong>' . ($is_booked ? 'Booked' : 'Purchase') . ' Date:</strong> ' . date('F j, Y g:i A', strtotime($item_date)) . '</p>';
                        
                        if ($is_booked) {
                            if ($product_table === 'bike' || $product_table === 'scooter') {
                                $discountPrice = round($product_data['Prize'] * 0.98, 2);
                                $totalPrice = $discountPrice * $item['Quantity'];
                                echo '<p><strong>Unit Price:</strong> ৳' . number_format($discountPrice, 2) . ' (2% discount applied)</p>';
                                echo '<p><strong>Total Price:</strong> ৳' . number_format($totalPrice, 2) . '</p>';
                            } else {
                                $totalPrice = $product_data['Prize'] * $item['Quantity'];
                                echo '<p><strong>Unit Price:</strong> ৳' . number_format($product_data['Prize'], 2) . '</p>';
                                echo '<p><strong>Total Price:</strong> ৳' . number_format($totalPrice, 2) . '</p>';
                            }
                        } else {
                            $unit_price = $item['Prize'] / $item['Quantity'];
                            echo '<p><strong>Unit Price:</strong> ৳' . number_format($unit_price, 2) . '</p>';
                            echo '<p><strong>Total Price:</strong> ৳' . number_format($item['Prize'], 2) . '</p>';
                        }
                        
                        echo '</div>';
                        
                        echo '<div class="item-actions">';
                        if ($is_booked) {
                            echo '<form method="POST" onsubmit="return confirm(\'Are you sure you want to cancel this booking?\')">';
                            echo '<input type="hidden" name="product_id" value="' . htmlspecialchars($item['Product_id']) . '">';
                            echo '<input type="hidden" name="product_type" value="' . htmlspecialchars($item['Product_type']) . '">';
                            echo '<input type="hidden" name="booked_date" value="' . htmlspecialchars($item['Booked_date']) . '">';
                            echo '<input type="hidden" name="quantity" value="' . htmlspecialchars($item['Quantity']) . '">';
                            echo '<button type="submit" name="cancel_booking" class="cancel-btn">Cancel Booking</button>';
                            echo '</form>';
                        } else {
                            echo '<div style="color: #28a745; font-weight: bold; text-align: center; padding: 10px;">✓ Purchased</div>';
                        }
                        echo '</div>';
                        
                        echo '</div>';
                    }
                    $product_stmt->close();
                }
                
            } else {
                echo '<div class="empty-cart">';
                echo '<h2>No items found</h2>';
                if ($filter == 'session') {
                    echo '<p>You haven\'t made any transactions during this login session.</p>';
                } else {
                    echo '<p>No ' . ($tab == 'booked' ? 'bookings' : ($tab == 'bought' ? 'purchases' : 'transactions')) . ' found for the selected period.</p>';
                }
                echo '<p><a href="index.php">Start Shopping</a></p>';
                echo '</div>';
            }
            ?>
        </div>
    </main>
    
    <script>
        function showTab(tabName) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('tab', tabName);
            window.location.search = urlParams.toString();
        }
    </script>
    
    <?php include("footer.html"); ?>
</body>
</html>