<?php
include 'includes/db.php';

$query = "
SELECT 
    p.name AS product_name,
    e1.name AS current_employee,
    sr1.quantity AS current_quantity,
    e2.name AS previous_employee, 
    sr2.quantity AS previous_quantity,
    (sr1.quantity - sr2.quantity) AS difference,
    sr1.reported_at AS current_report_time
FROM stock_reports sr1
JOIN (
    SELECT product_id, MAX(reported_at) AS max_date
    FROM stock_reports
    GROUP BY product_id
) latest ON sr1.product_id = latest.product_id AND sr1.reported_at = latest.max_date
LEFT JOIN (
    SELECT product_id, MAX(reported_at) AS max_prev_date
    FROM stock_reports
    WHERE reported_at < (
        SELECT MAX(reported_at) FROM stock_reports
    )
    GROUP BY product_id
) prev_report ON sr1.product_id = prev_report.product_id
LEFT JOIN stock_reports sr2 ON sr2.product_id = sr1.product_id AND sr2.reported_at = prev_report.max_prev_date
JOIN products p ON sr1.product_id = p.id
JOIN employees e1 ON sr1.employee_id = e1.id
LEFT JOIN employees e2 ON sr2.employee_id = e2.id
ORDER BY p.name;
";

$result = $conn->query($query);

if (!$result) {
    die("❌ Query Failed: " . $conn->error);
}

// Alert message before table display
echo "
<!DOCTYPE html>
<html>
<head>
    <title>Stock Comparison Report</title>
    <script>
        alert('✅ Stock report comparison loaded successfully!');
    </script>
</head>
<body>
";

echo "<h2>📊 Stock Report Comparison</h2>";
echo "<table border='1' cellpadding='8' cellspacing='0'>";
echo "<tr style='background:#f2f2f2;'>
        <th>Product</th>
        <th>Current Employee</th>
        <th>Current Qty</th>
        <th>Previous Employee</th>
        <th>Previous Qty</th>
        <th>Difference</th>
        <th>Reported At</th>
      </tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['product_name']}</td>
            <td>{$row['current_employee']}</td>
            <td>{$row['current_quantity']}</td>
            <td>" . ($row['previous_employee'] ?? '-') . "</td>
            <td>" . ($row['previous_quantity'] ?? '-') . "</td>
            <td>" . ($row['difference'] ?? '-') . "</td>
            <td>{$row['current_report_time']}</td>
          </tr>";
}

echo "</table>";
echo "</body></html>";
?>


