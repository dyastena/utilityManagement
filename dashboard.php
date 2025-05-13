<?php
session_start();
require_once 'db.php'; // Include your database connection file

// Get the selected date and shift from the form or set defaults
$selectedDate = $_POST['date'] ?? date('Y-m-d'); // Default to today's date
$selectedShift = $_POST['shift'] ?? '09:00'; // Default to AM shift

// Redirect to login page if the user is not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: index.html");
    exit;
}

// Fetch user details from the database
$stmt = $conn->prepare("SELECT first_name, last_name, email, image FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $email, $image);
$stmt->fetch();
$stmt->close();

// Prepare the SQL query
$query = "
    SELECT 
        a.Area_Assignment AS floor,
        GROUP_CONCAT(DISTINCT s.status ORDER BY FIELD(s.status, 'Needs attention', 'Needs cleaning', 'Clean')) AS statuses,
        GROUP_CONCAT(DISTINCT c.Comments SEPARATOR '; ') AS comments
    FROM 
        area_status s
    JOIN 
        inspection i ON s.inspection_id = i.Inspection_Id
    JOIN 
        assignment a ON i.ass_id = a.Personel_Id
    LEFT JOIN 
        comments c ON i.Inspection_Id = c.inspection_id
    WHERE 
        i.Date = ? AND i.time = ?
    GROUP BY 
        a.Area_Assignment
    ORDER BY 
        a.Area_Assignment;
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $selectedDate, $selectedShift);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - NeatWare</title>
  <link rel="stylesheet" href="dashboard.css" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
  <div class="container">
    <aside class="sidebar" data-collapsed="false">
        <div class="logo-container" onclick="toggleSidebar()">
            <img src="source/neatWareLogo.png" alt="NeatWare Logo" class="logo-img">
        </div>
        <div class="logo-text">NeatWare</div>
        <div class="profile">
            <div class="avatar">
                <img src="<?php echo htmlspecialchars($image ? $image : 'source/default-avatar.png'); ?>" alt="Profile Picture" class="profile-picture">
            </div>
            <p class="username"><?php echo htmlspecialchars($first_name . ' ' . $last_name); ?></p>
            <p class="email"><?php echo htmlspecialchars($email); ?></p>
        </div>
        <nav class="nav-links">
            <a href="dashboard.php" class="active">
                <i class="fas fa-tachometer-alt nav-icon"></i>
                <span class="nav-text">Dashboard</span>
            </a>
            <a href="inspections.php">
                <i class="fas fa-clipboard-list nav-icon"></i>
                <span class="nav-text">Inspections</span>
            </a>
            <a href="reports.php">
                <i class="fas fa-chart-bar nav-icon"></i>
                <span class="nav-text">Reports</span>
            </a>
            <a href="settings.php">
                <i class="fas fa-cogs nav-icon"></i>
                <span class="nav-text">Settings</span>
            </a>
        </nav>
        <form action="logout.php" method="POST">
            <button type="submit" class="logout-btn">Log Out</button>
        </form>
    </aside>

    <main class="main">
      <header class="header">
        <h1>Dashboard</h1>
        
      </header>
      
      <div class="contain">
        <p class = "pi">Track your progress here!</p>
      </div>
      

      <section class="welcome">
        <h2>Hello, <?php echo htmlspecialchars($first_name); ?></h2>
        <div class="cards">
          <div class="card green">
            <i class="fas fa-check-circle card-icon"></i>
            <p>Task completed today</p>
            <h3>4</h3>
          </div>
          <div class="card red">
            <i class="fas fa-exclamation-circle card-icon"></i>
            <p>Missed / Incomplete Task</p>
            <h3>2</h3>
          </div>
          <div class="card yellow">
            <i class="fas fa-exclamation-triangle card-icon"></i>
            <p>Issues</p>
            <h3>2</h3>
          </div>
          <div class="card blue">
            <i class="fas fa-user-friends card-icon"></i>
            <p>Personnel on Duty</p>
            <h3>7</h3>
          </div>
        </div>
      </section>

      <section class="table-section">
        <div class="contain">
        <h3>Floor Status Summary</h3>
        <form method="POST" action="dashboard.php">
            <div class="time-select">
                <label for="date-picker">Date:</label>
                <input type="date" id="date-picker" name="date" value="<?php echo htmlspecialchars($selectedDate); ?>" onchange="this.form.submit()" required>
                
                <label for="shift">Shift:</label>
                <select id="shift" name="shift" onchange="this.form.submit()" required>
                    <option value="09:00" <?php echo ($selectedShift === '09:00') ? 'selected' : ''; ?>>AM | 9:00</option>
                    <option value="14:00" <?php echo ($selectedShift === '14:00') ? 'selected' : ''; ?>>PM | 2:00</option>
                </select>
            </div>
        </form>
        </div>
        
        <table>
          <thead>
            <tr>
              <th>Floor</th>
              <th>Status</th>
              <th>Comments</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result->num_rows > 0): ?>

              <?php while ($row = $result->fetch_assoc()): ?>
                  <?php
                      // Process statuses
                      $statuses = $row['statuses'] ?? '';
                      $statusArray = explode(',', $statuses); // Split statuses into an array
                      $filteredStatuses = [];

                      // Filter out "clean" statuses and handle conflicting ones
                      foreach ($statusArray as $status) {
                          $trimmedStatus = trim($status);
                          if (strcasecmp($trimmedStatus, 'Clean') !== 0) {
                              $filteredStatuses[] = $trimmedStatus;
                          }
                      }

                      // Handle specific logic for "Needs cleaning" and "Needs attention"
                      if (in_array('Needs attention', $filteredStatuses)) {
                          $finalStatus = 'Needs attention'; // Prioritize "Needs attention"
                      } elseif (in_array('Needs cleaning', $filteredStatuses)) {
                          $finalStatus = 'Needs cleaning';
                      } else {
                          $finalStatus = 'Clean';
                      }

                      // Generate the class name based on final status (convert to lowercase and remove spaces)
                      $className = 'status' . strtolower(str_replace(' ', '', $finalStatus));
                  ?>

                  <tr class="<?php echo $className; ?>">
                      <td><?php echo htmlspecialchars($row['floor']); ?></td>
                      <td><?php echo htmlspecialchars($finalStatus); ?></td>
                      <td>
                          <?php
                              // Process and filter comments
                              $rawComments = $row['comments'] ?? '';
                              $filteredComments = [];

                              foreach (explode(';', $rawComments) as $comment) {
                                  $trimmed = trim($comment);
                                  if (!empty($trimmed) && stripos($trimmed, ': OK') === false) {
                                      $filteredComments[] = htmlspecialchars($trimmed);
                                  }
                              }

                              // Display comments with line breaks
                              echo count($filteredComments) > 0 ? implode('<br>', $filteredComments) : ' -';
                          ?>
                      </td>
                  </tr>
              <?php endwhile; ?>




            <?php else: ?>
              <tr>
                <td colspan="3">No data available for the selected date and shift.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>

  <script src="script.js"></script>
</body>
</html>
