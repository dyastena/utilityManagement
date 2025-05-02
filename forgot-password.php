<?php
require_once 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'];

// Check if the email exists in the database
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Generate a reset token
    $reset_token = bin2hex(random_bytes(16));
    $stmt = $conn->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
    $stmt->bind_param("ss", $reset_token, $email);
    $stmt->execute();

    // Send the reset link to the user's email
    $reset_link = "http://yourwebsite.com/reset-password.php?token=$reset_token";
    $to = $email;
    $subject = "Password Reset Request";
    $message = "Click the link below to reset your password:\n\n$reset_link";
    $headers = "From: no-reply@neatware.com";

    if (mail($to, $subject, $message, $headers)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to send email."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Email not found."]);
}

$stmt->close();
$conn->close();
?>