<?php
session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Simple check — replace with real DB check later if needed
    if ($username === 'admin' && $password === '5678') {
        $_SESSION['logged_in'] = true;
        header('Location: dashboard.php');
        exit(); // ✅ always exit after header redirect
    } else {
        $message = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SaveEat Initiative - Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="loader">
        <div class="spinner"></div>
    </div>

    <div class="login-wrapper">
        <div class="login-card" id="loginCard">
            <h2>SaveEat Foundation</h2>

            <?php if ($message && $message !== 'success'): ?>
                <div class="error"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <form id="loginForm" method="post" action="">
                <input type="text" name="username" placeholder="Username" required autofocus>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" class="btn">Login</button>
            </form>
        </div>
    </div>

    <script>
        const loginStatus = <?= json_encode($message) ?>;
    </script>
    <script src="js/login.js"></script>
</body>
</html>
