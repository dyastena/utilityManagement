<?php
require_once 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$otp = $data['otp'];

// Check if the OTP exists in the database
$stmt = $conn->prepare("SELECT id FROM users WHERE otp = ?");
$stmt->bind_param("i", $otp);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Invalid OTP."]);
}

$stmt->close();
$conn->close();
?>