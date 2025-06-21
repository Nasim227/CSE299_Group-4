<?php
    session_start();

    include("connection.php");

    if (!isset($_SESSION['redirect_back']) && isset($_SERVER['HTTP_REFERER']) && !str_contains($_SERVER['HTTP_REFERER'], "signup.php") && !str_contains($_SERVER['HTTP_REFERER'], "login.php")) {
        $_SESSION['redirect_back'] = $_SERVER['HTTP_REFERER'];
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $Email = filter_input(INPUT_POST, "Email", FILTER_VALIDATE_EMAIL);
        $Password = $_POST["Password"];

        if ($Email && $Password) {
            $stmt = $conn->prepare("SELECT * FROM signup WHERE Email = ?");
            $stmt->bind_param("s", $Email);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_assoc();
                if (password_verify($Password, $user['Password'])) {
                    $_SESSION["user_id"] = $user["ID"];
                    $_SESSION["username"] = $user["Name"];
                    $_SESSION["name"] = $user["Name"];   
                    $_SESSION["email"] = $user["Email"];              
                    $_SESSION["contact_no"] = $user["Contact_no"];

                    if ($Email === "admin@dreamride.com") {
                        $_SESSION["role"] = "admin";
                    } else {
                        $_SESSION["role"] = "user";
                    }
    
                    $redirect = isset($_SESSION['redirect_back']) ? $_SESSION['redirect_back'] : 'index.php';
                    unset($_SESSION['redirect_back']);
                    header("Location: $redirect");
                    exit();
                }
                else {
                    $message = "Incorrect password!!";
                }
            } else {
                $message = "You are not registered.<br>Please register first.";
                header("refresh:3;url=signup.php");
            }
        } else {
            $message = "Please enter a valid email and password.";
        }
    }
    
    mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
   <head>
       <link rel="icon" type="image/png" href="images/logo2.png">
      <title>Dream Ride</title> 
      <link rel="stylesheet" href="lgn.css">
      <link rel="stylesheet" href="footer.css">
      <script src="https://kit.fontawesome.com/7139f829c6.js" crossorigin="anonymous"></script>
   </head>

    <body>
        <?php include("navbar.php"); ?>

       <main>
        <div class="frm">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <h2 class="hd">Login</h2>
    
                <label for="Email" class="fnt">Email :</label>
                <input type="email" id="Email" name="Email" required placeholder="xyz@mail.com" class="int"><br>

                <label for="Password" class="fnt">Password :</label>
                <input type="password" id="Password" name="Password" required minlength="7" class="int"><br>
    
                <input type="Reset" class="btn">
                <input type="submit" class="btn">

                <h4 class="dt">Don't have an account? Create now!!</h4>
                <a href="signup.php" class="sn">Signup</a>
            </form>

            <?php if (!empty($message)): ?>
                <div class="hn"><?php echo $message; ?></div>
            <?php endif; ?>

        </div>  
       </main>

   </body>
</html>
