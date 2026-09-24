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

// Handle account unlock/reactivation requests
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reactivate_user'])) {
    $user_id = $_POST['user_id'];
    
    // First get the faculty_id for this user
    $faculty_query = "SELECT faculty_id FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($faculty_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $faculty_result = $stmt->get_result();
    $user_data = $faculty_result->fetch_assoc();
    
    if ($user_data && $user_data['faculty_id']) {
        // Reactivate the faculty account and reset login attempts
        $reactivate_stmt = $conn->prepare("UPDATE faculty SET status = 'Active' WHERE faculty_id = ?");
        $reactivate_stmt->bind_param("s", $user_data['faculty_id']);
        $reactivate_stmt->execute();
        $reactivate_stmt->close();
        
        $reset_stmt = $conn->prepare("UPDATE users SET login_attempts = 0 WHERE user_id = ?");
        $reset_stmt->bind_param("i", $user_id);
        if ($reset_stmt->execute()) {
            $_SESSION['success_message'] = "Account reactivated successfully";
        } else {
            $_SESSION['error_message'] = "Error reactivating account";
        }
        $reset_stmt->close();
    } else {
        $_SESSION['error_message'] = "Error: Faculty record not found";
    }
    
    header("Location: user.php");
    exit();
}

// Fetch users from the same college with faculty status
$sql = "SELECT u.*, f.status as faculty_status, f.full_name, f.email 
        FROM users u
        JOIN faculty f ON u.faculty_id = f.faculty_id
        WHERE f.college_id = ?
        ORDER BY f.status DESC, f.full_name"; // Sort by status (Inactive first)
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $current_college_id);
$stmt->execute();
$users = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Management | Admin</title>
  <link rel="icon" type="image/png" href="../images/logo.png">
  <link rel="stylesheet" href="../css/admin_style.css?v=<?php echo time(); ?>" />
  <link rel="stylesheet" href="../css/user.css?v=<?php echo time(); ?>" />
  <link rel="stylesheet" href="../css/help.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body>
  <div class="header">
    <div class="logo-section">
      <img src="../images/logo.png" alt="Logo" />
      <div class="title">PAMANTASAN NG LUNGSOD NG PASIG</div>
      <button class="hamburger" onclick="toggleMenu()" aria-label="Toggle Navigation Menu">
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
          <li><a href="user.php" class="active"><img src="../images/user.png" alt="User Icon" class="menu-icon">USER MANAGEMENT</a></li>
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
        <a href="#" onclick="confirmLogout()"><img src="../images/logout.png" class="menu-icon">LOGOUT</a>
      </div>
    </div>

        <div class="header-user-management">
            <h1 class="user-management-title">
                <i class="fas fa-users"></i>
                User Management<?php if (!empty($current_college_name)) echo ' - ' . htmlspecialchars($current_college_name); ?>
            </h1>
        </div>
      <!-- Display success/error messages -->
  <?php if (isset($_SESSION['success_message'])): ?>
      <div class="alert alert-success">
          <?= $_SESSION['success_message']; ?>
          <?php unset($_SESSION['success_message']); ?>
      </div>
  <?php endif; ?>

  <?php if (isset($_SESSION['error_message'])): ?>
      <div class="alert alert-error">
          <?= $_SESSION['error_message']; ?>
          <?php unset($_SESSION['error_message']); ?>
      </div>
  <?php endif; ?>

  <div class="user-management-container">
      <div class="search-add-container">
          <div class="search-box">
              <i class="fas fa-search"></i>
              <input type="text" id="searchInput" placeholder="Search faculty...">
          </div>
          
          <button class="add-user-btn" onclick="openAddModal()">
                <i class="fas fa-user-plus"></i>
              Add User
          </button>
      </div>

      <div class="table-container">
          <table class="user-table">
              <thead>
                  <tr>
                      <th>Faculty ID</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Username</th>
                      <th>Status</th>
                      <th>Actions</th>
                      <!-- <th>Actions</th> -->
                  </tr>
              </thead>
              <tbody>
                  <?php if ($users->num_rows > 0): ?>
                      <?php while ($user = $users->fetch_assoc()): 
                        $statusClass = $user['faculty_status'] === 'Active' ? 'status-active' : 'status-inactive';
                        ?>
                          <tr>
                              <td><?= htmlspecialchars($user['faculty_id'] ?? '') ?></td>
                              <td><?= htmlspecialchars($user['full_name'] ?? '') ?></td>
                              <td><?= htmlspecialchars($user['email'] ?? '') ?></td>
                              <td><?= htmlspecialchars($user['username'] ?? '') ?></td>
                              <td>
                                  <span class="status-badge <?= ($user['faculty_status'] ?? '') === 'Active' ? 'status-active' : 'status-inactive' ?>">
                                      <?= htmlspecialchars($user['faculty_status'] ?? 'N/A') ?>
                                  </span>
                              </td>
                              <td> 
                                  <?php if (($user['faculty_status'] ?? '') === 'Active'): ?>
                                      <button class="action-btn edit-btn" onclick="openEditModal(
                                          '<?= htmlspecialchars($user['faculty_id'] ?? '') ?>',
                                          '<?= htmlspecialchars($user['username'] ?? '') ?>'
                                      )">
                                          <i class="fas fa-pen"></i> Edit
                                      </button>
                                  <?php else: ?>
                                      <button class="action-btn edit-btn" disabled style="opacity: 0.5; cursor: not-allowed;">
                                          <i class="fas fa-pen"></i> Edit
                                      </button>
                                  <?php endif; ?>
                              </td>
                              <!-- <td>
                                <?php if ($user['faculty_status'] === 'Active'): ?>
                                  <button class="action-btn edit-btn" onclick="openEditModal(
                                      '<?= htmlspecialchars($user['faculty_id']) ?>',
                                      '<?= htmlspecialchars($user['username']) ?>'
                                  )">
                                      <i class="fas fa-pen"></i> Edit
                                  </button>
                                <?php else: ?>
                                  <button class="action-btn edit-btn" disabled style="opacity: 0.5; cursor: not-allowed;">
                                      <i class="fas fa-pen"></i> Edit
                                  </button>
                                <?php endif; ?>
                              </td> -->
                          </tr>
                      <?php endwhile; ?>
                  <?php else: ?>
                      <tr>
                          <td colspan="7" class="no-results">No users found</td>
                      </tr>
                  <?php endif; ?>
              </tbody>
          </table>
      </div>
  </div>

  <!-- ADD NEW USER MODAL -->
  <div id="addModal" class="modal">
    <div class="modal-content">
        <button class="modal-close-btn" onclick="closeAndRefreshAddModal()" title="Close">
            <i class="fas fa-times"></i>
        </button>
        <h2 class="add-faculty-title">
            <i class="fas fa-user-plus"></i>
            Add New Faculty User
        </h2>
          <form id="addUserForm" method="POST" onsubmit="return validateFacultyID()">
              <input type="text" name="faculty_id" id="faculty_id" placeholder="Enter Faculty ID" required>
              <input type="hidden" name="college_id" value="<?= $current_college_id ?>">
              <div class="college-display">College: <?= htmlspecialchars($current_college_name) ?></div>
              <input type="text" name="username" placeholder="Username" required>
              
              <div class="password-container">
                  <input type="password" name="password" id="password" placeholder="Password" required>
                  <span class="toggle-password" onclick="togglePassword('password')">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                          <circle cx="12" cy="12" r="3"></circle>
                      </svg>
                  </span>
              </div>
              
              <div class="password-container">
                  <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                  <span class="toggle-password" onclick="togglePassword('confirm_password')">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                          <circle cx="12" cy="12" r="3"></circle>
                      </svg>
                  </span>
              </div>
              <small id="password-match-message" style="color: #dc3545; display: none;">Passwords do not match!</small>

              <select name="role" required>
                  <option value="">Select Role</option>
                  <option value="Admin">Admin</option>
                  <option value="Faculty">Faculty</option>
                  <option value="Head">Head</option>
              </select>

              <div class="modal-buttons">
                  <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                  <button type="submit" class="submit-btn">Add User</button>
              </div>
          </form>
          <div id="faculty-id-error" class="custom-alert">
              There is no existing faculty with this Faculty ID. You need to add a faculty with the Faculty ID 
              <span id="faculty-id" style="font-weight: bold;"></span> first 
              <a href="college_management.php">here</a>.
          </div>
      </div>
  </div>

  <!-- EDIT USER MODAL -->
  <div id="editModal" class="modal">
      <div class="modal-content">
          <h2 class="update-account-title">
              <i class="fas fa-user-edit"></i>
              Update Faculty User Account
          </h2>
          <form id="editUserForm" method="POST">
              <input type="text" name="faculty_id" id="edit_faculty_id" readonly>
              <input type="hidden" name="college_id" value="<?= $current_college_id ?>">
              <div class="college-display">College: <?= htmlspecialchars($current_college_name) ?></div>
              
              <input type="text" name="username" id="edit_username" required>

              <div class="password-container">
                  <input type="password" name="new_password" id="new_password" placeholder="Click Generate Password button" readonly>
                  <div class="password-buttons">
                    <button type="button" class="generate-password-btn" onclick="generateRandomPassword()">
                        <i class="fas fa-key"></i> Generate Password
                    </button>
                    <button type="button" class="clear-password-btn" onclick="clearPassword()">
                        <i class="fas fa-eraser"></i> Clear Password
                    </button>
                  </div>
              </div>
              <small style="color: #6c757d; display: block; margin-bottom: 1rem;">
                  A strong random password will be generated and emailed to the user.
              </small>

              <div class="modal-buttons">
                  <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                  <button type="submit" class="submit-btn">Update User</button>
              </div>
          </form>
      </div>
  </div>

  <form id="deleteForm" method="POST" style="display:none;">
      <input type="hidden" name="faculty_id" id="delete_faculty_id">
  </form>

    
  <?php include '../auth_2/help.php'; ?>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const savedTheme = localStorage.getItem('plpTheme') || 'light';
      if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
      }

      const savedTextSize = localStorage.getItem('plpTextSize') || '100';
      document.querySelector('html').style.fontSize = savedTextSize + '%';
      
      // Search functionality
      document.getElementById('searchInput').addEventListener('keyup', function() {
        const input = this.value.toLowerCase();
        const rows = document.querySelectorAll('.user-table tbody tr');
        
        rows.forEach(row => {
          const text = row.textContent.toLowerCase();
          row.style.display = text.includes(input) ? '' : 'none';
        });
      });

      // Reports dropdown functionality
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

    function confirmLogout() {
      if (confirm('Are you sure you want to logout?')) {
        // Change this to point to your process_logout.php
        window.location.href = '../login/process_logout.php';
      }
    }

    function closeAndRefreshAddModal() {
        document.getElementById('addModal').style.display = 'none';
        window.location.href = 'user.php'; // Refresh the page
    }
  </script>
  <script src="scripts.js?v=<?php echo time(); ?>"></script>
  <script src="users.js?v=<?php echo time(); ?>"></script>
    <script src="../auth_2/help.js?v=<?php echo time(); ?>"></script>
  <?php if (isset($conn)) $conn->close(); ?>
</body>
</html>