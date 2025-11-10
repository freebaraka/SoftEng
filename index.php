<?php
session_start();

// Database connection
$conn = new mysqli('localhost','root','','SaveEat');
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch available food items
$food_items = [];
$food_result = $conn->query("
    SELECT fi.*, h.business_name as hotel_name 
    FROM food_items fi 
    LEFT JOIN hotels h ON fi.hotel_id = h.id 
    WHERE fi.status = 'available' 
    AND fi.expires_at > NOW() 
    AND fi.quantity > 0 
    ORDER BY fi.listed_at DESC
");

if ($food_result) {
    $food_items = $food_result->fetch_all(MYSQLI_ASSOC);
}

// Handle order placement
$order_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $customer_name = trim($_POST['customer_name'] ?? '');
    $customer_phone = trim($_POST['customer_phone'] ?? '');
    $customer_email = trim($_POST['customer_email'] ?? '');
    $food_item_id = intval($_POST['food_item_id'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 1);
    
    // Validate input
    if (empty($customer_name) || empty($customer_phone) || empty($customer_email) || $food_item_id <= 0) {
        $order_message = "Please fill in all required fields and select a food item.";
    } else {
        // Check if food item is still available
        $check_stmt = $conn->prepare("SELECT quantity, title FROM food_items WHERE id = ? AND status = 'available' AND quantity >= ?");
        $check_stmt->bind_param("ii", $food_item_id, $quantity);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 1) {
            $food_item = $check_result->fetch_assoc();
            
            // Insert order
            $order_stmt = $conn->prepare("INSERT INTO orders (food_item_id, customer_name, customer_phone, customer_email, quantity, order_status, ordered_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
            $order_stmt->bind_param("isssi", $food_item_id, $customer_name, $customer_phone, $customer_email, $quantity);
            
            if ($order_stmt->execute()) {
                // Update food item quantity
                $update_stmt = $conn->prepare("UPDATE food_items SET quantity = quantity - ? WHERE id = ?");
                $update_stmt->bind_param("ii", $quantity, $food_item_id);
                $update_stmt->execute();
                
                $order_message = "success:Order placed successfully! Your order for {$food_item['title']} is being processed.";
            } else {
                $order_message = "error:Failed to place order. Please try again.";
            }
        } else {
            $order_message = "error:Selected food item is no longer available or quantity is insufficient.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SaveEat - Order Food & Reduce Waste</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        /* Custom styles for the ordering page */
        .order-body {
            background: linear-gradient(135deg, #0f172a, #1e293b, #334155);
            color: #fff;
            min-height: 100vh;
            font-family: "Poppins", sans-serif;
        }
        
        .header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            padding: 1rem 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.5rem;
            font-weight: bold;
            color: #38bdf8;
        }
        
        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        
        .nav-links a {
            color: #e2e8f0;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .nav-links a:hover {
            color: #38bdf8;
        }
        
        .admin-login {
            background: #38bdf8;
            color: #0f172a;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: bold;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .admin-login:hover {
            background: #0ea5e9;
            color: #0f172a;
        }
        
        .hero {
            text-align: center;
            padding: 4rem 2rem;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #38bdf8, #0ea5e9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .hero p {
            font-size: 1.2rem;
            color: #cbd5e1;
            margin-bottom: 2rem;
        }
        
        .food-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .food-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(15px);
            border-radius: 15px;
            padding: 1.5rem;
            border: 1px solid rgba(56, 189, 248, 0.2);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .food-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(56, 189, 248, 0.2);
        }
        
        .food-header {
            display: flex;
            justify-content: between;
            align-items: start;
            margin-bottom: 1rem;
        }
        
        .food-title {
            color: #38bdf8;
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
        }
        
        .hotel-name {
            color: #94a3b8;
            font-size: 0.9rem;
        }
        
        .food-details {
            margin-bottom: 1.5rem;
        }
        
        .food-details p {
            margin: 0.5rem 0;
            color: #e2e8f0;
        }
        
        .food-details i {
            color: #38bdf8;
            margin-right: 8px;
            width: 16px;
        }
        
        .price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #10b981;
            margin: 1rem 0;
        }
        
        .order-form {
            background: rgba(255, 255, 255, 0.05);
            padding: 1.5rem;
            border-radius: 10px;
            margin-top: 1rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #cbd5e1;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #38bdf8;
        }
        
        .quantity-selector {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1rem 0;
        }
        
        .quantity-btn {
            background: #38bdf8;
            border: none;
            color: #0f172a;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            font-weight: bold;
        }
        
        .quantity-input {
            width: 60px !important;
            text-align: center;
        }
        
        .order-btn {
            width: 100%;
            padding: 12px;
            background: #10b981;
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .order-btn:hover {
            background: #059669;
        }
        
        .order-btn:disabled {
            background: #64748b;
            cursor: not-allowed;
        }
        
        .message {
            padding: 12px;
            border-radius: 8px;
            margin: 1rem 0;
            text-align: center;
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
        
        .stats {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin: 3rem 0;
            flex-wrap: wrap;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.08);
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            min-width: 150px;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #38bdf8;
        }
        
        .stat-label {
            color: #cbd5e1;
            font-size: 0.9rem;
        }
        
        .no-food {
            text-align: center;
            padding: 3rem;
            color: #94a3b8;
        }
        
        .no-food i {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        @media (max-width: 768px) {
            .nav {
                flex-direction: column;
                gap: 1rem;
            }
            
            .hero h1 {
                font-size: 2rem;
            }
            
            .food-grid {
                grid-template-columns: 1fr;
                padding: 1rem;
            }
        }
    </style>
</head>
<body class="order-body">
    <!-- Header -->
    <header class="header">
        <nav class="nav">
            <div class="logo">
                <i class="fa-solid fa-utensils"></i>
                <span>SaveEat</span>
            </div>
            <div class="nav-links">
                <a href="#food">Available Food</a>
                <a href="#how-it-works">How It Works</a>
                <a href="login.php" class="admin-login">
                    <i class="fa-solid fa-lock"></i> Admin Login
                </a>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <h1>Reduce Food Waste, Save Money</h1>
        <p>Order delicious surplus food from local hotels and restaurants at discounted prices. Help reduce food waste while enjoying great meals!</p>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?= count($food_items) ?></div>
                <div class="stat-label">Available Items</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">24h</div>
                <div class="stat-label">Fresh Food</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">50%</div>
                <div class="stat-label">Average Savings</div>
            </div>
        </div>
    </section>

    <!-- Available Food Section -->
    <section id="food" class="food-section">
        <h2 style="text-align: center; margin-bottom: 2rem; color: #38bdf8;">Available Food Items</h2>
        
        <?php if (!empty($order_message)): ?>
            <?php 
            $message_type = strpos($order_message, 'success:') === 0 ? 'success' : 'error';
            $message_text = str_replace(['success:', 'error:'], '', $order_message);
            ?>
            <div class="message <?= $message_type ?>" style="max-width: 800px; margin: 0 auto 2rem auto;">
                <i class="fa-solid fa-<?= $message_type === 'success' ? 'circle-check' : 'circle-exclamation' ?>"></i> 
                <?= htmlspecialchars($message_text) ?>
            </div>
        <?php endif; ?>

        <div class="food-grid">
            <?php if (!empty($food_items)): ?>
                <?php foreach ($food_items as $item): ?>
                    <div class="food-card">
                        <div class="food-header">
                            <div>
                                <h3 class="food-title"><?= htmlspecialchars($item['title']) ?></h3>
                                <?php if (!empty($item['hotel_name'])): ?>
                                    <p class="hotel-name">from <?= htmlspecialchars($item['hotel_name']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="food-details">
                            <p><i class="fa-solid fa-utensils"></i> <?= htmlspecialchars($item['food_type']) ?></p>
                            <p><i class="fa-solid fa-box"></i> <?= htmlspecialchars($item['portion_size']) ?></p>
                            <p><i class="fa-solid fa-layer-group"></i> <?= $item['quantity'] ?> available</p>
                            <p><i class="fa-solid fa-clock"></i> Expires: <?= date('M j, g:i A', strtotime($item['expires_at'])) ?></p>
                            <?php if (!empty($item['storage_instructions'])): ?>
                                <p><i class="fa-solid fa-info-circle"></i> <?= htmlspecialchars($item['storage_instructions']) ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="price">KES <?= number_format($item['price'], 2) ?></div>
                        
                        <div class="order-form">
                            <form method="POST" action="">
                                <input type="hidden" name="food_item_id" value="<?= $item['id'] ?>">
                                
                                <div class="form-group">
                                    <label for="name_<?= $item['id'] ?>">Your Name *</label>
                                    <input type="text" id="name_<?= $item['id'] ?>" name="customer_name" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="phone_<?= $item['id'] ?>">Phone Number *</label>
                                    <input type="tel" id="phone_<?= $item['id'] ?>" name="customer_phone" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email_<?= $item['id'] ?>">Email Address *</label>
                                    <input type="email" id="email_<?= $item['id'] ?>" name="customer_email" required>
                                </div>
                                
                                <div class="quantity-selector">
                                    <label>Quantity:</label>
                                    <button type="button" class="quantity-btn minus" data-item="<?= $item['id'] ?>">-</button>
                                    <input type="number" class="quantity-input" name="quantity" value="1" min="1" max="<?= $item['quantity'] ?>" data-item="<?= $item['id'] ?>">
                                    <button type="button" class="quantity-btn plus" data-item="<?= $item['id'] ?>">+</button>
                                </div>
                                
                                <button type="submit" name="place_order" class="order-btn" 
                                        <?= $item['quantity'] == 0 ? 'disabled' : '' ?>>
                                    <i class="fa-solid fa-cart-plus"></i> 
                                    <?= $item['quantity'] == 0 ? 'Out of Stock' : 'Place Order' ?>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-food">
                    <i class="fa-solid fa-inbox"></i>
                    <h3>No Food Available Right Now</h3>
                    <p>Check back later for delicious surplus food from our partner hotels and restaurants!</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" style="padding: 4rem 2rem; background: rgba(255,255,255,0.05); margin-top: 4rem;">
        <div style="max-width: 800px; margin: 0 auto; text-align: center;">
            <h2 style="color: #38bdf8; margin-bottom: 2rem;">How It Works</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem;">
                <div>
                    <i class="fa-solid fa-magnifying-glass" style="font-size: 2rem; color: #38bdf8; margin-bottom: 1rem;"></i>
                    <h3>Browse Food</h3>
                    <p style="color: #cbd5e1;">Explore available surplus food from local businesses</p>
                </div>
                <div>
                    <i class="fa-solid fa-cart-shopping" style="font-size: 2rem; color: #38bdf8; margin-bottom: 1rem;"></i>
                    <h3>Place Order</h3>
                    <p style="color: #cbd5e1;">Select items and provide your contact details</p>
                </div>
                <div>
                    <i class="fa-solid fa-truck" style="font-size: 2rem; color: #38bdf8; margin-bottom: 1rem;"></i>
                    <h3>Pick Up</h3>
                    <p style="color: #cbd5e1;">Collect your order at the specified location</p>
                </div>
                <div>
                    <i class="fa-solid fa-earth-africa" style="font-size: 2rem; color: #38bdf8; margin-bottom: 1rem;"></i>
                    <h3>Save the Planet</h3>
                    <p style="color: #cbd5e1;">Help reduce food waste and environmental impact</p>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Quantity selector functionality
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.quantity-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.getAttribute('data-item');
                    const input = document.querySelector(`.quantity-input[data-item="${itemId}"]`);
                    const max = parseInt(input.getAttribute('max'));
                    let value = parseInt(input.value);
                    
                    if (this.classList.contains('plus') && value < max) {
                        input.value = value + 1;
                    } else if (this.classList.contains('minus') && value > 1) {
                        input.value = value - 1;
                    }
                });
            });
            
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