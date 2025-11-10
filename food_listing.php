<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

// Database connection for displaying data
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'SaveEat';
$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Check if food_items table exists, if not use foods table
$table_check = $conn->query("SHOW TABLES LIKE 'food_items'");
$table_name = $table_check->num_rows > 0 ? 'food_items' : 'foods';

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM $table_name WHERE id = $delete_id";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Food item deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting food item: " . $conn->error;
    }
    header("Location: food_listing.php");
    exit();
}

// Fetch foods data
$sql = "SELECT * FROM $table_name ORDER BY created_at DESC";
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
        
        .main-content {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
            height: 100vh;
            margin-left: 230px;
            background: linear-gradient(135deg, #0f172a, #1e293b, #334155);
        }
        
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(56, 189, 248, 0.5);
            border-radius: 10px;
        }
        
        .main-content::-webkit-scrollbar {
            width: 6px;
        }
        
        .main-content::-webkit-scrollbar-thumb {
            background: #38bdf8;
            border-radius: 10px;
        }
        
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
        
        .form-error {
            color: #f87171;
            font-size: 0.875rem;
            margin-top: 5px;
            display: none;
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
                <form id="addFoodForm" method="POST">
                    <div class="form-grid">
                        <div>
                            <input type="text" name="title" placeholder="Food Title" required>
                            <div class="form-error" id="title-error"></div>
                        </div>
                        <div>
                            <input type="text" name="food_type" placeholder="Food Type (e.g., Main Course, Dessert)" required>
                            <div class="form-error" id="food_type-error"></div>
                        </div>
                        <div>
                            <input type="text" name="portion_size" placeholder="Portion Size (e.g., 500g, 2 servings)" required>
                            <div class="form-error" id="portion_size-error"></div>
                        </div>
                        <div>
                            <input type="number" step="0.01" name="price" placeholder="Price (KES)" min="0">
                            <div class="form-error" id="price-error"></div>
                        </div>
                        <div>
                            <input type="number" name="quantity" placeholder="Quantity" required min="1" value="1">
                            <div class="form-error" id="quantity-error"></div>
                        </div>
                        <div>
                            <input type="datetime-local" name="expires_at" required>
                            <div class="form-error" id="expires_at-error"></div>
                        </div>
                    </div>
                    <div>
                        <textarea name="storage_instructions" placeholder="Storage Instructions (e.g., Refrigerate, Keep frozen)" rows="2" required></textarea>
                        <div class="form-error" id="storage_instructions-error"></div>
                    </div>
                    <button type="submit" class="btn add-btn">
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
                                <h3><i class="fa-solid fa-bowl-food"></i> 
                                    <?= htmlspecialchars($row['title'] ?? $row['food_name'] ?? 'No Title') ?>
                                </h3>
                                <p><i class="fa-solid fa-utensils"></i> Type: <?= htmlspecialchars($row['food_type']) ?></p>
                                <p><i class="fa-solid fa-box"></i> Portion: <?= htmlspecialchars($row['portion_size']) ?></p>
                                <p><i class="fa-solid fa-money-bill"></i> Price: KES <?= htmlspecialchars($row['price']) ?></p>
                                
                                <?php if (isset($row['quantity'])): ?>
                                    <p><i class="fa-solid fa-layer-group"></i> Quantity: <?= htmlspecialchars($row['quantity']) ?></p>
                                <?php endif; ?>
                                
                                <p><i class="fa-solid fa-calendar-xmark"></i> Expires: 
                                    <?= date('M j, Y g:i A', strtotime($row['expires_at'] ?? $row['expiry_date'] ?? $row['expiry_datetime'])) ?>
                                </p>
                                
                                <?php if (!empty($row['storage_instructions'])): ?>
                                    <p><i class="fa-solid fa-info-circle"></i> Storage: <?= htmlspecialchars($row['storage_instructions']) ?></p>
                                <?php endif; ?>
                                
                                <?php if (isset($row['created_at'])): ?>
                                    <p><i class="fa-solid fa-clock"></i> Added: <?= date('M j, Y g:i A', strtotime($row['created_at'])) ?></p>
                                <?php endif; ?>
                                
                                <?php if (isset($row['status'])): ?>
                                    <p><i class="fa-solid fa-circle" style="color: <?= $row['status'] == 'available' ? '#10b981' : '#ef4444' ?>"></i> 
                                        Status: <?= ucfirst($row['status']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <a href="food_listing.php?delete_id=<?= $row['id'] ?>" 
                               class="delete-btn"
                               onclick="return confirm('Are you sure you want to remove <?= htmlspecialchars($row['title'] ?? $row['food_name'] ?? 'this item') ?>?');">
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
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('addFoodForm');
            const submitBtn = form.querySelector('button[type="submit"]');
            
            // Set minimum datetime for expiry to current time + 1 hour (to ensure it's in the future)
            const expiresAtInput = document.querySelector('input[name="expires_at"]');
            if (expiresAtInput) {
                const now = new Date();
                // Add 1 hour to ensure it's always in the future
                now.setHours(now.getHours() + 1);
                
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                
                const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
                expiresAtInput.min = minDateTime;
                
                // Set default value to 2 hours from now
                const defaultTime = new Date(now.getTime() + (60 * 60 * 1000)); // +1 more hour
                const defaultYear = defaultTime.getFullYear();
                const defaultMonth = String(defaultTime.getMonth() + 1).padStart(2, '0');
                const defaultDay = String(defaultTime.getDate()).padStart(2, '0');
                const defaultHours = String(defaultTime.getHours()).padStart(2, '0');
                const defaultMinutes = String(defaultTime.getMinutes()).padStart(2, '0');
                
                const defaultDateTime = `${defaultYear}-${defaultMonth}-${defaultDay}T${defaultHours}:${defaultMinutes}`;
                expiresAtInput.value = defaultDateTime;
            }

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // Clear previous errors
                document.querySelectorAll('.form-error').forEach(error => {
                    error.style.display = 'none';
                    error.textContent = '';
                });

                // Get form data
                const formData = new FormData(form);
                const data = {
                    title: formData.get('title').trim(),
                    food_type: formData.get('food_type').trim(),
                    portion_size: formData.get('portion_size').trim(),
                    price: formData.get('price'),
                    quantity: parseInt(formData.get('quantity')),
                    expires_at: formData.get('expires_at'),
                    storage_instructions: formData.get('storage_instructions').trim()
                };

                console.log("Submitting data:", data);

                // Basic validation
                let hasErrors = false;
                
                if (!data.title) {
                    showError('title-error', 'Title is required.');
                    hasErrors = true;
                }
                
                if (!data.food_type) {
                    showError('food_type-error', 'Food type is required.');
                    hasErrors = true;
                }
                
                if (!data.portion_size) {
                    showError('portion_size-error', 'Portion size is required.');
                    hasErrors = true;
                }
                
                if (!data.quantity || data.quantity < 1) {
                    showError('quantity-error', 'Quantity must be at least 1.');
                    hasErrors = true;
                }
                
                if (!data.expires_at) {
                    showError('expires_at-error', 'Expiry datetime is required.');
                    hasErrors = true;
                } else {
                    const selectedDate = new Date(data.expires_at);
                    const now = new Date();
                    const maxDate = new Date(now.getTime() + 24 * 60 * 60 * 1000); // 24 hours from now
                    
                    if (selectedDate <= now) {
                        showError('expires_at-error', 'Expiry must be in the future (at least 1 hour from now).');
                        hasErrors = true;
                    } else if (selectedDate > maxDate) {
                        showError('expires_at-error', 'Expiry cannot be more than 24 hours from now.');
                        hasErrors = true;
                    }
                }
                
                if (!data.storage_instructions) {
                    showError('storage_instructions-error', 'Storage instructions are required.');
                    hasErrors = true;
                }

                if (hasErrors) return;

                // Add loading state
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Adding Food...';
                submitBtn.disabled = true;

                try {
                    console.log("Sending request to API...");
                    
                    const response = await fetch('/SoftEng/api/add_food.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(data)
                    });

                    console.log("Response status:", response.status);
                    
                    const result = await response.json();
                    console.log("API response:", result);

                    if (result.success) {
                        showMessage('Food item added successfully!', 'success');
                        form.reset();
                        
                        // Reset the datetime to a future time
                        if (expiresAtInput) {
                            const futureTime = new Date();
                            futureTime.setHours(futureTime.getHours() + 2);
                            const futureYear = futureTime.getFullYear();
                            const futureMonth = String(futureTime.getMonth() + 1).padStart(2, '0');
                            const futureDay = String(futureTime.getDate()).padStart(2, '0');
                            const futureHours = String(futureTime.getHours()).padStart(2, '0');
                            const futureMinutes = String(futureTime.getMinutes()).padStart(2, '0');
                            expiresAtInput.value = `${futureYear}-${futureMonth}-${futureDay}T${futureHours}:${futureMinutes}`;
                        }
                        
                        // Reload page after 1 second to show new item
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        // Show API errors
                        if (result.errors) {
                            result.errors.forEach(error => {
                                if (error.includes('Title')) showError('title-error', error);
                                else if (error.includes('Food type')) showError('food_type-error', error);
                                else if (error.includes('Quantity')) showError('quantity-error', error);
                                else if (error.includes('Expiry')) showError('expires_at-error', error);
                                else showMessage(error, 'error');
                            });
                        } else if (result.error) {
                            if (result.error.includes('Expiry must be in the future')) {
                                showError('expires_at-error', 'Please select a future time (at least 1 hour from now).');
                            } else if (result.error.includes('24 hours')) {
                                showError('expires_at-error', 'Expiry cannot be more than 24 hours from now.');
                            } else if (result.error.includes('Invalid expires_at format')) {
                                showError('expires_at-error', 'Please use the correct datetime format.');
                            } else {
                                showMessage(result.error, 'error');
                            }
                        }
                    }
                } catch (error) {
                    console.error("Fetch error:", error);
                    showMessage('Network error. Please try again.', 'error');
                } finally {
                    // Reset button state
                    submitBtn.innerHTML = '<i class="fa-solid fa-circle-plus"></i> Add Food';
                    submitBtn.disabled = false;
                }
            });

            function showError(elementId, message) {
                const errorElement = document.getElementById(elementId);
                errorElement.textContent = message;
                errorElement.style.display = 'block';
            }

            function showMessage(message, type) {
                const messageDiv = document.createElement('div');
                messageDiv.className = `message ${type}`;
                messageDiv.innerHTML = `<i class="fa-solid fa-${type === 'success' ? 'circle-check' : 'circle-exclamation'}"></i> ${message}`;
                
                // Insert after the header
                const header = document.querySelector('header');
                header.parentNode.insertBefore(messageDiv, header.nextSibling);
                
                // Auto-hide after 5 seconds
                setTimeout(() => {
                    messageDiv.style.opacity = '0';
                    messageDiv.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => {
                        messageDiv.remove();
                    }, 500);
                }, 5000);
            }

            // Auto-hide existing messages after 5 seconds
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