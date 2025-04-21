<?php
$host = "localhost";
$user = "root";      // your DB username
$pass = "";          // your DB password
$dbname = "neatware";

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
