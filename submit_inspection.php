<?php
session_start();
require_once 'db.php';

// Redirect to login page if the user is not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: index.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION["user_id"];
    $location = $_POST["location"] ?? 'Unknown';
    
    // Get the personnel ID based on the selected floor
    $personnel_id = 'Unknown';
    $person_query = $conn->prepare("SELECT Personel_Id FROM Assignment WHERE Area_Assignment = ?");
    $person_query->bind_param("s", $location);
    $person_query->execute();
    $person_result = $person_query->get_result();
    if ($person_row = $person_result->fetch_assoc()) {
        $personnel_id = $person_row['Personel_Id'];
    }

    $date = $_POST["date"] ?? 'N/A';
    $shift = $_POST["shift"] ?? 'N/A';q 
    $areas = $_POST["areas"] ?? [];

    // Get the day of the week based on the submitted date
    $dayOfWeek = date("l", strtotime($date));

    echo "<h2>Inspection Report</h2>";
    echo "<strong>User ID:</strong> " . htmlspecialchars($user_id) . "<br>";
    echo "<strong>Day:</strong> " . htmlspecialchars($dayOfWeek) . "<br>";
    echo "<strong>Floor:</strong> " . htmlspecialchars($location) . "<br>";
    echo "<strong>Personnel ID:</strong> " . htmlspecialchars($personnel_id) . "<br>";
    echo "<strong>Date:</strong> " . htmlspecialchars($date) . "<br>";
    echo "<strong>Shift:</strong> " . htmlspecialchars($shift) . "<hr>";

    if (!empty($areas)) {
        foreach ($areas as $i => $data) {
            $area = htmlspecialchars($data['name'] ?? 'Unnamed');
            $status = htmlspecialchars($data['status'] ?? 'No status');
            
            echo "<strong>Area:</strong> $area<br>";
            echo "<strong>Status:</strong> $status<br>";

            // Show "OK" if status is Clean, otherwise show comments
            if ($status === "Clean") {
                echo "<strong>Comment:</strong> OK<br>";
            } elseif (isset($data['comments']) && is_array($data['comments'])) {
                echo "<strong>Comments: </strong>";
                foreach ($data['comments'] as $comment) {
                    $cleanComment = htmlspecialchars($comment);
                    echo $cleanComment . "<br>";
                }

            }

            echo "<hr>"; // Add a separator for each area
        }
    } else {
        echo "No areas submitted.";
    }
} else {
    echo "Invalid form submission.";
}
?>
