<?php
session_start();
require_once 'db.php';

header("Content-Type: application/json"); // Set the response type to JSON

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match."]);
        exit;
    }

    // Check if the email is already registered
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "This email is already registered."]);
        exit;
    }
    $stmt->close();

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Handle image upload
    $image_path = null;

    // Check if an image was uploaded
    if (!isset($_FILES["image"]) || $_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
        echo json_encode(["status" => "error", "message" => "Image is required. Please upload a valid image."]);
        exit;
    }

    // Validate the uploads directory
    $image_dir = "uploads/";
    if (!is_dir($image_dir)) {
        mkdir($image_dir, 0777, true); // Create the directory if it doesn't exist
    }

    // Validate file type
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_type = mime_content_type($_FILES["image"]["tmp_name"]);
    if (!in_array($file_type, $allowed_types)) {
        echo json_encode(["status" => "error", "message" => "Invalid file type. Only JPG, PNG, and GIF are allowed."]);
        exit;
    }

    // Restrict file size
    $max_file_size = 2 * 1024 * 1024; // 2 MB
    if ($_FILES["image"]["size"] > $max_file_size) {
        echo json_encode(["status" => "error", "message" => "File size exceeds the 2MB limit."]);
        exit;
    }

    // Sanitize file name
    $image_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
    $image_name = uniqid() . "." . $image_extension;
    $image_path = $image_dir . $image_name;

    // Move the uploaded file to the uploads directory
    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)) {
        echo json_encode(["status" => "error", "message" => "Failed to upload image."]);
        exit;
    }

    // Insert user data into the database
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $hashed_password, $image_path);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "redirect" => "index.html"]);
        exit;
    } else {
        echo json_encode(["status" => "error", "message" => "Registration failed. Please try again."]);
        exit;
    }

    $stmt->close();
    $conn->close();
}
?>
