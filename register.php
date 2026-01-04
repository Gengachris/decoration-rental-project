<?php
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $personal_id = $_POST['personal_id'];
    $phone = $_POST['phone'];
    $workplace = $_POST['workplace'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO clients (personal_id, full_name, phone, workplace, email, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $personal_id, $name, $phone, $workplace, $email, $password);
    $stmt->execute();
    $stmt->close();

    header("Location: login.html");
}
?>