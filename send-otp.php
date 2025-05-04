<?php
require_once 'db.php';
header('Content-Type: application/json');

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? null;

// Validate email
if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    error_log("Invalid email: $email");
    echo json_encode(['success' => false, 'error' => 'Invalid email address.']);
    exit();
}

// Check if the email exists in the database
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Generate a random OTP
    $otp = rand(100000, 999999);

    // Save the OTP in the database (optional: with expiration time)
    $stmt = $conn->prepare("UPDATE users SET otp = ? WHERE email = ?");
    $stmt->bind_param("is", $otp, $email);
    $stmt->execute();

    // Send the OTP to the user's email
    $to = $email;
    $subject = "Your OTP Code";
    $message = "Your OTP code is: $otp";
    $headers = "From: no-reply@yourdomain.com\r\n";

    if (mail($to, $subject, $message, $headers)) {
        echo json_encode(["success" => true, 'message' => 'OTP sent successfully.']);
    } else {
        error_log("Failed to send email to $email");
        echo json_encode(["success" => false, "error" => "Failed to send OTP."]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Email not found."]);
}

$stmt->close();
$conn->close();
?>