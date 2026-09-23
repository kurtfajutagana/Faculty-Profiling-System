<?php
session_start();
require_once '../db_connection.php';

// Get admin info - FIXED: Explicitly specify users.username
$adminName = "ADMIN";
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $adminName = strtoupper(str_replace('_', ' ', $user['username']));
}

// Get college_id of the logged in admin
$college_id = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT college_id FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $college_id = $user['college_id'];
}

// Initialize faculty status variables
$activeFaculty = 0;
$inactiveFaculty = 0;
$totalFaculty = 0;
$activePercent = 0;
$inactivePercent = 0;

// Get active/inactive faculty counts
if ($college_id) {
    // Count active faculty
    $stmt = $conn->prepare("SELECT COUNT(*) FROM faculty WHERE college_id = ? AND status = 'Active'");
    $stmt->bind_param("i", $college_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_row();
    $activeFaculty = $row[0];
    
    // Count inactive faculty
    $stmt = $conn->prepare("SELECT COUNT(*) FROM faculty WHERE college_id = ? AND status = 'Inactive'");
    $stmt->bind_param("i", $college_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_row();
    $inactiveFaculty = $row[0];
    
    // Calculate totals and percentages
    $totalFaculty = $activeFaculty + $inactiveFaculty;
    if ($totalFaculty > 0) {
        $activePercent = ($activeFaculty / $totalFaculty) * 100;
        $inactivePercent = ($inactiveFaculty / $totalFaculty) * 100;
    }
}

// Get faculty stats for the admin's college
$stats = [
    'total_faculty' => 0,
    'total_female' => 0,
    'total_male' => 0,
    'full_time' => 0,
    'part_time' => 0
];

if ($college_id) {
    // Total faculty
    $stmt = $conn->prepare("SELECT COUNT(*) FROM faculty WHERE college_id = ?");
    $stmt->bind_param("i", $college_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_row();
    $stats['total_faculty'] = $row[0];

    // Gender counts
    $stmt = $conn->prepare("SELECT p.gender, COUNT(*) as cnt
                       FROM faculty f 
                       LEFT JOIN faculty_personal_info p ON f.faculty_id = p.faculty_id 
                       WHERE f.college_id = ? 
                       GROUP BY p.gender");
    $stmt->bind_param("i", $college_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        if (($row['gender'] ?? '') === 'Female') $stats['total_female'] = (int)$row['cnt'];
        if (($row['gender'] ?? '') === 'Male') $stats['total_male'] = (int)$row['cnt'];
    }

    // Employment type
    $stmt = $conn->prepare("SELECT employment_type, COUNT(*) FROM faculty WHERE college_id = ? GROUP BY employment_type");
    $stmt->bind_param("i", $college_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        if ($row['employment_type'] === 'Full-Time') $stats['full_time'] = $row['COUNT(*)'];
        if ($row['employment_type'] === 'Part-Time') $stats['part_time'] = $row['COUNT(*)'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Home | Admin</title>
  <link rel="stylesheet" href="../css/admin_style.css?v=<?php echo time(); ?>" />
  <link rel="stylesheet" href="../css/help.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    /* Base font sizes for elements to scale properly */
    body {
      background: #f4fff4;
      color: #187436;
    }

    .dashboard-container h1 { font-size: 2em; }
    .dashboard-container h2 { font-size: 1.5em; }
    .dashboard-container h3 { font-size: 1.2em; }
    .welcome-message { font-size: 1.2em; }
    .admin-credentials-card { font-size: 1em; }
    .section-title { font-size: 1.5em; }
    .view-link { font-size: 0.9em; }
  </style>
  <script>
    // Check and apply theme and text size on page load
    document.addEventListener('DOMContentLoaded', function() {
      // Apply theme
      const savedTheme = localStorage.getItem('plpTheme') || 'light';
      if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
      }
      
      // Apply text size
      const savedTextSize = localStorage.getItem('plpTextSize') || '100';
      const html = document.querySelector('html');
      html.style.fontSize = savedTextSize + '%';
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
    
    <div class="navigation" id="menu">
      <div class="navigation-header">
          <h1>ADMINISTRATOR</h1>
        <h2>| PLP FACULTY PROFILING SYSTEM |</h2>
      </div>
    
      <nav>
        <ul>
          <li><a href="home.php" class="active"><img src="../images/home.png" alt="Home Icon" class="menu-icon">HOME</a></li>
          <li><a href="college_management.php"><img src="../images/department.png" alt="Department Icon" class="menu-icon">COLLEGE MANAGEMENT</a></li>
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
          <li><a href="setting.php"><img src="../images/setting.png" alt="Settings Icon" class="menu-icon">SETTINGS</a></li>
        </ul>
      </nav>

      <div class="logout-section">
        <a href="#" onclick="confirmLogout()"><img src="../images/logout.png" alt="Logout Icon" class="menu-icon">LOGOUT</a>
      </div>
    </div>
  </div>

  <div class="admin-dashboard-container">
    <div class="admin-home-card">
      <header>
  <h1>
        <i class="fas fa-home" style="font-size:1.1em;vertical-align:middle;margin-right:8px;"></i>
        Admin Home
      </h1>
        <div class="welcome-message">
          Welcome back, <span id="adminName"><?php echo $adminName; ?></span>! Here's your home overview.
        </div>
      </header>
    </div>

    <div class="stats-grid">
      <!-- Total Faculty Card -->
      <div class="stat-card">
        <div class="stat-header">
          <img src="../images/totalfaculty.png" alt="Faculty" class="stat-icon">
          <h3>Total Faculty Members</h3>
        </div>
        <div class="value"> Total Faculty: <?php echo $stats['total_faculty']; ?></div>
      </div>
      
        <!-- Gender Pie Chart Card -->
        <div class="stat-card pie-chart-card">
            <div class="stat-header">
                <img src="../images/gender.png" alt="Gender" class="stat-icon">
                <h3>By Gender</h3>
            </div>
            <div class="pie-chart-container">
                <div class="pie-chart" id="genderChart">
                    <?php 
                    $genderTotal = $stats['total_male'] + $stats['total_female'];
                    if ($genderTotal > 0) {
                        $malePercent = ($stats['total_male'] / $genderTotal) * 100;
                        $femalePercent = 100 - $malePercent;
                        echo '<div class="pie-chart" style="--color1:#3498db;--color2:#e91e63;--percent1:'.$malePercent.'%;"></div>';
                    } else {
                        echo '<div class="pie-chart" style="--color1:#e0e0e0;--color2:#e0e0e0;--percent1:100%;"></div>';
                    }
                    ?>
                </div>
                <div class="pie-legend">
                    <div class="legend-item">
                        <span class="color-indicator male"></span>
                        <span class="legend-label">Male: <?php echo $stats['total_male']; ?></span>
                    </div>
                    <div class="legend-item">
                        <span class="color-indicator female"></span>
                        <span class="legend-label">Female: <?php echo $stats['total_female']; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employment Type Pie Chart Card -->
        <div class="stat-card pie-chart-card">
            <div class="stat-header">
                <img src="../images/emptype.png" alt="Employment" class="stat-icon">
                <h3>By Employment</h3>
            </div>
            <div class="pie-chart-container">
                <div class="pie-chart" id="employmentChart">
                    <?php 
                    $employmentTotal = $stats['full_time'] + $stats['part_time'];
                    if ($employmentTotal > 0) {
                        $fullPercent = ($stats['full_time'] / $employmentTotal) * 100;
                        $partPercent = 100 - $fullPercent;
                        echo '<div class="pie-chart" style="--color1:#2ecc71;--color2:#f39c12;--percent1:'.$fullPercent.'%;"></div>';
                    } else {
                        echo '<div class="pie-chart" style="--color1:#e0e0e0;--color2:#e0e0e0;--percent1:100%;"></div>';
                    }
                    ?>
                </div>
                <div class="pie-legend">
                    <div class="legend-item">
                        <span class="color-indicator full-time"></span>
                        <span class="legend-label">Full-Time: <?php echo $stats['full_time']; ?></span>
                    </div>
                    <div class="legend-item">
                        <span class="color-indicator part-time"></span>
                        <span class="legend-label">Part-Time: <?php echo $stats['part_time']; ?></span>
                    </div>
                </div>
            </div>
        </div>

    <h2 class="section-title">Pending Documents and Recent Faculty Status</h2>

    <div class="credentials-section">
      <div class="admin-credentials-card">
        <h3><img src="../images/pendingdoc.png" alt="pendingdoc-icon" class="preview-icon">Pending Documents for Verification</h3>
        <div class="credentials-container">
          <ul class="credentials-list">
            <?php
            // Get pending credentials by type
            $pendingCredentialsByType = [];
            if ($college_id) {
                $stmt = $conn->prepare("SELECT c.credential_type, COUNT(*) as count 
                                      FROM credentials c 
                                      JOIN faculty f ON c.faculty_id = f.faculty_id 
                                      WHERE f.college_id = ? AND c.status = 'Pending'
                                      GROUP BY c.credential_type
                                      ORDER BY count DESC");
                $stmt->bind_param("i", $college_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                while ($row = $result->fetch_assoc()) {
                    $pendingCredentialsByType[] = $row;
                }
            }
            
            if (!empty($pendingCredentialsByType)) {
                foreach ($pendingCredentialsByType as $credential) {
                    echo '<li>';
                    echo '<span class="credential-type">' . htmlspecialchars($credential['credential_type']) . '</span>';
                    echo '<span class="credential-count">' . $credential['count'] . '</span>';
                    echo '</li>';
                }
            } else {
              echo '<li class="no-pending">No pending credentials.</li>';
            }
            ?>
          </ul>
        </div>
        <a href="files_report.php" class="action-btn edit-btn" style="margin-top:10px;">
        <i class="fas fa-file-alt" style="font-size:18px;vertical-align:middle;margin-right:6px;"></i>
            Manage Pending Documents
        </a>
      </div>

      <div class="admin-credentials-card">
        <h3><img src="../images/status.png" alt="status-icon" class="preview-icon">Faculty Status</h3>
        <div class="faculty-graph-container">
          <div class="faculty-graph">
            <div class="faculty-graph-segment faculty-graph-active" 
                style="width: <?php echo $activePercent; ?>%">
            </div>
            <div class="faculty-graph-segment faculty-graph-inactive" 
                style="width: <?php echo $inactivePercent; ?>%">
            </div>
          </div>
          <div class="faculty-graph-labels">
            <span class="status-label-active">
                <span class="color-indicator active"></span>
                Active: <?php echo $activeFaculty; ?>
            </span>
            <span class="status-label-inactive">
                <span class="color-indicator inactive"></span>
                Inactive: <?php echo $inactiveFaculty; ?>
            </span>
            <span>
                Total: <?php echo $totalFaculty; ?>
            </span>
          </div>
        </div>
        <a href="college_management.php" class="action-btn edit-btn" style="margin-top:10px;">
        <i class="fas fa-user-clock" style="font-size:18px;vertical-align:middle;margin-right:6px;"></i>
            Manage Faculty Status
        </a>
      </div>
    </div>
  </div>

  <?php include '../auth_2/help.php'; ?>

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
    })
    
    //log-out
    function confirmLogout() {
            if (confirm('Are you sure you want to logout?')) {
                // Change this to point to your process_logout.php
                window.location.href = '../login/process_logout.php';
            }
        }
  </script>
  <script src="../auth_2/help.js?v=<?php echo time(); ?>"></script>
  <script src="scripts.js?v=<?php echo time(); ?>"></script>
</body>
</html>