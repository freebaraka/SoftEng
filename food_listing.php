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

// Handle Add New Food form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_food'])) {
    $food_name = $_POST['food_name'];
    $food_type = $_POST['food_type'];
    $portion_size = $_POST['portion_size'];
    $price = $_POST['price'];
    $storage_instructions = $_POST['storage_instructions'];
    $expiry_date = $_POST['expiry_date'];

    $stmt = $conn->prepare("INSERT INTO foods (food_name, food_type, portion_size, price, storage_instructions, expiry_date) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiss", $food_name, $food_type, $portion_size, $price, $storage_instructions, $expiry_date);
    $stmt->execute();
    $stmt->close();

    header("Location: food_listing.php");
    exit();
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM foods WHERE id = $delete_id");
    header("Location: food_listing.php");
    exit();
}

// Fetch foods data
$sql = "SELECT * FROM foods ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Food Listing - SaveEat</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body class="dashboard-body">
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2><i class="fa-solid fa-utensils"></i> SaveEat</h2>
            <ul>
                <li><a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                <li class="active"><a href="food_listing.php"><i class="fa-solid fa-burger"></i> Food Listing</a></li>
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
                <h1>Food Listings 🍲</h1>
                <p>Manage and view all available food donations.</p>
            </header>

            <!-- Add New Food Form -->
            <section class="add-food-form">
                <h2><i class="fa-solid fa-plus"></i> Add New Food</h2>
                <form method="POST" action="">
                    <div class="form-grid">
                        <input type="text" name="food_name" placeholder="Food Name" required>
                        <input type="text" name="food_type" placeholder="Food Type" required>
                        <input type="text" name="portion_size" placeholder="Portion Size" required>
                        <input type="number" step="0.01" name="price" placeholder="Price (KES)" required>
                        <input type="date" name="expiry_date" required>
                    </div>
                    <textarea name="storage_instructions" placeholder="Storage Instructions" rows="2" required></textarea>
                    <button type="submit" name="add_food" class="btn add-btn">
                        <i class="fa-solid fa-circle-plus"></i> Add Food
                    </button>
                </form>
            </section>

            <!-- Food Cards -->
            <section class="food-grid">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="food-card">
                            <div class="food-info">
                                <h3><i class="fa-solid fa-bowl-food"></i> <?= htmlspecialchars($row['food_name']) ?></h3>
                                <p><i class="fa-solid fa-utensils"></i> Type: <?= htmlspecialchars($row['food_type']) ?></p>
                                <p><i class="fa-solid fa-box"></i> Portion: <?= htmlspecialchars($row['portion_size']) ?></p>
                                <p><i class="fa-solid fa-dollar-sign"></i> Price: <?= htmlspecialchars($row['price']) ?> KES</p>
                                <p><i class="fa-solid fa-calendar-xmark"></i> Expiry: <?= htmlspecialchars($row['expiry_date']) ?></p>
                            </div>
                            <a href="food_listing.php?delete_id=<?= $row['id'] ?>" 
                               class="delete-btn"
                               onclick="return confirm('Are you sure you want to remove this food item?');">
                                <i class="fa-solid fa-trash"></i> Remove
                            </a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-data">No food records found.</p>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>
