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

// Handle Add New Food form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_food'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $food_type = $conn->real_escape_string($_POST['food_type']);
    $portion_size = $conn->real_escape_string($_POST['portion_size']);
    $price = $conn->real_escape_string($_POST['price']);
    $storage_instructions = $conn->real_escape_string($_POST['storage_instructions']);
    $expires_at = $conn->real_escape_string($_POST['expires_at']);
    $quantity = $conn->real_escape_string($_POST['quantity']);

    // Debug: Check if values are received
    error_log("Adding food: $title, $food_type, $portion_size, $price, $storage_instructions, $expires_at, $quantity");

    // Convert datetime format
    $expires_at = date('Y-m-d H:i:s', strtotime($expires_at));
    $listed_at = date('Y-m-d H:i:s');

    // Insert into food_items table (based on your API structure)
    $sql = "INSERT INTO food_items (title, food_type, portion_size, price, storage_instructions, expires_at, quantity, listed_at, status) 
            VALUES ('$title', '$food_type', '$portion_size', '$price', '$storage_instructions', '$expires_at', '$quantity', '$listed_at', 'available')";
    
    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Food item added successfully!";
        header("Location: food_listing.php");
        exit();
    } else {
        $_SESSION['error'] = "Error adding food item: " . $conn->error;
        error_log("Database Error: " . $conn->error);
    }
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM food_items WHERE id = $delete_id";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Food item deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting food item: " . $conn->error;
    }
    header("Location: food_listing.php");
    exit();
}

