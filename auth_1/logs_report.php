<?php
require_once '../db_connection.php';
session_start();

// Check if user is logged in and has a college_id
if (!isset($_SESSION['user_id'])) {
    header("Location: ../landing/index.php");
    exit();
}

// Get the logged-in user's college_id
$current_user_id = $_SESSION['user_id'];
$user_query = "SELECT college_id FROM users WHERE user_id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$current_user = $user_result->fetch_assoc();

if (!$current_user || !isset($current_user['college_id'])) {
    die("Error: Unable to determine user's college");
}

$current_college_id = $current_user['college_id'];

// Fetch the college name for the current user
$college_query = "SELECT college_name FROM colleges WHERE college_id = ?";
$stmt = $conn->prepare($college_query);
$stmt->bind_param("i", $current_college_id);
$stmt->execute();
$college_result = $stmt->get_result();
$college_row = $college_result->fetch_assoc();
$current_college_name = $college_row['college_name'];

// Modified query to better handle login/logout pairs
$login_logs_query = "SELECT 
                        u.faculty_id, 
                        f.full_name AS name, 
                        l.login_time AS login_time,
                        l.logout_time AS logout_time,
                        CASE 
                            WHEN l.session_status = 'active' THEN 'LOG IN (Active)'
                            WHEN l.session_status = 'completed' THEN 'LOG IN'
                            WHEN l.session_status = 'timeout' THEN 'LOG IN (Timeout)'
                            ELSE 'LOG IN'
                        END AS login_action,
                        CASE 
                            WHEN l.session_status = 'completed' THEN 'LOG OUT'
                            WHEN l.session_status = 'timeout' THEN 'TIMEOUT'
                            ELSE NULL
                        END AS logout_action,
                        TIMESTAMPDIFF(MINUTE, l.login_time, IFNULL(l.logout_time, NOW())) AS session_duration,
                        l.ip_address,
                        l.session_status
                    FROM user_logins l
                    JOIN users u ON l.user_id = u.user_id
                    JOIN faculty f ON u.faculty_id = f.faculty_id
                    WHERE f.college_id = ?
                    ORDER BY l.login_time DESC";

$stmt = $conn->prepare($login_logs_query);
$stmt->bind_param("i", $current_college_id);
$stmt->execute();
$login_logs_result = $stmt->get_result();

// Credential logs query
$document_logs_query = "SELECT 
                            'credential' AS doc_category,
                            c.credential_id AS doc_id,
                            f.faculty_id,
                            f.full_name AS name,
                            c.credential_name AS doc_name,
                            c.credential_type AS doc_type,
                            c.uploaded_at,
                            c.status,
                            c.verified_at,
                            c.reason,
                            NULL AS semester,
                            NULL AS academic_year,
                            NULL AS total_loads
                        FROM credentials c
                        JOIN faculty f ON c.faculty_id = f.faculty_id
                        WHERE f.college_id = ?
                        
                        UNION ALL
                        
                        SELECT 
                            'teaching_load' AS doc_category,
                            t.load_id AS doc_id,
                            f.faculty_id,
                            f.full_name AS name,
                            t.file_name AS doc_name,
                            'Teaching Load' AS doc_type,
                            t.created_at AS uploaded_at,
                            t.status,
                            t.verified_at,
                            t.reason,
                            t.semester,
                            CONCAT(t.start_year, '-', t.end_year) AS academic_year,
                            t.total_loads
                        FROM teaching_load t
                        JOIN faculty f ON t.faculty_id = f.faculty_id
                        WHERE f.college_id = ?
                        
                        ORDER BY uploaded_at DESC";

$stmt = $conn->prepare($document_logs_query);
$stmt->bind_param("ii", $current_college_id, $current_college_id);
$stmt->execute();
$document_logs_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Activity Logs | Admin</title>
  <link rel="icon" type="image/png" href="../images/logo.png">
  <link rel="stylesheet" href="../css/admin_style.css?v=<?php echo time(); ?>"/>
  <link rel="stylesheet" href="../css/report.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/help.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="report-page">

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
          <li><a href="college_management.php"><img src="../images/department.png" alt="Department Icon" class="menu-icon">COLLEGE MANAGEMENT</a></li>
          <li><a href="user.php"><img src="../images/user.png" alt="User Icon" class="menu-icon">USER MANAGEMENT</a></li>
          <li class="dropdown">
            <a href="javascript:void(0)" id="reportsDropdown" class="active">
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
                    <a href="logs_report.php" class="active">
                        <i class="fas fa-user-clock"></i> USER LOGS
                    </a>
                </li>
            </ul>
          </li>
          <li><a href="setting.php"><img src="../images/setting.png" alt="Settings Icon" class="menu-icon">SETTINGS</a></li>
        </ul>
    </nav>

        <div class="logout-section">
        <a href="javascript:void(0)" onclick="confirmLogout()">
            <img src="../images/logout.png" alt="Logout Icon" class="menu-icon">LOGOUT
        </a>
        </div>
  </div>

  <div id="main" class="main-content">
    <div class="report-header">
        <h1 class="activity-logs-title">
            <i class="fas fa-history"></i>
            User Activity Logs - <?= htmlspecialchars($current_college_name) ?>
        </h1>
    </div>

