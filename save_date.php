<?php
session_start();

// Get the JSON data from the request
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data["selectedDate"])) {
    $_SESSION["selectedDate"] = $data["selectedDate"];
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "No date provided"]);
}
?>