<?php
    session_start();

    include("connection.php");

    $message = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $Username = filter_input(INPUT_POST , "Username" , FILTER_SANITIZE_SPECIAL_CHARS);
        $Email = filter_input(INPUT_POST , "Email" , FILTER_SANITIZE_EMAIL);
        $Contact_No = filter_input(INPUT_POST , "Contact_NO" , FILTER_SANITIZE_SPECIAL_CHARS);
        $Password = filter_input(INPUT_POST , "Password" , FILTER_SANITIZE_SPECIAL_CHARS);

        $hash = password_hash($Password , PASSWORD_DEFAULT);

        $check_sql = "SELECT * FROM signup WHERE Email = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("s", $Email);
        $stmt->execute();
        $result = $stmt->get_result();

        if (mysqli_num_rows($result) > 0) {
            $message = "You are already registered.<br> Redirecting to Login page...";
            header("refresh:3;url=login.php");
        }
        else {
            $sql = "INSERT INTO signup(Name , Email , Contact_no , Password)
                    VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $Username, $Email, $Contact_No, $hash); 
            if ($stmt->execute()) {
                header("Location: login.php");
                exit();
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        }

    }

    mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
   <head>
       <link rel="icon" type="image/png" href="images/logo2.png">
      <title>Dream Ride</title> 
      <link rel="stylesheet" href="sgnup.css">
      <link rel="stylesheet" href="footer.css">
      <script src="https://kit.fontawesome.com/7139f829c6.js" crossorigin="anonymous"></script>
   </head>

    <body>
        <?php include("navbar.php"); ?>

       <main>
        <div class="frm">

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">

                <h2 class="hd">Sign Up</h2>
    
                <label for="Username" class="fnt">Username :</label>
                <input type="text" id="Username" name="Username" required placeholder="Name" class="int"><br>
    
                <label for="Email" class="fnt">Email :</label>
                <input type="email" id="Email" name="Email" required placeholder="xyz@mail.com" class="int"><br>
    
                <label for="Contact_NO" class="fnt">Contact_NO :</label>
                <input type="text" id="Contact_NO" name="Contact_NO" required minlength="11" maxlength="11" placeholder="01*********" class="int"><br>
    
                <label for="Password" class="fnt">Password :</label>
                <input type="password" id="Password" name="Password" required minlength="7" class="int"><br>
    
                <input type="Reset" class="btn">
                <input type="submit" class="btn">
    
            </form>

            <?php if (!empty($message)): ?>
                <div class="hf"><?php echo $message; ?></div>
            <?php endif; ?>

        </div>
    
       </main>

   </body>
</html>
