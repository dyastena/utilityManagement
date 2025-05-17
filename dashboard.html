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
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Settings - NeatWare</title>
  <link rel="stylesheet" href="settings.css" />
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
      <a href="dashboard.php"><i class="fas fa-tachometer-alt nav-icon"></i><span class="nav-text">Dashboard</span></a>
      <a href="inspections.php"><i class="fas fa-clipboard-list nav-icon"></i><span class="nav-text">Inspections</span></a>
      <a href="reports.php"><i class="fas fa-chart-bar nav-icon"></i><span class="nav-text">Reports</span></a>
      <a href="settings.php" class="active"><i class="fas fa-cogs nav-icon"></i><span class="nav-text">Settings</span></a>
    </nav>
    <form action="logout.php" method="POST">
      <button type="submit" class="logout-btn">Log Out</button>
    </form>
  </aside>

  <main class="main">
    <header class="header">
      <h1>Settings</h1>
      <p>Manage preferences, secure your account, and manage staff assignments</p>
    </header>

    <div class="card-container">

    <!-- Manage Staff Section -->
    <section class="card">
        <h2>Manage Staff</h2>
        <table>
          <thead>
          <tr>
            <th>Name</th>
            <th>Position</th>
            <th></th>
          </tr>
          </thead>
          <tbody>
          <tr><td>Melanie Ruiz</td><td>Janitor</td><td><a href="#">Edit</a></td></tr>
          <tr><td>Luis Santos</td><td>Janitor</td><td><a href="#">Edit</a></td></tr>
          <tr><td>Julius Dela Cruz</td><td>Supervisor</td><td><a href="#">Edit</a></td></tr>
          <tr><td>Carlos Tan</td><td>Janitor</td><td><a href="#">Edit</a></td></tr>
          </tbody>
        </table>
        <form class="add-staff-form" action="#" method="POST">
          <input type="text" placeholder="Name" name="staff_name" required />
          <button type="submit" class="add-btn">Add Staff</button>
        </form>
      </section>

      <!-- Change Password Section -->
      <section class="card">
        <h2>Change Password</h2>
        <form class="form" action="#" method="POST">
          <input type="password" placeholder="Current password" name="current_password" required />
          <input type="password" placeholder="New password" name="new_password" required />
          <input type="password" placeholder="Confirm new password" name="confirm_password" required />
          <button type="submit" class="save-btn">Save</button>
        </form>
      </section>

      <section class="card">
        <h2>New Card</h2>
        <p>This is a new card added to the layout.</p>
      </section>
        
    </div>
  </main>
</div>
<script src="script.js"></script>
</body>
</html>
