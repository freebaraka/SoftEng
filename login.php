<?php
session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $conn = new mysqli('localhost','root','munyoiks7','SaveEat');
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT id, role, password hash FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if ($user['role'] !== 'admin') {
            $message = "Only admins can login here.";
        } elseif ($password === $user['password']) {
            $_SESSION['logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $username;
            header("Location: dashboard.php");
            exit;
        } else {
            $message = "Incorrect password.";
        }
    } else {
        $message = "Admin not found.";
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
