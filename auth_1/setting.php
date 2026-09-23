<?php
session_start();
// Add this after session_start()
if (!isset($_SESSION['college_id']) && isset($_SESSION['user_id'])) {
    require_once '../db_connection.php';
    $stmt = $conn->prepare("SELECT college_id FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $_SESSION['college_id'] = $result->fetch_assoc()['college_id'];
    }
}
$current_college_id = $_SESSION['college_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Settings | Admin</title>
  <link rel="icon" type="image/png" href="../images/logo.png">
  <link rel="stylesheet" href="../css/admin_style.css?v=<?php echo time(); ?>" />
  <link rel="stylesheet" href="../css/help.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/themes.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/settings.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="../auth_2/theme.js?v=<?php echo time(); ?>"></script>
    <script>
    // Initialize states
    //let currentSize = parseInt(localStorage.getItem('plpTextSize')) || 100;

    // Check and apply theme and text size on page load
    document.addEventListener('DOMContentLoaded', function() {
      const savedTheme = localStorage.getItem('plpTheme') || 'light';
      if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
      }
      // Apply saved text size
      document.body.style.fontSize = currentSize + '%';
      updateSelected();
    });

    function applyTheme(theme) {
      if (theme === 'dark') {
        document.body.classList.add('dark-theme');
      } else {
        document.body.classList.remove('dark-theme');
      }
      localStorage.setItem('plpTheme', theme);
    }

    function setTextSize(size) {
      currentSize = size;
      document.body.style.fontSize = size + '%';
      localStorage.setItem('plpTextSize', size);
      updateSelected();
    }

    function setTheme(theme) {
      applyTheme(theme);
      updateSelected();
    }

    function updateSelected() {
      // Text size buttons
      [100, 150, 200].forEach(s => {
        document.getElementById('size-' + s).classList.toggle('selected', currentSize === s);
      });
      // Theme buttons
      const currentTheme = localStorage.getItem('plpTheme') || 'light';
      document.getElementById('theme-light').classList.toggle('selected', currentTheme === 'light');
      document.getElementById('theme-dark').classList.toggle('selected', currentTheme === 'dark');
    }
  </script>
  <style>
    /* Light theme (default) */
    body {
      background: #f4fff4;
      color: #187436;
      overflow-y: auto;           /* Enable vertical scrolling */
      scrollbar-width: none;      /* Firefox: hide scrollbar */
      -ms-overflow-style: none;   /* IE/Edge: hide scrollbar */
    }

    /* Chrome, Safari, Opera: hide scrollbar */
    body::-webkit-scrollbar {
      display: none;
    }

    /* Dark theme */
    body.dark-theme {
      background: #101010 !important;
      color: #f3f3f3 !important;
    }

    body.dark-theme .settings-label {
      color: #f3f3f3;
    }

    body.dark-theme .settings-btn {
      background: #222;
      color: #ccc;
    }

    body.dark-theme .settings-btn.selected {
      background: #00d34a;
      color: #101010;
    }

    body.dark-theme hr {
      border-top: 6px solid #333;
    }

    /* Dropdown Styles */
    nav ul li.dropdown {
      position: relative;
    }
    
    nav ul li.dropdown .dropdown-menu {
      display: none;
      position: relative;
      left: 0;
      min-width: 200px;
      z-index: 1000;
      padding: 0;
      margin: 0;
    }
    
    nav ul li.dropdown .dropdown-menu li {
      padding: 0;
      list-style: none;
    }
    
    nav ul li.dropdown .dropdown-menu a {
      color: white;
      padding: 12px 16px;
      text-decoration: none;
      display: block;
      font-size: 14px;
      font-family: 'Trebuchet MS';
    }
    
    nav ul li.dropdown .dropdown-menu a:hover {
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
      border-right: 3px solid #04b032;
      border-left: 3px solid #04b032;
      margin-right: 15px;
      padding-top: 10px;
      padding-bottom: 10px;
      background-color: #0e4301;
    }

    /* Collapsible sections from 2 setting.php */
    .collapsible {
      background: none;
      color: inherit;
      cursor: pointer;
      width: 100%;
      border: none;
      text-align: left;
      outline: none;
      font-size: 1.2rem;
      font-weight: bold;
      padding: 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border: 1px solid #187436;
      border-radius: 8px;
      margin-bottom: 0.5rem;
      transition: all 0.3s ease;
    }

    body.dark-theme .collapsible {
      border-color: #333;
      color: #f3f3f3;
    }

    .collapsible:hover {
      background: rgba(0, 211, 74, 0.1);
    }

    body.dark-theme .collapsible:hover {
      background: rgba(0, 211, 74, 0.2);
    }

    .collapsible:after {
      content: '\002B';
      font-weight: bold;
      float: right;
      margin-left: 5px;
      transition: transform 0.3s ease;
    }

    .collapsible.active:after {
      content: '\2212';
    }

    .content {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease-out;
      background: rgba(255, 255, 255, 0.05);
      border-radius: 0 0 8px 8px;
      padding: 0 1rem;
    }

    .content.active {
      max-height: 500px;
      padding: 1rem;
    }

    /* Password change form */
    .settings-input {
      display: block;
      margin: 0.5rem auto 1rem auto;
      width: 70%;
      padding: 0.7rem;
      border: 1px solid #187436;
      border-radius: 8px;
      background: #f4fff4;
      color: #187436;
}

    body.dark-theme .settings-input {
      background: #222;
      color: #f3f3f3;
      border-color: #333;
    }

    .settings-input:focus {
      outline: none;
      border-color: #00d34a;
    }

    #passwordMessage {
      display: none;
      margin-bottom: 1rem;
      padding: 1rem;
      border-radius: 4px;
    }
  </style>