<div class="report-container">
  <div class="log-tabs">
    <div class="log-tab active" onclick="switchTab('login-logs')">Login/Logout Logs</div>
    <div class="log-tab" onclick="switchTab('credential-logs')">Document Upload Logs</div>
    <div class="search-box log-search-box">
      <i class="fas fa-search"></i>
      <input type="text" id="searchInput" placeholder="Search faculty...">
    </div>
  </div>
      
      <!-- Login/Logout Logs -->
      <div id="login-logs" class="log-content active">
        <table class="report-table">
            <thead>
                <tr>
                    <th>Faculty ID</th>
                    <th>Name</th>
                    <th>Login Time</th>
                    <th>Logout Time</th>
                    <th>Action</th>
                    <th>Session Duration (Minutes)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $login_logs_result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['faculty_id']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars(date('d/m/Y h:iA', strtotime($row['login_time']))) ?></td>
                    <td>
                        <?= $row['logout_time'] ? 
                            htmlspecialchars(date('d/m/Y h:iA', strtotime($row['logout_time']))) : 
                            'Still logged in' ?>
                    </td>
                    <td>
                        <?= $row['logout_time'] ? 'LOG OUT' : $row['login_action'] ?>
                    </td>
                    <td class="session-duration">
                        <?= htmlspecialchars($row['session_duration']) ?>
                    </td>
                    <td>
                      <span class="session-status-badge 
                        <?php
                          if ($row['session_status'] === 'active') echo 'session-status-active';
                          elseif ($row['session_status'] === 'completed') echo 'session-status-completed';
                          elseif ($row['session_status'] === 'timeout') echo 'session-status-timeout';
                        ?>">
                        <?= htmlspecialchars(strtoupper($row['session_status'])) ?>
                      </span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
      </div>

      <!-- Credentials Status Logs -->
<div id="credential-logs" class="log-content">
    <table class="report-table">
        <thead>
            <tr>
                <th>Faculty ID</th>
                <th>Name</th>
                <th>Document Category</th>
                <th>Document Name</th>
                <th>Document Type</th>
                <th>Semester</th>
                <th>Academic Year</th>
                <th>Loads</th>
                <th>Date Submitted</th>
                <th>Status</th>
                <th>Date Verified</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $document_logs_result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['faculty_id']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $row['doc_category']))) ?></td>
                <td><?= htmlspecialchars($row['doc_name']) ?></td>
                <td><?= htmlspecialchars($row['doc_type']) ?></td>
                <td><?= $row['semester'] ? htmlspecialchars($row['semester']) : 'N/A' ?></td>
                <td><?= $row['academic_year'] ? htmlspecialchars($row['academic_year']) : 'N/A' ?></td>
                <td><?= $row['total_loads'] ? htmlspecialchars($row['total_loads']) : 'N/A' ?></td>
                <td><?= htmlspecialchars(date('d/m/Y h:iA', strtotime($row['uploaded_at']))) ?></td>
                <td>
                    <span class="status-badge <?= strtolower($row['status']) ?>">
                        <?= htmlspecialchars($row['status']) ?>
                    </span>
                    <?php if ($row['status'] === 'Rejected' && !empty($row['reason'])): ?>
                        <span class="reason-tooltip" title="<?= htmlspecialchars($row['reason']) ?>">
                            <i class="fas fa-info-circle"></i>
                        </span>
                    <?php endif; ?>
                </td>
                <td>
                    <?= $row['verified_at'] ? 
                        htmlspecialchars(date('d/m/Y h:iA', strtotime($row['verified_at']))) : 
                        'Not verified yet' ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

  <?php include '../auth_2/help.php'; ?>

  <script src="report.js?v=<?php echo time(); ?>"></script>
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
    });

              // Search functionality
          document.getElementById('searchInput').addEventListener('keyup', function() {
              const input = this.value.toLowerCase();
              const rows = document.querySelectorAll('.report-table tbody tr');
              
              rows.forEach(row => {
                  const text = row.textContent.toLowerCase();
                  row.style.display = text.includes(input) ? '' : 'none';
              });
          });
    
        function confirmLogout() {
            if (confirm('Are you sure you want to logout?')) {
                // Change this to point to your process_logout.php
                window.location.href = '../login/process_logout.php';
            }
        }
    
    // Tab switching functionality
    function switchTab(tabId) {
      // Hide all content
      document.querySelectorAll('.log-content').forEach(content => {
        content.classList.remove('active');
      });
      
      // Deactivate all tabs
      document.querySelectorAll('.log-tab').forEach(tab => {
        tab.classList.remove('active');
      });
      
      // Activate selected tab and content
      document.getElementById(tabId).classList.add('active');
      event.currentTarget.classList.add('active');
    }
  </script>
  <script src="../auth_2/help.js?v=<?php echo time(); ?>"></script>
</body>
</html>