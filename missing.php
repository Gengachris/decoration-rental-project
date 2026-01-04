<?php
session_start();
include 'includes/db.php';

// ✅ Error handling: check DB connection
if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}

// ✅ Only process POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("❌ Invalid request method.");
}

// ✅ Validate required POST data
if (empty($_POST['products']) || empty($_POST['quantities'])) {
    die("❌ Products and quantities are required.");
}

// ✅ Collect data
$productIds = $_POST['products'];
$quantities = $_POST['quantities'];
$notes = $_POST['notes'] ?? null;
$clientId = $_SESSION['client_id'] ?? null; // Optional: can be NULL

// ✅ Check if product and quantity arrays match
if (count($productIds) !== count($quantities)) {
    die("❌ Mismatched product and quantity entries.");
}

// ✅ Prepare SQL statement
$stmt = $conn->prepare("INSERT INTO missing_items (product_id, quantity, notes, client_id, reported_at) VALUES (?, ?, ?, ?, NOW())");

if (!$stmt) {
    die("❌ Failed to prepare statement: " . $conn->error);
}

// ✅ Loop and bind data
for ($i = 0; $i < count($productIds); $i++) {
    $productId = (int)$productIds[$i];
    $quantity = (int)$quantities[$i];

    $stmt->bind_param("iisi", $productId, $quantity, $notes, $clientId);

    if (!$stmt->execute()) {
        die("❌ Failed to execute query: " . $stmt->error);
    }
}

$stmt->close();
$conn->close();
?>

<!-- ✅ Success Alert and Redirect -->
<!DOCTYPE html>
<html>
<head>
    <title>Missing Report Submitted</title>
    <script>
        alert("✅ Missing product report submitted successfully!");
        window.location.href = "dashboard.html";
    </script>
</head>
<body></body>
</html>


