<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - SaveEat Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Your existing CSS with additions for settings page */
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #0f172a, #1e293b, #334155);
            color: #fff;
            overflow: hidden;
            overflow-y: scroll; /* Always show vertical scrollbar */
        }

        /* Dashboard Enhanced Styles */
        .dashboard-body {
            background: linear-gradient(135deg, #0f172a, #1e293b, #334155);
            color: #fff;
            height: 100vh;
            margin: 0;
            display: flex;
            font-family: "Poppins", sans-serif;
        }

        .dashboard-container {
            display: flex;
            width: 100%;
        }

        /* Sidebar */
        .sidebar {
            width: 230px;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            padding: 2rem 1rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            transition: width 0.3s ease;
        }

        .sidebar h2 {
            text-align: center;
            color: #38bdf8;
            font-size: 1.4rem;
            margin-bottom: 1.5rem;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar li, .sidebar a.logout {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            margin: 8px 0;
            border-radius: 8px;
            transition: background 0.3s, transform 0.2s;
            cursor: pointer;
            color: #e2e8f0;
            text-decoration: none;
        }

        .sidebar li:hover,
        .sidebar li.active,
        .sidebar a.logout:hover {
            background: rgba(56, 189, 248, 0.3);
            transform: translateX(5px);
            color: #fff;
        }

        .sidebar .logout {
            color: #f87171;
        }

        .sidebar .logout:hover {
            background: rgba(248, 113, 113, 0.2);
        }

        /* Main content */
        .main-content {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
        }

        .main-content header h1 {
            font-size: 1.8rem;
            margin-bottom: 0.3rem;
        }

        .main-content header p {
            color: #94a3b8;
        }

        /* Settings Page Specific Styles */
        .settings-container {
            margin-top: 2rem;
        }

        .settings-tabs {
            display: flex;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 2rem;
        }

        .tab-btn {
            padding: 12px 24px;
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s;
            border-bottom: 2px solid transparent;
        }

        .tab-btn.active {
            color: #38bdf8;
            border-bottom: 2px solid #38bdf8;
        }

        .tab-btn:hover:not(.active) {
            color: #e2e8f0;
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .tab-content.active {
            display: block;
        }

        .settings-card {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .settings-card h3 {
            color: #38bdf8;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #cbd5e1;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #38bdf8;
            background: rgba(255, 255, 255, 0.15);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 1rem;
        }

        .checkbox-group input {
            width: auto;
        }

        .checkbox-group label {
            margin-bottom: 0;
        }

        .btn {
            padding: 12px 24px;
            background: #38bdf8;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            color: #0f172a;
            cursor: pointer;
            transition: background 0.3s;
            font-size: 1rem;
        }

        .btn:hover {
            background: #0ea5e9;
        }

        .btn-danger {
            background: #ef4444;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-secondary {
            background: #64748b;
        }

        .btn-secondary:hover {
            background: #475569;
        }

        .btn-group {
            display: flex;
            gap: 12px;
            margin-top: 1.5rem;
        }

        .notification-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #475569;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #38bdf8;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 1.5rem;
        }

        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #38bdf8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #0f172a;
        }

        .user-info h4 {
            color: #38bdf8;
            margin-bottom: 5px;
        }

        .user-info p {
            color: #94a3b8;
        }

        .danger-zone {
            border-left: 4px solid #ef4444;
        }

        .danger-zone h3 {
            color: #ef4444;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                height: auto;
                padding: 1rem;
            }
            
            .settings-tabs {
                flex-wrap: wrap;
            }
            
            .tab-btn {
                flex: 1;
                min-width: 120px;
            }
        }
    </style>
</head>
<body class="dashboard-body">
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div>
                <h2><i class="fas fa-utensils"></i> SaveEat</h2>
                <ul>
                    <li><a href="dashboard.html"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="partners.html"><i class="fas fa-handshake"></i> Partners</a></li>
                    <li><a href="food-listings.html"><i class="fas fa-hamburger"></i> Food Listings</a></li>
                    <li class="active"><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><a href="#"><i class="fas fa-chart-bar"></i> Analytics</a></li>
                    <li><a href="#"><i class="fas fa-users"></i> Users</a></li>
                </ul>
            </div>
            <a href="#" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <header>
                <h1>Settings</h1>
                <p>Manage your SaveEat platform configuration</p>
            </header>

            <div class="settings-container">
                <!-- Settings Tabs -->
                <div class="settings-tabs">
                    <button class="tab-btn active" data-tab="general">General</button>
                    <button class="tab-btn" data-tab="notifications">Notifications</button>
                    <button class="tab-btn" data-tab="security">Security</button>
                    <button class="tab-btn" data-tab="appearance">Appearance</button>
                    <button class="tab-btn" data-tab="advanced">Advanced</button>
                </div>

                <!-- General Settings -->
                <div id="general" class="tab-content active">
                    <div class="settings-card">
                        <h3><i class="fas fa-info-circle"></i> Platform Information</h3>
                        <div class="form-group">
                            <label for="platform-name">Platform Name</label>
                            <input type="text" id="platform-name" value="SaveEat" placeholder="Enter platform name">
                        </div>
                        <div class="form-group">
                            <label for="platform-description">Platform Description</label>
                            <textarea id="platform-description" rows="3" placeholder="Enter platform description">A platform connecting food providers with shelters and organizations to reduce food waste.</textarea>
                        </div>
                        <div class="form-group">
                            <label for="contact-email">Contact Email</label>
                            <input type="email" id="contact-email" value="contact@saveeat.com" placeholder="Enter contact email">
                        </div>
                    </div>

                    <div class="settings-card">
                        <h3><i class="fas fa-user-cog"></i> Profile Settings</h3>
                        <div class="user-profile">
                            <div class="avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="user-info">
                                <h4>Admin User</h4>
                                <p>Administrator</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="admin-name">Full Name</label>
                            <input type="text" id="admin-name" value="Admin User" placeholder="Enter your full name">
                        </div>
                        <div class="form-group">
                            <label for="admin-email">Email Address</label>
                            <input type="email" id="admin-email" value="admin@saveeat.com" placeholder="Enter your email">
                        </div>
                        <div class="btn-group">
                            <button class="btn">Save Changes</button>
                            <button class="btn btn-secondary">Cancel</button>
                        </div>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div id="notifications" class="tab-content">
                    <div class="settings-card">
                        <h3><i class="fas fa-bell"></i> Notification Preferences</h3>
                        <div class="notification-item">
                            <div>
                                <h4>New Partner Registrations</h4>
                                <p>Get notified when new partners register on the platform</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="notification-item">
                            <div>
                                <h4>New Food Listings</h4>
                                <p>Receive alerts when new food items are listed</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="notification-item">
                            <div>
                                <h4>Order Updates</h4>
                                <p>Get notified about order status changes</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="notification-item">
                            <div>
                                <h4>System Alerts</h4>
                                <p>Receive important system notifications</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="btn-group">
                            <button class="btn">Save Preferences</button>
                        </div>
                    </div>

                    <div class="settings-card">
                        <h3><i class="fas fa-envelope"></i> Email Notifications</h3>
                        <div class="checkbox-group">
                            <input type="checkbox" id="email-digest" checked>
                            <label for="email-digest">Weekly digest email</label>
                        </div>
                        <div class="checkbox-group">
                            <input type="checkbox" id="email-promotions">
                            <label for="email-promotions">Promotional emails</label>
                        </div>
                        <div class="checkbox-group">
                            <input type="checkbox" id="email-updates" checked>
                            <label for="email-updates">Platform updates</label>
                        </div>
                        <div class="btn-group">
                            <button class="btn">Update Email Settings</button>
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div id="security" class="tab-content">
                    <div class="settings-card">
                        <h3><i class="fas fa-shield-alt"></i> Password & Security</h3>
                        <div class="form-group">
                            <label for="current-password">Current Password</label>
                            <input type="password" id="current-password" placeholder="Enter current password">
                        </div>
                        <div class="form-group">
                            <label for="new-password">New Password</label>
                            <input type="password" id="new-password" placeholder="Enter new password">
                        </div>
                        <div class="form-group">
                            <label for="confirm-password">Confirm New Password</label>
                            <input type="password" id="confirm-password" placeholder="Confirm new password">
                        </div>
                        <div class="btn-group">
                            <button class="btn">Update Password</button>
                        </div>
                    </div>

                    <div class="settings-card">
                        <h3><i class="fas fa-user-lock"></i> Two-Factor Authentication</h3>
                        <p>Add an extra layer of security to your account</p>
                        <div class="checkbox-group">
                            <input type="checkbox" id="two-factor">
                            <label for="two-factor">Enable Two-Factor Authentication</label>
                        </div>
                        <div class="btn-group">
                            <button class="btn">Configure 2FA</button>
                        </div>
                    </div>

                    <div class="settings-card">
                        <h3><i class="fas fa-desktop"></i> Active Sessions</h3>
                        <p>Manage devices that are logged into your account</p>
                        <div class="notification-item">
                            <div>
                                <h4>Chrome on Windows</h4>
                                <p>Current session • Last active: Just now</p>
                            </div>
                            <button class="btn btn-danger">Log Out</button>
                        </div>
                        <div class="notification-item">
                            <div>
                                <h4>Safari on iPhone</h4>
                                <p>Last active: 2 days ago</p>
                            </div>
                            <button class="btn btn-danger">Log Out</button>
                        </div>
                    </div>
                </div>

                <!-- Appearance Settings -->
                <div id="appearance" class="tab-content">
                    <div class="settings-card">
                        <h3><i class="fas fa-palette"></i> Theme & Appearance</h3>
                        <div class="form-group">
                            <label for="theme-select">Theme</label>
                            <select id="theme-select">
                                <option value="dark" selected>Dark</option>
                                <option value="light">Light</option>
                                <option value="auto">Auto (System)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="accent-color">Accent Color</label>
                            <input type="color" id="accent-color" value="#38bdf8">
                        </div>
                        <div class="checkbox-group">
                            <input type="checkbox" id="compact-mode">
                            <label for="compact-mode">Compact Mode</label>
                        </div>
                        <div class="btn-group">
                            <button class="btn">Apply Changes</button>
                        </div>
                    </div>

                    <div class="settings-card">
                        <h3><i class="fas fa-language"></i> Language & Region</h3>
                        <div class="form-group">
                            <label for="language-select">Language</label>
                            <select id="language-select">
                                <option value="en" selected>English</option>
                                <option value="es">Spanish</option>
                                <option value="fr">French</option>
                                <option value="de">German</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="timezone-select">Timezone</label>
                            <select id="timezone-select">
                                <option value="utc-5" selected>UTC-5 (Eastern Time)</option>
                                <option value="utc-6">UTC-6 (Central Time)</option>
                                <option value="utc-7">UTC-7 (Mountain Time)</option>
                                <option value="utc-8">UTC-8 (Pacific Time)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="date-format">Date Format</label>
                            <select id="date-format">
                                <option value="mm/dd/yyyy" selected>MM/DD/YYYY</option>
                                <option value="dd/mm/yyyy">DD/MM/YYYY</option>
                                <option value="yyyy-mm-dd">YYYY-MM-DD</option>
                            </select>
                        </div>
                        <div class="btn-group">
                            <button class="btn">Save Preferences</button>
                        </div>
                    </div>
                </div>

                <!-- Advanced Settings -->
                <div id="advanced" class="tab-content">
                    <div class="settings-card">
                        <h3><i class="fas fa-database"></i> Data Management</h3>
                        <div class="form-group">
                            <label for="auto-backup">Automatic Backups</label>
                            <select id="auto-backup">
                                <option value="daily">Daily</option>
                                <option value="weekly" selected>Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="never">Never</option>
                            </select>
                        </div>
                        <div class="btn-group">
                            <button class="btn">Backup Now</button>
                            <button class="btn btn-secondary">Restore Backup</button>
                        </div>
                    </div>

                    <div class="settings-card">
                        <h3><i class="fas fa-code"></i> API Settings</h3>
                        <div class="form-group">
                            <label for="api-key">API Key</label>
                            <input type="text" id="api-key" value="sk_xxxxxxxxxxxxxxxxxxxxxxxx" readonly>
                        </div>
                        <div class="checkbox-group">
                            <input type="checkbox" id="api-access" checked>
                            <label for="api-access">Enable API Access</label>
                        </div>
                        <div class="btn-group">
                            <button class="btn">Generate New Key</button>
                            <button class="btn btn-secondary">API Documentation</button>
                        </div>
                    </div>

                    <div class="settings-card danger-zone">
                        <h3><i class="fas fa-exclamation-triangle"></i> Danger Zone</h3>
                        <p>These actions are irreversible. Please proceed with caution.</p>
                        <div class="btn-group">
                            <button class="btn btn-danger">Clear All Data</button>
                            <button class="btn btn-danger">Delete Account</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    // Remove active class from all buttons and contents
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));
                    
                    // Add active class to clicked button
                    button.classList.add('active');
                    
                    // Show corresponding tab content
                    const tabId = button.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                });
            });
            
            // Theme selector functionality
            const themeSelect = document.getElementById('theme-select');
            if (themeSelect) {
                themeSelect.addEventListener('change', function() {
                    // In a real application, you would save this preference and apply the theme
                    alert(`Theme changed to: ${this.value}`);
                });
            }
            
            // Password update functionality
            const updatePasswordBtn = document.querySelector('#security .btn');
            if (updatePasswordBtn) {
                updatePasswordBtn.addEventListener('click', function() {
                    const currentPassword = document.getElementById('current-password').value;
                    const newPassword = document.getElementById('new-password').value;
                    const confirmPassword = document.getElementById('confirm-password').value;
                    
                    if (!currentPassword || !newPassword || !confirmPassword) {
                        alert('Please fill in all password fields');
                        return;
                    }
                    
                    if (newPassword !== confirmPassword) {
                        alert('New passwords do not match');
                        return;
                    }
                    
                    // In a real application, you would send this to the server
                    alert('Password updated successfully');
                    
                    // Clear the fields
                    document.getElementById('current-password').value = '';
                    document.getElementById('new-password').value = '';
                    document.getElementById('confirm-password').value = '';
                });
            }
        });
    </script>
</body>
</html>