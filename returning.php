<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['client_id'])) {
    // Redirect to login if not logged in
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_id = $_SESSION['client_id'];
    $products = $_POST['products'];
    $quantities = $_POST['quantities'];
    $return_date = $_POST['return_date'];
    $notes = $_POST['notes'] ?? null;

    $stmt = $conn->prepare("INSERT INTO returns (client_id, product_id, quantity, return_date, notes, created_at) VALUES (?, ?, ?, ?, ?, NOW())");

    foreach ($products as $index => $product_id) {
        $quantity = $quantities[$index];
        $stmt->bind_param("iiiss", $client_id, $product_id, $quantity, $return_date, $notes);
        $stmt->execute();
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Return Success</title>
    <script>
        alert("✅ Return submitted successfully!");
        window.location.href = "dashboard.html";
    </script>
</head>
<body>
</body>
</html>

