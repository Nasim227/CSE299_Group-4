<?php
session_start();
include("connection.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo "❌ Only POST method allowed.";
    exit();
}

if (!isset($_SESSION['email']) || empty($_POST['product_id']) || empty($_POST['interaction_type'])) {
    http_response_code(400);
    echo "❌ Missing required data.";
    exit();
}

$email = $_SESSION['email'];
$product_id = $_POST['product_id'];
$interaction_type = $_POST['interaction_type'];
$timestamp = date("Y-m-d H:i:s");

$stmt = $conn->prepare("INSERT INTO user_product_interaction (Email, Product_id, Interaction_type, Interaction_time) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $email, $product_id, $interaction_type, $timestamp);

if ($stmt->execute()) {
    echo "✅ Interaction logged.";
} else {
    echo "❌ Failed to log interaction: " . htmlspecialchars($stmt->error);
}
$stmt->close();
$command = escapeshellcmd("python recommendations.py");
$output = shell_exec($command);
file_put_contents("reco_log.txt", $output . "\n", FILE_APPEND);

?>
