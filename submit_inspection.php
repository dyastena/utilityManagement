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
        
            if ($status === "Clean") {
                $fullComment .= "$area: OK; ";
            } elseif (isset($data['comments']) && is_array($data['comments'])) {
                $commentsArray = [];
                foreach ($data['comments'] as $comment) {
                    $trimmed = trim($comment);
                    if ($trimmed !== '') {
                        $commentsArray[] = htmlspecialchars($trimmed);
                    }
                }
                if (!empty($commentsArray)) {
                    $fullComment .= "$area: " . implode(", ", $commentsArray) . "; ";
                }
            }
            echo "<hr>";
        }
    } else {
        echo "No areas submitted.<br>";
    }

    // Check if an inspection already exists for the given location, date, and time
    $check_query = $conn->prepare("SELECT Inspection_Id FROM inspection WHERE Date = ? AND time = ? AND ass_id = ?");
    $check_query->bind_param("ssi", $date, $shift, $personnel_id);
    $check_query->execute();
    $check_query->bind_result($inspection_id);
    $check_query->fetch();
    $check_query->close();

    if ($inspection_id) {
        // If an inspection exists, update the area statuses and comments
        echo "<h2>Updating Existing Inspection</h2>";

        // Retrieve existing area statuses for the inspection
        $existingStatuses = [];
        $status_query = $conn->prepare("SELECT Area, status FROM area_status WHERE inspection_id = ?");
        $status_query->bind_param("i", $inspection_id);
        $status_query->execute();
        $status_result = $status_query->get_result();
        while ($row = $status_result->fetch_assoc()) {
            $existingStatuses[$row['Area']] = $row['status'];
        }
        $status_query->close();

        // Update only modified area statuses
        $update_area_query = $conn->prepare("UPDATE area_status SET status = ? WHERE inspection_id = ? AND Area = ?");
        $insert_area_query = $conn->prepare("INSERT INTO area_status (Area, status, inspection_id) VALUES (?, ?, ?)");
        foreach ($areas as $i => $data) {
            $area = htmlspecialchars($data['name'] ?? 'Unnamed');
            $status = htmlspecialchars($data['status'] ?? 'No status');

            if (isset($existingStatuses[$area])) {
                // If the area exists, check if the status has changed
                if ($existingStatuses[$area] !== $status) {
                    // Update the status only if it has changed
                    $update_area_query->bind_param("sis", $status, $inspection_id, $area);
                    $update_area_query->execute();
                }
            } else {
                // If the area does not exist, insert it
                $insert_area_query->bind_param("ssi", $area, $status, $inspection_id);
                $insert_area_query->execute();
            }
        }
        $update_area_query->close();
        $insert_area_query->close();

        // Update or insert comments
        if (!empty($fullComment)) {
            $fullComment = trim($fullComment);

            // Retrieve the existing comment string for this inspection
            $existingComment = "";
            $check_comment_query = $conn->prepare("SELECT Comments FROM comments WHERE inspection_id = ?");
            $check_comment_query->bind_param("i", $inspection_id);
            $check_comment_query->execute();
            $check_comment_query->bind_result($existingComment);
            $check_comment_query->fetch();
            $check_comment_query->close();

            // Parse the existing comment string into an associative array
            $existingCommentsArray = [];
            if (!empty($existingComment)) {
                $commentParts = explode("; ", $existingComment);
                foreach ($commentParts as $part) {
                    if (strpos($part, ":") !== false) {
                        list($area, $comments) = explode(": ", $part, 2);
                        $existingCommentsArray[trim($area)] = trim($comments);
                    }
                }
            }

            // Parse the new comments and update the existing comments array
            foreach ($areas as $i => $data) {
                $area = htmlspecialchars($data['name'] ?? 'Unnamed');
                if (isset($data['comments']) && is_array($data['comments'])) {
                    $commentsArray = [];
                    foreach ($data['comments'] as $comment) {
                        $trimmed = trim($comment);
                        if ($trimmed !== '') {
                            $commentsArray[] = htmlspecialchars($trimmed);
                        }
                    }
                    if (!empty($commentsArray)) {
                        // Update the comment for this area
                        $existingCommentsArray[$area] = implode(", ", $commentsArray);
                    }
                } elseif ($data['status'] === "Clean") {
                    // If the area is clean, set the comment to "OK"
                    $existingCommentsArray[$area] = "OK";
                }
            }

            // Reconstruct the updated comment string
            $updatedComment = "";
            foreach ($existingCommentsArray as $area => $comments) {
                $updatedComment .= "$area: $comments; ";
            }
            $updatedComment = trim($updatedComment);

            // Update the comment in the database
            $check_comment_query = $conn->prepare("SELECT comment_id FROM comments WHERE inspection_id = ?");
            $check_comment_query->bind_param("i", $inspection_id);
            $check_comment_query->execute();
            $check_comment_query->store_result();

            if ($check_comment_query->num_rows > 0) {
                // Update the existing comment
                $update_comment_query = $conn->prepare("UPDATE comments SET Comments = ? WHERE inspection_id = ?");
                $update_comment_query->bind_param("si", $updatedComment, $inspection_id);
                $update_comment_query->execute();
                $update_comment_query->close();
            } else {
                // Insert a new comment
                $insert_comment_query = $conn->prepare("INSERT INTO comments (Comments, inspection_id) VALUES (?, ?)");
                $insert_comment_query->bind_param("si", $updatedComment, $inspection_id);
                $insert_comment_query->execute();
                $insert_comment_query->close();
            }
            $check_comment_query->close();
        }
    } else {
        // If no inspection exists, insert a new one
        echo "<h2>Inserting New Inspection</h2>";

        // Insert into the inspection table
        $inspection_query = $conn->prepare("INSERT INTO inspection (Date, inspector_id, day, time, ass_id) VALUES (?, ?, ?, ?, ?)");
        $inspection_query->bind_param("sissi", $date, $user_id, $dayOfWeek, $shift, $personnel_id);
        $inspection_query->execute();
        $inspection_id = $conn->insert_id; // Get the last inserted inspection ID
        $inspection_query->close();

        // Insert area statuses
        $area_status_query = $conn->prepare("INSERT INTO area_status (Area, status, inspection_id) VALUES (?, ?, ?)");
        foreach ($areas as $i => $data) {
            $area = htmlspecialchars($data['name'] ?? 'Unnamed');
            $status = htmlspecialchars($data['status'] ?? 'No status');

            // Insert area status
            $area_status_query->bind_param("ssi", $area, $status, $inspection_id);
            $area_status_query->execute();
        }
        $area_status_query->close();

        // Insert the concatenated comment
        if (!empty($fullComment)) {
            $fullComment = trim($fullComment);
            $comment_query = $conn->prepare("INSERT INTO comments (Comments, inspection_id) VALUES (?, ?)");
            $comment_query->bind_param("si", $fullComment, $inspection_id);
            $comment_query->execute();
            $comment_query->close();
        }
    }

    // Confirmation message
    echo "<h2>Inspection Submitted</h2>";
    echo "Inspection submitted successfully with the following comment:<br>";
    echo "<strong>Comment:</strong> $fullComment<br>";
}
?>
