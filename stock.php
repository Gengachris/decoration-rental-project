<?php
include 'includes/db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productIds = $_POST['products'] ?? [];
    $quantities = $_POST['quantities'] ?? [];
    $employeeName = trim($_POST['employee_name'] ?? '');
    $notes = $_POST['notes'] ?? null;

    if (empty($employeeName)) {
        die("Employee name is required.");
    }

    // Step 1: Check if employee exists
    $stmt1 = $conn->prepare("SELECT id FROM employees WHERE name = ?");
    $stmt1->bind_param("s", $employeeName);
    $stmt1->execute();
    $stmt1->store_result();

    if ($stmt1->num_rows > 0) {
        $stmt1->bind_result($employeeId);
        $stmt1->fetch();
        $stmt1->close();
    } else {
        $stmt1->close();
        // Insert new employee
        $stmt2 = $conn->prepare("INSERT INTO employees (name) VALUES (?)");
        $stmt2->bind_param("s", $employeeName);
        $stmt2->execute();
        $employeeId = $stmt2->insert_id;
        $stmt2->close();
    }

    // Step 2: Insert stock report
    if (!empty($productIds) && !empty($quantities) && count($productIds) === count($quantities)) {
        $stmt3 = $conn->prepare("INSERT INTO stock_reports (product_id, employee_id, quantity, notes, reported_at) VALUES (?, ?, ?, ?, NOW())");

        for ($i = 0; $i < count($productIds); $i++) {
            $productId = (int)$productIds[$i];
            $quantity = (int)$quantities[$i];

            $stmt3->bind_param("iiis", $productId, $employeeId, $quantity, $notes);
            $stmt3->execute();
        }

        $stmt3->close();
    }

    // Show alert then redirect using JavaScript
    echo "<script>
        alert('✅ Stock report submitted successfully!');
        window.location.href = 'stock_comparison.php';
    </script>";
    exit();
}
?>


