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

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - NeatWare</title>
  <link rel="stylesheet" href="reports.css" />
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
            <a href="dashboard.php">
                <i class="fas fa-tachometer-alt nav-icon"></i>
                <span class="nav-text">Dashboard</span>
            </a>
            <a href="inspections.php">
                <i class="fas fa-clipboard-list nav-icon"></i>
                <span class="nav-text">Inspections</span>
            </a>
            <a href="reports.php" class="active">
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
            <h1>Cleaning & Inspection Reports</h1>
            <p>Generate and review system logs and performance summaries</p>
        </header>


        <div class="card-container">
            
           <section class="card">    
           <h2 style="text-align: center;">Print Report This Month</h2>
           <div class="button-group">
             <button type="button" class="btn">Import to PDF</button>
           </div>
           </section>
      
           <section class="card">
           <h2 style="text-align: center;">Print Report This Week</h2>
           <div class="button-group">
             <button onclick="importToPdf()" class="btn">Import to PDF</button>
           </div>
           </section>
        </div>
    </main>
    
    <!-- Month Picker Modal -->
    <div id="monthModal" class="dialog">
      <div class="dialog-content">
        <h3>Select Month and Year</h3>
        <select id="monthSelect" class="modal-select">
          <option value="01">January</option>
          <option value="02">February</option>
          <option value="03">March</option>
          <option value="04">April</option>
          <option value="05">May</option>
          <option value="06">June</option>
          <option value="07">July</option>
          <option value="08">August</option>
          <option value="09">September</option>
          <option value="10">October</option>
          <option value="11">November</option>
          <option value="12">December</option>
        </select>
        <select id="yearSelect" class="modal-select">
          <?php
            $currentYear = date('Y');
            for ($y = $currentYear; $y >= $currentYear - 10; $y--) {
              echo "<option value=\"$y\">$y</option>";
            }
          ?>
        </select>
        <br>
        <button class="btn" onclick="confirmMonthModal()">Confirm</button>
        <button class="btn" onclick="closeMonthModal()">Cancel</button>
      </div>
    </div>

    <!-- Week Picker Modal -->
    <div id="weekModal" class="dialog">
      <div class="dialog-content">
        <h3>Select Month, Year, and Week</h3>
        <select id="weekMonthSelect" class="modal-select">
          <option value="01">January</option>
          <option value="02">February</option>
          <option value="03">March</option>
          <option value="04">April</option>
          <option value="05">May</option>
          <option value="06">June</option>
          <option value="07">July</option>
          <option value="08">August</option>
          <option value="09">September</option>
          <option value="10">October</option>
          <option value="11">November</option>
          <option value="12">December</option>
        </select>
        <select id="weekYearSelect" class="modal-select">
          <?php
            $currentYear = date('Y');
            for ($y = $currentYear; $y >= $currentYear - 10; $y--) {
              echo "<option value=\"$y\">$y</option>";
            }
          ?>
        </select>
        <select id="weekNumberSelect" class="modal-select">
          <option value="1">Week 1</option>
          <option value="2">Week 2</option>
          <option value="3">Week 3</option>
          <option value="4">Week 4</option>
        </select>
        <br>
        <button class="btn" onclick="confirmWeekModal()">Confirm</button>
        <button class="btn" onclick="closeWeekModal()">Cancel</button>
      </div>
    </div>
  
  </div>
  
  <script src="script.js"></script>
  <script>
    // Show modal when Import to PDF is clicked
    document.addEventListener('DOMContentLoaded', function() {
      document.querySelector('.card .button-group .btn').onclick = function(e) {
        e.preventDefault();
        document.getElementById('monthModal').style.display = 'flex';
      };
    });

    function closeMonthModal() {
      document.getElementById('monthModal').style.display = 'none';
    }

    function confirmMonthModal() {
      const month = document.getElementById('monthSelect').value;
      const year = document.getElementById('yearSelect').value;
      alert('Selected: ' + month + '/' + year);
      closeMonthModal();
    }

    // Show modal when Import to PDF is clicked under Print Report This Week
    document.addEventListener('DOMContentLoaded', function() {
      // This is the second card's button
      document.querySelectorAll('.card .button-group .btn')[1].onclick = function(e) {
        e.preventDefault();
        document.getElementById('weekModal').style.display = 'flex';
      };
    });

    function closeWeekModal() {
      document.getElementById('weekModal').style.display = 'none';
    }

    function confirmWeekModal() {
      const month = document.getElementById('weekMonthSelect').value;
      const year = document.getElementById('weekYearSelect').value;
      const week = document.getElementById('weekNumberSelect').value;
      alert('Selected: ' + month + '/' + year + ' - Week ' + week);
      closeWeekModal();
    }
  </script>
</body>
</html>
