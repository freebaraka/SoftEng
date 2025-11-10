<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'SaveEat';
$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM users WHERE id = $delete_id");
    header("Location: users.php");
    exit();
}

// Handle search
$search = '';
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT * FROM users WHERE username LIKE '%$search%' OR name LIKE '%$search%' OR email LIKE '%$search%' ORDER BY username ASC";
} else {
    $sql = "SELECT * FROM users ORDER BY username ASC";
}
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Users - SaveEat</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        table.styled-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #1e293b;
            color: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .styled-table th, .styled-table td {
            padding: 12px 15px;
            border: 1px solid #334155;
            text-align: left;
        }
        .styled-table thead th {
            background-color: #0f172a;
            color: #38bdf8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .styled-table tr {
            background-color: #1e293b;
        }
        .styled-table tr:nth-child(even) {
            background-color: #273449;
        }
        .styled-table tr:hover {
            background-color: #334155;
            transition: background 0.3s;
        }
        .delete-btn {
            color: #fff;
            background-color: #e74c3c;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
        }
        .delete-btn:hover {
            background-color: #c0392b;
        }
        .no-data {
            margin-top: 20px;
            font-style: italic;
        }
    </style>
</head>
<body class="dashboard-body">
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2><i class="fa-solid fa-utensils"></i> SaveEat</h2>
            <ul>
                <li><a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                <li><a href="food_listing.php"><i class="fa-solid fa-burger"></i> Food Listing</a></li>
                <li><a href="partners.php"><i class="fa-solid fa-handshake"></i> Partners</a></li>
                <li><a href="reports.php"><i class="fa-solid fa-chart-line"></i> Reports</a></li>
                <li class="active"><a href="users.php"><i class="fa-solid fa-users"></i> Users</a></li>
                <li><a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a></li>
                <li><a href="logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header>
                <h1>All Users 👥</h1>
                <p>Manage all users registered in the SaveEat system.</p>
            </header>

            <!-- Search Bar for Admins -->
            <form method="get" style="margin-bottom: 20px; max-width: 400px;">
                <input type="text" name="search" placeholder="Search users by username, name, or email..." value="<?= htmlspecialchars($search) ?>" style="width: 75%; padding: 8px; border-radius: 6px; border: 1px solid #38bdf8; background: #0f172a; color: #fff;">
                <button type="submit" style="padding: 8px 16px; border-radius: 6px; border: none; background: #38bdf8; color: #0f172a; font-weight: bold; cursor: pointer;">Search</button>
            </form>

            <?php if ($result && $result->num_rows > 0): ?>
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['email'] ?? '-') ?></td>
                                <td><?= $row['role'] ?></td>
                                <td><?= $row['created_at'] ?></td>
                                <td>
                                    <a href="users.php?delete_id=<?= $row['id'] ?>" 
                                       class="delete-btn"
                                       onclick="return confirm('Are you sure you want to remove this user?');">
                                       <i class="fa-solid fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">No users found.</p>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
