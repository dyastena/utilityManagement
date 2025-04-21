<?php
session_start();
require_once 'db.php';

header("Content-Type: application/json"); // Set the response type to JSON

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Find user
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $id;
            echo json_encode(["status" => "success", "redirect" => "dashboard.php"]);
            exit;
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid password."]);
            exit;
        }
    } else {
        echo json_encode(["status" => "error", "message" => "No user found with that email."]);
        exit;
    }

    $stmt->close();
    $conn->close();
}
?>