// Fetch foods data from food_items table
$sql = "SELECT * FROM food_items ORDER BY listed_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Food Listing - SaveEat</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        /* Additional CSS to fix scrolling issues */
        .dashboard-body {
            height: 100vh;
            overflow: hidden;
            margin: 0;
        }
        
        .dashboard-container {
            height: 100vh;
            display: flex;
            overflow: hidden;
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
            overflow-y: auto;
            position: fixed;
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
            margin-left: 230px;
            background: linear-gradient(135deg, #0f172a, #1e293b, #334155);
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
        
        /* Message Styling */
        .message {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
        }
        
        .message.success {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        
        .message.error {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
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
        
        /* Ensure content doesn't get hidden behind fixed sidebar */
        .add-food-form, .food-grid {
            position: relative;
            z-index: 1;
        }
        
        /* Enhanced food grid for better scrolling */
        .food-grid {
            max-height: none;
            overflow: visible;
        }
        
        /* DateTime input styling */
        input[type="datetime-local"] {
            width: 100%;
            padding: 12px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        input[type="datetime-local"]:focus {
            outline: none;
            border-color: #38bdf8;
            background: rgba(255, 255, 255, 0.15);
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
                    <li><a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                    <li class="active"><a href="food_listing.php"><i class="fa-solid fa-burger"></i> Food Listing</a></li>
                    <li><a href="partners.php"><i class="fa-solid fa-handshake"></i> Partners</a></li>
                    <li><a href="reports.php"><i class="fa-solid fa-chart-line"></i> Reports</a></li>
                    <li><a href="users.php"><i class="fa-solid fa-users"></i> Users</a></li>
                    <li><a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a></li>
                </ul>
            </div>
            <div>
                <a href="logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header>
                <h1>Food Listings 🍲</h1>
                <p>Manage and view all available food donations.</p>
            </header>

            <!-- Display Messages -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="message success">
                    <i class="fa-solid fa-circle-check"></i> <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="message error">
                    <i class="fa-solid fa-circle-exclamation"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Add New Food Form -->
            <section class="add-food-form">
                <h2><i class="fa-solid fa-plus"></i> Add New Food</h2>
                <form method="POST" action="">
                    <div class="form-grid">
                        <input type="text" name="title" placeholder="Food Title" required>
                        <input type="text" name="food_type" placeholder="Food Type (e.g., Main Course, Dessert)" required>
                        <input type="text" name="portion_size" placeholder="Portion Size (e.g., 500g, 2 servings)" required>
                        <input type="number" step="0.01" name="price" placeholder="Price (KES)" required min="0">
                        <input type="number" name="quantity" placeholder="Quantity" required min="1">
                        <input type="datetime-local" name="expires_at" required>
                    </div>
                    <textarea name="storage_instructions" placeholder="Storage Instructions (e.g., Refrigerate, Keep frozen)" rows="2" required></textarea>
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
                                <h3><i class="fa-solid fa-bowl-food"></i> <?= htmlspecialchars($row['title']) ?></h3>
                                <p><i class="fa-solid fa-utensils"></i> Type: <?= htmlspecialchars($row['food_type']) ?></p>
                                <p><i class="fa-solid fa-box"></i> Portion: <?= htmlspecialchars($row['portion_size']) ?></p>
                                <p><i class="fa-solid fa-money-bill"></i> Price: KES <?= htmlspecialchars($row['price']) ?></p>
                                <p><i class="fa-solid fa-layer-group"></i> Quantity: <?= htmlspecialchars($row['quantity']) ?></p>
                                <p><i class="fa-solid fa-calendar-xmark"></i> Expires: <?= date('M j, Y g:i A', strtotime($row['expires_at'])) ?></p>
                                <?php if (!empty($row['storage_instructions'])): ?>
                                    <p><i class="fa-solid fa-info-circle"></i> Storage: <?= htmlspecialchars($row['storage_instructions']) ?></p>
                                <?php endif; ?>
                                <?php if (isset($row['listed_at'])): ?>
                                    <p><i class="fa-solid fa-clock"></i> Listed: <?= date('M j, Y g:i A', strtotime($row['listed_at'])) ?></p>
                                <?php endif; ?>
                                <p><i class="fa-solid fa-circle" style="color: <?= $row['status'] == 'available' ? '#10b981' : '#ef4444' ?>"></i> Status: <?= ucfirst($row['status']) ?></p>
                            </div>
                            <a href="food_listing.php?delete_id=<?= $row['id'] ?>" 
                               class="delete-btn"
                               onclick="return confirm('Are you sure you want to remove <?= htmlspecialchars($row['title']) ?>?');">
                                <i class="fa-solid fa-trash"></i> Remove
                            </a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-data" style="text-align: center; padding: 2rem; background: rgba(255,255,255,0.05); border-radius: 10px;">
                        <i class="fa-solid fa-inbox" style="font-size: 3rem; color: #64748b; margin-bottom: 1rem;"></i>
                        <h3 style="color: #94a3b8; margin-bottom: 0.5rem;">No Food Items Found</h3>
                        <p style="color: #64748b;">Start by adding your first food listing using the form above.</p>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <script>
        // JavaScript to enhance user experience
        document.addEventListener('DOMContentLoaded', function() {
            // Set minimum datetime for expiry to current time
            const expiresAtInput = document.querySelector('input[name="expires_at"]');
            if (expiresAtInput) {
                const now = new Date();
                // Format to YYYY-MM-DDTHH:MM (datetime-local format)
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                
                const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
                expiresAtInput.min = minDateTime;
            }
            
            // Form validation
            const addFoodForm = document.querySelector('form[method="POST"]');
            if (addFoodForm) {
                addFoodForm.addEventListener('submit', function(e) {
                    const title = document.querySelector('input[name="title"]').value.trim();
                    const foodType = document.querySelector('input[name="food_type"]').value.trim();
                    const portionSize = document.querySelector('input[name="portion_size"]').value.trim();
                    const price = document.querySelector('input[name="price"]').value;
                    const quantity = document.querySelector('input[name="quantity"]').value;
                    const expiresAt = document.querySelector('input[name="expires_at"]').value;
                    
                    if (!title || !foodType || !portionSize || !price || !quantity || !expiresAt) {
                        e.preventDefault();
                        alert('Please fill in all required fields.');
                        return;
                    }
                    
                    if (parseFloat(price) < 0) {
                        e.preventDefault();
                        alert('Price cannot be negative.');
                        return;
                    }
                    
                    if (parseInt(quantity) < 1) {
                        e.preventDefault();
                        alert('Quantity must be at least 1.');
                        return;
                    }
                    
                    // Check if expiry datetime is in the future
                    const selectedDate = new Date(expiresAt);
                    const now = new Date();
                    if (selectedDate <= now) {
                        e.preventDefault();
                        alert('Expiry date and time must be in the future.');
                        return;
                    }
                    
                    // Add loading state
                    const submitBtn = this.querySelector('button[type="submit"]');
                    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Adding Food...';
                    submitBtn.disabled = true;
                });
            }
            
            // Auto-hide messages after 5 seconds
            const messages = document.querySelectorAll('.message');
            messages.forEach(message => {
                setTimeout(() => {
                    message.style.opacity = '0';
                    message.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => {
                        message.remove();
                    }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>