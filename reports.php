<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Maintenance | SaveEat</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            color: #f1f5f9;
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            text-align: center;
            flex-direction: column;
        }

        .maintenance-box {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px 60px;
            border-radius: 16px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            max-width: 450px;
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 15px;
            color: #38bdf8;
        }

        p {
            font-size: 1rem;
            color: #e2e8f0;
            margin-bottom: 20px;
        }

        .icon {
            font-size: 60px;
            color: #38bdf8;
            margin-bottom: 15px;
            animation: pulse 1.5s infinite alternate;
        }

        @keyframes pulse {
            from { transform: scale(1); opacity: 0.8; }
            to { transform: scale(1.1); opacity: 1; }
        }

        .footer {
            margin-top: 20px;
            font-size: 0.85rem;
            color: #94a3b8;
        }

        .back-btn {
            display: inline-block;
            background: #38bdf8;
            color: #0f172a;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: 0.3s ease;
        }

        .back-btn:hover {
            background: #0ea5e9;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="maintenance-box">
        <i class="fa-solid fa-screwdriver-wrench icon"></i>
        <h1>Under Maintenance</h1>
        <p>We're currently performing some updates and improvements.<br>
        This page will be available again shortly.</p>

        <a href="dashboard.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>

        <p class="footer">— SaveEat Team</p>
    </div>
</body>
</html>