</head>
<body class="setting-page">
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
      <h1>ADMINISTRATOR</h1>
      <h2>| PLP FACULTY PROFILING SYSTEM |</h2>
    </div>

    <nav>
      <ul>
        <li><a href="home.php"><img src="../images/home.png" alt="Home Icon" class="menu-icon">HOME</a></li>
        <li><a href="college_management.php"><img src="../images/department.png" alt="Department Icon" class="menu-icon">DEPARTMENT MANAGEMENT</a></li>
        <li><a href="user.php"><img src="../images/user.png" alt="User Icon" class="menu-icon">USER MANAGEMENT</a></li>
        <li class="dropdown">
          <a href="javascript:void(0)" id="reportsDropdown">
            <img src="../images/reports.png" alt="Reports Icon" class="menu-icon">
            REPORTS
            <i class="fas fa-chevron-down down-icon" id="dropdownArrow"></i>
          </a>
          <ul class="dropdown-menu">
            <li>
              <a href="files_report.php">
                <i class="fas fa-file-alt"></i> DOCUMENT FILES
              </a>
            </li>
            <li>
              <a href="logs_report.php">
                <i class="fas fa-user-clock"></i> USER LOGS
              </a>
            </li>
          </ul>
        </li>
        <li><a href="setting.php" class="active"><img src="../images/setting.png" alt="Settings Icon" class="menu-icon">SETTINGS</a></li>
      </ul>
    </nav>

    <div class="logout-section">
      <a href="#" onclick="confirmLogout()"><img src="../images/logout.png" alt="Logout Icon" class="menu-icon">LOGOUT</a>
    </div>
  </div>


<div id="main" class="main-content">
    <h2 class="settings-title"><i class="fas fa-cog admin-settings-icon"></i> Admin Settings</h2>
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
        <div id="passwordMessage" class="message-box" style="display: none; margin-bottom: 1rem; padding: 1rem; border-radius: 4px;"></div>
        <form id="changePasswordForm" class="settings-options" style="flex-direction: column;">
          <input type="password" id="currentPassword" placeholder="Current Password" class="settings-input" required />
          <input type="password" id="newPassword" placeholder="New Password" class="settings-input" required />
          <input type="password" id="confirmPassword" placeholder="Confirm New Password" class="settings-input" required />
          <button type="submit" class="settings-btn" style="align-self: flex-start;">Change Password</button>
        </form>
      </div>
    </div>
<hr>
<div class="settings-section">
    <button type="button" class="collapsible">Add Faculty User</button>
    <div class="content">
        <form id="addFacultyForm" class="settings-options" style="flex-direction: column; width:100%;">
            <input type="text" class="settings-input" name="faculty_id" placeholder="Faculty ID (format: XX-XXXXX)" required />
            <input type="hidden" name="college_id" value="<?php echo $current_college_id; ?>">
            <input type="text" class="settings-input" name="full_name" placeholder="Full Name" required />
            <input type="email" class="settings-input" name="email" placeholder="Email" required />
            <select class="settings-input" name="employment_type" required>
                <option value="">Select Employment Type</option>
                <option value="Full-Time">Full-Time</option>
                <option value="Part-Time">Part-Time</option>
            </select>
            <input type="text" class="settings-input" name="specialization" placeholder="Specialization" />
            <input type="text" class="settings-input" name="contact_number" placeholder="Contact Number" />
            <select class="settings-input" name="status" required>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>
            <button type="submit" class="settings-btn" style="align-self: flex-start;">Add Faculty</button>
                    <div id="addFacultyMessage" class="message-box" style="display: none; margin-top: 1rem; padding: 1rem; border-radius: 4px;"></div>
        </form>
    </div>
</div>
<hr>
  </div>

  <?php include '../auth_2/help.php'; ?>

  <script>
  const CURRENT_COLLEGE_ID = <?php echo json_encode($current_college_id ?? null); ?>;
</script>

  <script src="scripts.js?v=<?php echo time(); ?>"></script>
  <script>
    // Reports dropdown functionality
    document.addEventListener('DOMContentLoaded', function() {
      document.getElementById('reportsDropdown').addEventListener('click', function(e) {
        e.preventDefault();
        const dropdown = this.closest('.dropdown');
        const menu = dropdown.querySelector('.dropdown-menu');
        dropdown.classList.toggle('open');
        // Toggle menu display
        if (menu.style.display === 'block') {
          menu.style.display = 'none';
        } else {
          // Close all other dropdowns first
          document.querySelectorAll('.dropdown-menu').forEach(item => {
            if (item !== menu) {
              item.style.display = 'none';
              item.closest('.dropdown').classList.remove('open');
            }
          });
          menu.style.display = 'block';
        }
      });

      // Close dropdown when clicking outside
      document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
          document.querySelectorAll('.dropdown-menu').forEach(item => {
            item.style.display = 'none';
            item.closest('.dropdown').classList.remove('open');
          });
        }
      });

      // Collapsible functionality
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

    function confirmLogout() {
      if (confirm('Are you sure you want to logout?')) {
        // Change this to point to your process_logout.php
        window.location.href = '../login/process_logout.php';
      }
    }
  </script>
    <script src="../auth_2/change_password.js"></script>
    <script src="../auth_2/help.js?v=<?php echo time(); ?>"></script>
</body>
</html>