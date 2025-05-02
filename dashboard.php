<?php
session_start();

// Redirect to login page if the user is not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: index.html");
    exit;
}

require_once 'db.php';

// Fetch user details from the database
$stmt = $conn->prepare("SELECT first_name, last_name, email, image FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $email, $image);
$stmt->fetch();
$stmt->close();

// Fetch floor status data from the database
$query = "SELECT 
    a.Area_Assignment AS floor,
    s.Area,
    s.status AS status,
    i.Date,
    i.time
FROM 
    area_status s
JOIN 
    inspection i ON s.inspection_id = i.Inspection_Id
JOIN 
    assignment a ON i.ass_id = a.Personel_Id
WHERE 
    i.Date = '2025-04-28'  -- Specify the date here, Changed based on the ui
    AND i.time = '09:00:00'  -- Specify the time here, Changed based on the ui
ORDER BY 
    a.Area_Assignment, s.Area, i.Date, i.time;
";
$result = $conn->query($query);
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
        <div class="date">
            <span id="today-date">21, April 2022</span>
            <img src="source/calendar.png" alt="Calendar Icon" />
            </div>
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
        <div class="time-select">AM | <span>9:00</span></div>
        </div>
        
        <table>
          <thead>
            <tr>
              <th>Floor</th>
              <th>Status</th>
              <th>Area</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr class="status-<?php echo strtolower(str_replace(' ', '', $row['status'])); ?>">
                  <td><?php echo htmlspecialchars($row['floor']); ?></td>
                  <td><?php echo htmlspecialchars($row['status']); ?></td>
                  <td><?php echo htmlspecialchars($row['Area']); ?></td>
                  <td><?php echo htmlspecialchars($row['Date']); ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="4">No data available</td>
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
