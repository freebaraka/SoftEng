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
    <style>
        /* Additional CSS to fix scrolling issues */
        .dashboard-body {
            height: 100vh;
            overflow: hidden; /* Prevent body scrolling */
        }
        
        .dashboard-container {
            height: 100vh;
            display: flex;
        }
        
        /* Sidebar with independent scrolling */
        .sidebar {
            width: 230px;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            padding: 2rem 1rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            height: 100vh;
            overflow-y: auto; /* Enable scrolling only for sidebar */
            position: fixed; /* Fixed positioning */
            left: 0;
            top: 0;
            z-index: 1000;
        }
        
        /* Main content with independent scrolling */
        .main-content {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
            height: 100vh;
            margin-left: 230px; /* Account for fixed sidebar width */
        }
        
        /* Custom scrollbar for sidebar */
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(56, 189, 248, 0.5);
            border-radius: 10px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }
        
        /* Custom scrollbar for main content */
        .main-content::-webkit-scrollbar {
            width: 6px;
        }
        
        .main-content::-webkit-scrollbar-thumb {
            background: #38bdf8;
            border-radius: 10px;
        }
        
        .main-content::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }
        
        /* Ensure content doesn't get hidden behind fixed sidebar */
        .stats, .overview, .partners {
            position: relative;
            z-index: 1;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                margin-left: 0;
            }
            
            .main-content {
                margin-left: 0;
                height: auto;
            }
            
            .dashboard-container {
                flex-direction: column;
            }
        }
        
        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="dashboard-body">
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div>
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
                </ul>
            </div>
            <div>
                <a href="logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header>
                <h1>Welcome Back, Admin 👋</h1>
                <p>Here's what's happening today at SaveEat Foundation.</p>
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
                <div class="card">
                    <i class="fa-solid fa-clock card-icon"></i>
                    <h3>Pending Orders</h3>
                    <p>8</p>
                </div>
            </section>

            <section class="overview">
                <h2>Overview</h2>
                <p>
                    SaveEat Initiative focuses on reducing food waste and supporting local communities.
                    Track your donations, manage partners, and access detailed reports from here.
                </p>
                <p>
                    Today's activities include 5 new food listings, 3 partner registrations, and 12 completed transactions.
                    The system is running smoothly with all services operational.
                </p>
            </section>

            <!-- Recent Activity Section -->
            <section class="partners">
                <h2><i class="fa-solid fa-clock-rotate-left"></i> Recent Activity</h2>
                <div class="partners-table-container">
                    <table class="partners-table">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Activity</th>
                                <th>User</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>10:30 AM</td>
                                <td>New food listing added</td>
                                <td>Restaurant A</td>
                                <td><span style="color: #10b981;">Completed</span></td>
                            </tr>
                            <tr>
                                <td>09:45 AM</td>
                                <td>Partner registration</td>
                                <td>Cafe B</td>
                                <td><span style="color: #f59e0b;">Pending</span></td>
                            </tr>
                            <tr>
                                <td>09:15 AM</td>
                                <td>Food order completed</td>
                                <td>Shelter C</td>
                                <td><span style="color: #10b981;">Completed</span></td>
                            </tr>
                            <tr>
                                <td>08:30 AM</td>
                                <td>System backup</td>
                                <td>Admin</td>
                                <td><span style="color: #10b981;">Completed</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Quick Actions Section -->
            <section class="overview" style="margin-top: 2rem;">
                <h2>Quick Actions</h2>
                <div style="display: flex; gap: 1rem; margin-top: 1rem; flex-wrap: wrap;">
                        <a href="food_listing.php"><button class="btn" style="flex: 1; min-width: 200px;">
                            <i class="fa-solid fa-plus"></i> Add New Food Listing
                        </button></a>
                        <a href="partners.php"><button class="btn" style="flex: 1; min-width: 200px;">
                            <i class="fa-solid fa-user-plus"></i> Add New Partner
                        </button></a>
                        <a href="reports.php"><button class="btn" style="flex: 1; min-width: 200px;">
                            <i class="fa-solid fa-file-export"></i> Generate Report
                        </button></a>
                </div>
            </section>
        </main>
    </div>

    <script>
        // JavaScript to enhance scrolling experience
        document.addEventListener('DOMContentLoaded', function() {
            // Prevent body scrolling when mouse is over sidebar
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            
            sidebar.addEventListener('mouseenter', function() {
                document.body.style.overflow = 'hidden';
            });
            
            sidebar.addEventListener('mouseleave', function() {
                document.body.style.overflow = 'hidden'; // Keep hidden as we have independent scrolling
            });
            
            // Add smooth scrolling to all links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
            
            // Update active states based on scroll position
            window.addEventListener('scroll', function() {
                const sections = document.querySelectorAll('section');
                const scrollPos = window.scrollY || document.documentElement.scrollTop;
                
                sections.forEach(section => {
                    const sectionTop = section.offsetTop - 100;
                    const sectionBottom = sectionTop + section.offsetHeight;
                    
                    if (scrollPos >= sectionTop && scrollPos < sectionBottom) {
                        // Remove active class from all
                        document.querySelectorAll('.sidebar li').forEach(li => {
                            li.classList.remove('active');
                        });
                        
                        // Add active class to corresponding nav item
                        const correspondingNav = document.querySelector(`.sidebar a[href="#${section.id}"]`);
                        if (correspondingNav) {
                            correspondingNav.parentElement.classList.add('active');
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>