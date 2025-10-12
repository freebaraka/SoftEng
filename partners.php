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

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM partners WHERE id = $delete_id");
    header("Location: dashboard.php");
    exit();
}

// Fetch partners data
$sql = "SELECT * FROM partners ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Partners - SaveEat</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body class="dashboard-body">
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2><i class="fa-solid fa-utensils"></i> SaveEat</h2>
            <ul>
                <li class="active"><a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                <li><a href="food_listing.php"><i class="fa-solid fa-burger"></i> Food Listing</a></li>
                <li><a href="partners.php"><i class="fa-solid fa-handshake"></i> Partners</a></li>
                <li><a href="reports.php"><i class="fa-solid fa-chart-line"></i> Reports</a></li>
                <li><a href="users.php"><i class="fa-solid fa-users"></i> Users</a></li>
                <li><a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a></li>
                <li><a href="logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header>
                <h1>Partner Hotels & Restaurants 🤝</h1>
                <p>Hotels and restaurants supporting the SaveEat Initiative.</p>
            </header>

            <section class="partners-grid">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="partner-card">
                            <div class="partner-info">
                                <h3><i class="fa-solid fa-building"></i> <?= htmlspecialchars($row['partner_name']) ?></h3>
                                <p><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($row['location']) ?></p>
                                <p><i class="fa-solid fa-utensils"></i> Donated: <?= htmlspecialchars($row['food_donated']) ?></p>
                            </div>
                            <a href="dashboard.php?delete_id=<?= $row['id'] ?>" 
                               class="delete-btn"
                               onclick="return confirm('Are you sure you want to remove this partner?');">
                                <i class="fa-solid fa-trash"></i> Remove
                            </a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-data">No partners found.</p>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>
