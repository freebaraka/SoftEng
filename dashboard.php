<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit();
}
// Database connection
$host = 'localhost';
$user = 'root';
$pass = '1234';
$dbname = 'SaveEat';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Fetch partners data
$sql = "SELECT * FROM partners";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SaveEat Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body class="dashboard-body">
    <div class="dashboard-container">
<!-- Sidebar -->
<aside class="sidebar">
    <h2><i class="fa-solid fa-utensils"></i> SaveEat</h2>
    <ul>
        <li class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
            <a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a>
        </li>
        <li class="<?= basename($_SERVER['PHP_SELF']) == 'food_listing.php' ? 'active' : '' ?>">
            <a href="food_listing.php"><i class="fa-solid fa-burger"></i> Food Listing</a>
        </li>
        <li class="<?= basename($_SERVER['PHP_SELF']) == 'partners.php' ? 'active' : '' ?>">
            <a href="partners.php"><i class="fa-solid fa-handshake"></i> Partners</a>
        </li>
        <li class="<?= basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : '' ?>">
            <a href="reports.php"><i class="fa-solid fa-chart-line"></i> Reports</a>
        </li>
        <li class="<?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>">
            <a href="users.php"><i class="fa-solid fa-users"></i> Users</a>
        </li>
        <li class="<?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : '' ?>">
            <a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a>
        </li>
        <li>
            <a href="logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </li>
    </ul>
</aside>


        <!-- Main Content -->
        <main class="main-content">
            <header>
                <h1>Welcome Back, Admin 👋</h1>
                <p>Here’s what’s happening today at SaveEat Foundation.</p>
            </header>

            <section class="stats">
                <div class="card">
                    <i class="fa-solid fa-apple-whole card-icon"></i>
                    <h3>Food Donations</h3>
                    <p>128</p>
                </div>
                <div class="card">
                    <i class="fa-solid fa-hand-holding-heart card-icon"></i>
                    <h3>Beneficiaries</h3>
                    <p>42</p>
                </div>
                <div class="card">
                    <i class="fa-solid fa-handshake-angle card-icon"></i>
                    <h3>Partners</h3>
                    <p>16</p>
                </div>
            </section>

            <section class="overview">
                <h2>Overview</h2>
                <p>
                    SaveEat Initiative focuses on reducing food waste and supporting local communities.
                    Track your donations, manage partners, and access detailed reports from here.
                </p>
            </section>
        </main>
    </div>
</body>
</html>
