<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | Faculty</title>
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="stylesheet" href="../css/faculty_style.css?v=<?php echo time(); ?>"/>
    <link rel="stylesheet" href="../css/help.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/themes.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/settings.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="theme.js?v=<?php echo time(); ?>"></script>
    <script>
    // Function to remove selected class from all buttons
    function clearSelectedButtons() {
        document.querySelectorAll('.settings-btn').forEach(btn => {
            btn.classList.remove('selected');
        });
    }

    function updateSelected() {
        // Clear all selected states first
        clearSelectedButtons();
        
        // Text size buttons
        const currentSize = parseInt(localStorage.getItem('plpTextSize')) || 100;
        const sizeBtn = document.getElementById('size-' + currentSize);
        if (sizeBtn) {
            sizeBtn.classList.add('selected');
        }
        
        // Theme buttons
        const currentTheme = localStorage.getItem('plpTheme') || 'light';
        const themeBtn = document.getElementById('theme-' + currentTheme);
        if (themeBtn) {
            themeBtn.classList.add('selected');
        }
    }
    
    // Initialize theme highlighting on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateSelected();
    });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var coll = document.getElementsByClassName("collapsible");
        for (var i = 0; i < coll.length; i++) {
            coll[i].addEventListener("click", function() {
                this.classList.toggle("active");
                var content = this.nextElementSibling;
                if (content.style.maxHeight) {
                    content.style.maxHeight = null;
                } else {
                    content.style.maxHeight = content.scrollHeight + "px";
                }
            });
        }
    });
    </script>
</head>
<body>
    <div class="header">
        <div class="logo-section">
            <img src="../images/logo.png" alt="Logo" />
            <div class="title">PAMANTASAN NG LUNGSOD NG PASIG</div>
            <button class="hamburger" onclick="toggleMenu()">
                <div id="bar1" class="bar"></div>
                <div id="bar2" class="bar"></div>
                <div id="bar3" class="bar"></div>
            </button>
        </div>
    </div>
        
    <div class="navigation" id="menu">
    <div class="navigation-header">
        <h1>FACULTY</h1>
      <h2>| PLP FACULTY PROFILING SYSTEM |</h2>
    </div>
        
    <nav>
      <ul>
        <li><a href="home.php"><img src="../images/home.png" alt="Home Icon" class="menu-icon">HOME</a></li>
        <li><a href="profile.php"><img src="../images/profile.png" alt="Profile Icon" class="menu-icon">PROFILE</a></li>
        <li><a href="teachingload.php"><img src="../images/teachingload.png" alt="Teaching Icon" class="menu-icon">TEACHING LOAD</a></li>
        <li><a href="credentials.php"><img src="../images/credentials.png" alt="Credentials Icon" class="menu-icon">CREDENTIALS</a></li>
        <li><a href="setting.php" class="active"><img src="../images/setting.png" alt="Settings Icon" class="menu-icon">SETTINGS</a></li>
      </ul>
    </nav>

    <div class="logout-section">
          <a href="#" onclick="confirmLogout()"><img src="../images/logout.png" alt="Logout Icon" class="menu-icon">LOGOUT</a>
        </div>
    </div> 

    <div id="main" class="main-content">
        <?php include 'help.php'; ?>
        <h2 class="settings-title"><i class="fas fa-sliders-h faculty-settings-icon"></i> Settings</h2>
        <hr>
        <div class="settings-section">
            <button type="button" class="collapsible">Text Size</button>
            <div class="content">
                <div class="settings-options">
                    <button class="settings-btn" id="size-100" onclick="setTextSize(100)">100%</button>
                    <button class="settings-btn" id="size-150" onclick="setTextSize(150)">150%</button>
                    <button class="settings-btn" id="size-200" onclick="setTextSize(200)">200%</button>
                </div>
            </div>
        </div>
        <hr>
        <div class="settings-section">
            <button type="button" class="collapsible">Theme</button>
            <div class="content">
                <div class="settings-options">
                    <button class="settings-btn" id="theme-light" onclick="setTheme('light')">Light</button>
                    <button class="settings-btn" id="theme-dark" onclick="setTheme('dark')">Dark</button>
                </div>
            </div>
        </div>
        <hr>
        <div class="settings-section">
            <button type="button" class="collapsible">Change Password</button>
            <div class="content">
                <div id="passwordMessage" class="message-box" aria-live="polite"></div>
                <form id="changePasswordForm" class="password-form">
                    <div class="form-field">
                        <label for="currentPassword">Current Password</label>
                        <div class="input-wrapper">
                            <input 
                                type="password" 
                                id="currentPassword" 
                                required 
                                autocomplete="current-password"
                                aria-describedby="currentPasswordHint" 
                            />
                            <button 
                                type="button" 
                                class="toggle-password" 
                                aria-label="Toggle current password visibility"
                                onclick="togglePasswordVisibility('currentPassword', this)"
                            >
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                        <small id="currentPasswordHint" class="hint">Enter your current password</small>
                    </div>

                    <div class="form-field">
                        <label for="newPassword">New Password</label>
                        <div class="input-wrapper">
                            <input 
                                type="password" 
                                id="newPassword" 
                                required 
                                autocomplete="new-password"
                                aria-describedby="newPasswordHint passwordStrength" 
                            />
                            <div class="password-tooltip" id="passwordTooltip">
                                <ul>
                                    <li>At least 8 characters long</li>
                                    <li>Include at least one uppercase letter</li>
                                    <li>Include at least one lowercase letter</li>
                                    <li>Include at least one number</li>
                                    <li>Include at least one special character</li>
                                </ul>
                            </div>
                            <button 
                                type="button" 
                                class="toggle-password" 
                                aria-label="Toggle new password visibility"
                                onclick="togglePasswordVisibility('newPassword', this)"
                            >
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                        <small id="newPasswordHint" class="hint">Password must be at least 8 characters</small>
                        <div id="passwordStrength" class="password-strength">
                            <div class="strength-bar"></div>
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="confirmPassword">Confirm New Password</label>
                        <div class="input-wrapper">
                            <input 
                                type="password" 
                                id="confirmPassword" 
                                required 
                                autocomplete="new-password"
                                aria-describedby="confirmPasswordHint" 
                            />
                            <button 
                                type="button" 
                                class="toggle-password" 
                                aria-label="Toggle confirm password visibility"
                                onclick="togglePasswordVisibility('confirmPassword', this)"
                            >
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                        <small id="confirmPasswordHint" class="hint">Re-enter your new password</small>
                    </div>

                    <button type="submit" class="change-password-btn">
                        <i class="fas fa-key"></i> Change Password
                    </button>
                </form>
            </div>
        </div>
        <hr>
    </div>
    
    <script>
        function confirmLogout() {
            if (confirm('Are you sure you want to logout?')) {
                // Change this to point to your process_logout.php
                window.location.href = '../login/process_logout.php';
            }
        }
    </script>
    <script src="js/settings.js"></script>
    <script src="help.js"></script>
    <script src="change_password.js"></script>
    <script src="../scripts.js"></script>
</body>
</html>