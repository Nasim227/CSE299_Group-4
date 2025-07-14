<?php
    session_start();
    include("connection.php");
    
    if (!isset($_SESSION["username"])) {
        header("Location: login.php");
        exit();
    }
    
    $message = "";
    $error = "";
    
    $username = $_SESSION["username"];
    $user_sql = "SELECT * FROM signup WHERE Name = ?";
    $stmt = $conn->prepare($user_sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user_result = $stmt->get_result();
    $user_data = $user_result->fetch_assoc();
    
    if (!$user_data) {
        $error = "User data not found.";
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
        $current_password = filter_input(INPUT_POST, "current_password", FILTER_SANITIZE_SPECIAL_CHARS);
        $new_password = filter_input(INPUT_POST, "new_password", FILTER_SANITIZE_SPECIAL_CHARS);
        $confirm_password = filter_input(INPUT_POST, "confirm_password", FILTER_SANITIZE_SPECIAL_CHARS);
        
        if (password_verify($current_password, $user_data['Password'])) {
            if ($new_password === $confirm_password) {
                if (strlen($new_password) >= 7) {
                    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $update_sql = "UPDATE signup SET Password = ? WHERE Name = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("ss", $new_hash, $username);
                    
                    if ($update_stmt->execute()) {
                        $message = "Password changed successfully!";
                    } else {
                        $error = "Error updating password.";
                    }
                } else {
                    $error = "New password must be at least 7 characters long.";
                }
            } else {
                $error = "New passwords do not match.";
            }
        } else {
            $error = "Current password is incorrect.";
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="icon" type="image/png" href="../images/logo2.png">
    <title>User Profile - Dream Ride</title>
    <link rel="stylesheet" href="../css/usr.css">
    <script src="https://kit.fontawesome.com/7139f829c6.js" crossorigin="anonymous"></script>
</head>
<body>
    <?php include("navbar.php"); ?>
    
    <main>
        <div class="profile-container">
            <div class="profile-header">
                <h1><i class="fa fa-user-circle"></i> User Profile</h1>
                <p>Welcome back, <?php echo $user_data ? htmlspecialchars($user_data['Name']) : 'User'; ?>!</p>
            </div>
            
            <?php if (!empty($message)): ?>
                <div class="success-message"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($user_data): ?>
                <div class="profile-section">
                    <h2><i class="fa fa-info-circle"></i> Personal Information</h2>
                    <div class="user-info">
                        <div class="info-item">
                            <strong>Full Name</strong>
                            <div><?php echo htmlspecialchars($user_data['Name']); ?></div>
                        </div>
                        <div class="info-item">
                            <strong>Email Address</strong>
                            <div><?php echo htmlspecialchars($user_data['Email']); ?></div>
                        </div>
                        <div class="info-item">
                            <strong>Contact Number</strong>
                            <div><?php echo htmlspecialchars($user_data['Contact_no']); ?></div>
                        </div>
                        <div class="info-item">
                            <strong>Account Status</strong>
                            <div class="status-active"><i class="fa fa-check-circle"></i> Active</div>
                        </div>
                    </div>
                </div>
                
                <div class="profile-section">
                    <h2><i class="fa fa-lock"></i> Change Password</h2>
                    <div class="password-form">
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="form-group">
                                <label for="current_password">Current Password:</label>
                                <input type="password" id="current_password" name="current_password" required>
                            </div>
                            <div class="form-group">
                                <label for="new_password">New Password:</label>
                                <input type="password" id="new_password" name="new_password" required minlength="7">
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password:</label>
                                <input type="password" id="confirm_password" name="confirm_password" required minlength="7">
                            </div>
                            <button type="submit" name="change_password" class="btn-primary">
                                <i class="fa fa-key"></i> Change Password
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="cart-section">
                    <h2><i class="fa fa-shopping-cart"></i> Purchase & Booking History</h2>
                    <p>View and manage your cart items.</p>
                    <a href="carthistory.php" class="btn-secondary">
                        <i class="fa fa-eye"></i> See Cart
                    </a>
                </div>
                
            <?php else: ?>
                <div class="error-state">
                    <i class="fa fa-exclamation-triangle"></i> Unable to load user data. Please try again later.
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

<?php mysqli_close($conn); ?>