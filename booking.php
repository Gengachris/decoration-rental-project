<?php
include 'includes/auth.php';
include 'includes/db.php';

$client_id = $_SESSION['client_id'];
$products = $_POST['products'];
$quantities = $_POST['quantities'];
$booking_date = $_POST['booking_date'];
$notes = isset($_POST['notes']) ? $_POST['notes'] : null;

for ($i = 0; $i < count($products); $i++) {
    $product_id = $products[$i];
    $quantity = $quantities[$i];

    if (!is_numeric($quantity) || $quantity <= 0) {
        continue;
    }

    $stmt = $conn->prepare("INSERT INTO bookings (client_id, product_id, quantity, booking_date, notes) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $client_id, $product_id, $quantity, $booking_date, $notes);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking Successful</title>
    <script>
        alert("✅ Booking successfully submitted!");
        window.location.href = "dashboard.html";
    </script>
</head>
<body>
</body>
</html>


