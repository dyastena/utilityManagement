<?php
session_start();
require_once 'db.php';

// Redirect if not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: index.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION["user_id"];
    $location = $_POST["location"] ?? 'Unknown';

    // Get personnel ID
    $personnel_id = 'Unknown';
    $person_query = $conn->prepare("SELECT Personel_Id FROM Assignment WHERE Area_Assignment = ?");
    $person_query->bind_param("s", $location);
    $person_query->execute();
    $person_result = $person_query->get_result();
    if ($person_row = $person_result->fetch_assoc()) {
        $personnel_id = $person_row['Personel_Id'];
    }

    $date = $_POST["date"] ?? 'N/A';
    $shift = $_POST["shift"] ?? 'N/A';
    $areas = $_POST["areas"] ?? [];

    // Get day of week
    $dayOfWeek = date("l", strtotime($date));
    
    // Display preview of inspection
    echo "<h2>Inspection Preview</h2>";
    echo "<strong>User ID:</strong> " . htmlspecialchars($user_id) . "<br>";
    echo "<strong>Floor:</strong> " . htmlspecialchars($location) . "<br>";
    echo "<strong>Personnel ID:</strong> " . htmlspecialchars($personnel_id) . "<br>";
    echo "<strong>Date:</strong> " . htmlspecialchars($date) . "<br>";
    echo "<strong>Shift:</strong> " . htmlspecialchars($shift) . "<br>";
    echo "<strong>Day:</strong> " . htmlspecialchars($dayOfWeek) . "<br>";
    echo "<hr>";

    // Prepare the concatenated comment string
    $fullComment = "";

    // Preview areas and comments
    if (!empty($areas)) {
        foreach ($areas as $i => $data) {
            $area = htmlspecialchars($data['name'] ?? 'Unnamed');
            $status = htmlspecialchars($data['status'] ?? 'No status');

            echo "<strong>Area:</strong> $area<br>";
            echo "<strong>Status:</strong> $status<br>";

            // Check if the area is clean and append one "OK" comment if it is
            if ($status === "Clean") {
                // Only add "OK" once for a clean area
                $fullComment = " OK; ";
            } elseif (isset($data['comments']) && is_array($data['comments'])) {
                // For non-clean areas, append the individual comments
                foreach ($data['comments'] as $comment) {
                    $fullComment .= "$area: " . htmlspecialchars($comment) . "; ";
                }
            }
            echo "<hr>";
        }
    } else {
        echo "No areas submitted.<br>";
    }

    // Insert into the inspection table
    $inspection_query = $conn->prepare("INSERT INTO inspection (Date, inspector_id, day, time, ass_id) VALUES (?, ?, ?, ?, ?)");
    $inspection_query->bind_param("sissi", $date, $user_id, $dayOfWeek, $shift, $personnel_id);
    $inspection_query->execute();
    $inspection_id = $conn->insert_id; // Get the last inserted inspection ID

    // Prepare area status query
    $area_status_query = $conn->prepare("INSERT INTO area_status (Area, status, inspection_id) VALUES (?, ?, ?)");

    foreach ($areas as $i => $data) {
        $area = htmlspecialchars($data['name'] ?? 'Unnamed');
        $status = htmlspecialchars($data['status'] ?? 'No status');

        // Insert area status
        $area_status_query->bind_param("ssi", $area, $status, $inspection_id);
        $area_status_query->execute();
    }

    // Insert the concatenated comment
    if (!empty($fullComment)) {
        $comment_query = $conn->prepare("INSERT INTO comments (Comments, inspection_id) VALUES (?, ?)");
        $comment_query->bind_param("si", $fullComment, $inspection_id);
        $comment_query->execute();
    }

    // Confirmation message after insertion
    echo "<h2>Inspection Submitted</h2>";
    echo "Inspection submitted successfully with the following comment:<br>";
    echo "<strong>Comment:</strong> $fullComment<br>";
}
?>
