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

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Include database connection
    require_once 'db.php';

    // Get form data
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $floor_assignment = $_POST['floor_assignment'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate passwords
    if ($password !== $confirm_password) {
        echo "Passwords do not match.";
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Handle file upload
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $file_name = basename($_FILES['picture']['name']);
        $target_file = $upload_dir . $file_name;

        // Move the uploaded file
        if (!move_uploaded_file($_FILES['picture']['tmp_name'], $target_file)) {
            echo "Failed to upload picture.";
            exit;
        }
    } else {
        echo "Please upload a valid picture.";
        exit;
    }

    // Insert data into the database
    $stmt = $conn->prepare("INSERT INTO staff (first_name, last_name, email, floor_assignment, picture, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $first_name, $last_name, $email, $floor_assignment, $target_file, $hashed_password);

    if ($stmt->execute()) {
        echo "Staff registered successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
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
      </section>

      <!-- Staff Registration Section -->
      <section class="card">
        <h2>Staff Registration</h2>
        <form action="register_staff.php" method="POST" enctype="multipart/form-data" class="staff-registration-form">
          <!-- First Name -->
          <label for="first_name">First Name</label>
          <input type="text" id="first_name" name="first_name" placeholder="Enter first name" required>

          <!-- Last Name -->
          <label for="last_name">Last Name</label>
          <input type="text" id="last_name" name="last_name" placeholder="Enter last name" required>

          <!-- Email -->
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="Enter email address" required>

          <!-- Floor Assignment -->
          <label for="floor_assignment">Floor Assignment</label>
          <select id="floor_assignment" name="floor_assignment" required>
            <option value="">Select a floor</option>
            <option value="1st Floor">1st Floor</option>
            <option value="2nd Floor">2nd Floor</option>
            <option value="3rd Floor">3rd Floor</option>
            <option value="4th Floor">4th Floor</option>
            <option value="5th Floor">5th Floor</option>
            <option value="6th Floor">6th Floor</option>
            <option value="7th Floor">7th Floor</option>
            <option value="8th Floor">8th Floor</option>
          </select>

          <!-- Picture -->
          <label for="picture">Profile Picture</label>
          <input type="file" id="picture" name="picture" accept="image/*" required>

          <!-- Password -->
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Enter password" required>

          <!-- Confirm Password -->
          <label for="confirm_password">Confirm Password</label>
          <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>

          <!-- Submit Button -->
          <button type="submit" class="sbmt">Register Staff</button>
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
        
    </div>
  </main>
</div>
<script src="script.js"></script>
</body>
</html>
