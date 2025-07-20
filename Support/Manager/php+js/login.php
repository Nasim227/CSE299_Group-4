<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = strtolower(trim($_POST["Email"]));
    $password = $_POST["Password"];

    if ($email === "admin@dreamride.com" && $password === "0123456789") {
        $_SESSION["user_id"] = "admin";
        $_SESSION["username"] = "Admin";
        $_SESSION["email"] = $email;
        header("Location: index.php");
        exit();
    } else {
        $message = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html>
   <head>
       <link rel="icon" type="image/png" href="../image/logo2.png">
      <title>Dream Ride</title> 
      <link rel="stylesheet" href="../css/login.css">
      <script src="https://kit.fontawesome.com/7139f829c6.js" crossorigin="anonymous"></script>
   </head>
<body>
<main>
    <div class="frm">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <h2 class="hd">Login</h2>

            <label for="Email" class="fnt">Email :</label>
            <input type="email" id="Email" name="Email" required placeholder="xyz@mail.com" class="int"><br>

            <label for="Password" class="fnt">Password :</label>
            <input type="password" id="Password" name="Password" required minlength="7" class="int"><br>

            <input type="reset" class="btn">
            <input type="submit" class="btn">
        </form>

        <?php if (!empty($message)): ?>
            <div class="hn"><?php echo $message; ?></div>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
