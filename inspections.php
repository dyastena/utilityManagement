<?php
session_start();

// Redirect to login page if the user is not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: index.html");
    exit;
}

$selectedDate = $_SESSION['selectedDate'] ?? date('Y-m-d');

require_once 'db.php';

// Fetch user details from the database
$stmt = $conn->prepare("SELECT first_name, last_name, email, image FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $email, $image);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Inspections - NeatWare</title>
  <link rel="stylesheet" href="inspections.css" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
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
            <a href="dashboard.php">
                <i class="fas fa-tachometer-alt nav-icon"></i>
                <span class="nav-text">Dashboard</span>
            </a>
            <a href="inspections.php" class="active">
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

    <!-- Main Content -->
    <main class="main">
        <header class="header">
            <h1>Room Inspections</h1>
            <p>Log the cleanliness status of assigned areas.</p>
        </header>

        <section class="inspection-form">
            <form action="submit_inspection.php" method="POST">
                <div class="form-grouped">
                    <div class="form-group">
                        <label for="location">Location</label>
                        <select id="location" name="location" required onchange="updateAreas()">
                            <option value="1st Floor">1st Floor</option>
                            <option value="2nd Floor">2nd Floor</option>
                            <option value="3rd Floor">3rd Floor</option>
                            <option value="4th Floor">4th Floor</option>
                            <option value="5th Floor">5th Floor</option>
                            <option value="6th Floor">6th Floor</option>
                            <option value="7th Floor">7th Floor</option>
                            <option value="8th Floor">8th Floor</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($selectedDate); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="shift">Shift</label>
                        <select id="shift" name="shift" required>
                                        <?php
                            // Determine the current time and set the appropriate shift
                            $currentHour = date('H');
                            $selectedAM = ($currentHour < 12) ? 'selected' : '';
                            $selectedPM = ($currentHour >= 12) ? 'selected' : '';
                        ?>
                        <!-- Store the time value (09:00 or 14:00) but display AM | PM -->
                        <option value="09:00" <?php echo $selectedAM; ?>>AM | 9:00</option>
                        <option value="14:00" <?php echo $selectedPM; ?>>PM | 2:00</option>
                        </select>
                    </div>
                </div>

                <div id="areas-container" >
                <!-- Areas to clean will be dynamically inserted here -->
                </div>

                <div class="comment-container">
                    <!-- Comment boxes will be dynamically added here -->
                </div>

                <button type="submit" class="submit-btn">Submit</button>
            </form>
        </section>
    </main>
  </div>

  <script src="script.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
        const datePicker = document.getElementById("date");
        const storedDate = sessionStorage.getItem("selectedDate");

        if (storedDate) {
            datePicker.value = storedDate;
        }
    });
  </script>
</body>
</html>