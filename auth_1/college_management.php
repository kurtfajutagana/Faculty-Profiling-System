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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>College Management | Admin</title>
  <link rel="stylesheet" href="../css/admin_style.css?v=<?php echo time(); ?>" />
  <link rel="stylesheet" href="../css/department.css?v=<?php echo time(); ?>" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="../css/help.css?v=<?php echo time(); ?>">

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const savedTheme = localStorage.getItem('plpTheme') || 'light';
      if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
      }
      
      const savedTextSize = localStorage.getItem('plpTextSize') || '100';
      document.querySelector('html').style.fontSize = savedTextSize + '%';
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
      <h1>ADMINISTRATOR</h1>
      <h2>| PLP FACULTY PROFILING SYSTEM |</h2>
    </div>

    <nav>
      <ul>
        <li><a href="home.php"><img src="../images/home.png" alt="Home Icon" class="menu-icon">HOME</a></li>
        <li><a href="college_management.php" class="active"><img src="../images/department.png" alt="Department Icon" class="menu-icon">COLLEGE MANAGEMENT</a></li>
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
      
    <div class="page-header">
        <h1 class="college-management-title">
        <i class="fas fa-building-columns"></i>
        College Management<?php if (!empty($current_college_name)) echo ' - ' . htmlspecialchars($current_college_name); ?>
    </h1>
    </div>
    
    <div class="faculty-management-container">
        <div class="management-tabs">
            <button class="tab-btn active" onclick="switchTab('facultyTab')">List of Faculty</button>
            <button class="tab-btn" id="credentialsTabBtn" onclick="switchTab('credentialsTab')" style="display:none;">Approved Documents</button>
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search faculty...">
            </div>
        </div>
        
        <div id="facultyTab" class="tab-content active">
            <div class="table-controls">
            </div>
            
            <div class="faculty-table-container">
                <table id="facultyTable">
                    <thead>
                        <tr>
                            <th>Faculty ID</th>
                            <th>Full Name</th>
                            <th>Gender</th>
                            <th>Email</th>
                            <th>Employment Type</th>
                            <th>Specialization</th>
                            <th>Contact Number</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if ($current_college_id) {
                                $sql = "SELECT f.faculty_id, f.full_name, p.gender, f.email, f.employment_type, 
                                        f.specialization, f.contact_number, f.status 
                                        FROM faculty f
                                        LEFT JOIN faculty_personal_info p ON f.faculty_id = p.faculty_id
                                        WHERE f.college_id = ? 
                                        ORDER BY f.faculty_id";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("i", $current_college_id);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $statusClass = ($row['status'] ?? 'Active') === 'Active' ? 'status-active' : 'status-inactive';
                                        echo '<tr>';
                                        echo '<td>' . htmlspecialchars($row['faculty_id'] ?? '') . '</td>';
                                        echo '<td>' . htmlspecialchars($row['full_name'] ?? '') . '</td>';
                                        echo '<td>' . htmlspecialchars($row['gender'] ?? 'N/A') . '</td>';
                                        echo '<td>' . htmlspecialchars($row['email'] ?? '') . '</td>';
                                        echo '<td>' . htmlspecialchars($row['employment_type'] ?? 'N/A') . '</td>';
                                        echo '<td>' . htmlspecialchars($row['specialization'] ?? 'N/A') . '</td>';
                                        echo '<td>' . htmlspecialchars($row['contact_number'] ?? 'N/A') . '</td>';
                                        echo '<td><span class="status-badge ' . $statusClass . '">' . htmlspecialchars($row['status'] ?? 'Active') . '</span></td>';
                                        echo '<td class="actions">';
                                        if (($row['status'] ?? 'Active') === 'Active') {
                                            echo '<button class="action-btn deactivate-btn" onclick="changeStatus(\'' . htmlspecialchars($row['faculty_id'] ?? '') . '\', \'Inactive\')">
                                                <i class="fas fa-times-circle"></i> Deactivate
                                                </button>';
                                        } else {
                                            echo '<button class="action-btn activate-btn" onclick="changeStatus(\'' . htmlspecialchars($row['faculty_id'] ?? '') . '\', \'Active\')">
                                                <i class="fas fa-check-circle"></i> Activate
                                                </button>';
                                        }
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="9" class="text-center">No faculty members found</td></tr>';
                                }
                                $stmt->close();
                            } else {
                                echo '<tr><td colspan="9" class="text-center">Error: College ID not found</td></tr>';
                            }
                            $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div id="credentialsTab" class="tab-content">
            <div class="credentials-header">
                <h2 id="credentialsTableTitle">Documents for Faculty Member</h2>
            </div>
            
                <div id="credentialsList"></div>
            </div>
    </div>

    <?php include '../auth_2/help.php'; ?>

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
                // Select rows from both tables, if they exist
                const facultyRows = document.querySelectorAll('#facultyTable tbody tr');
                const credentialsRows = document.querySelectorAll('.credentials-table tbody tr');
                // Combine NodeLists into one array
                const rows = [...facultyRows, ...credentialsRows];

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

    function changeStatus(facultyId, newStatus) {
        if (confirm(`Are you sure you want to ${newStatus === 'Active' ? 'activate' : 'deactivate'} this faculty member?`)) {
            fetch('change_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    faculty_id: facultyId,
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Faculty member ${newStatus === 'Active' ? 'activated' : 'deactivated'} successfully`);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating faculty status');
            });
        }
    }

    // Tab switching function
    function switchTab(tabId) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
        });
        
        // Deactivate all tab buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Activate the selected tab
        document.getElementById(tabId).classList.add('active');
        document.querySelector(`.tab-btn[onclick="switchTab('${tabId}')"]`).classList.add('active');
        
        // Special handling for credentials tab
        if (tabId === 'facultyTab') {
            document.getElementById('credentialsTabBtn').style.display = 'none';
        }
    }

function showFacultyCredentials(facultyId, facultyName) {
    // Update the title
    document.getElementById('credentialsTableTitle').textContent = `Credentials for ${facultyName}`;
    
    // Show loading state
    document.getElementById('credentialsList').innerHTML = `
        <div class="loading-message">Loading documents...</div>
    `;
    
    // Show the credentials tab button
    document.getElementById('credentialsTabBtn').style.display = 'inline-block';
    
    // Switch to the credentials tab
    switchTab('credentialsTab');
    
    // Fetch credentials via AJAX
    fetch(`get_faculty_credentials.php?faculty_id=${facultyId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.credentials.length > 0) {
                    let html = `
                        <div class="credentials-table-container">
                            <table class="credentials-table">
                                <thead>
                                    <tr>
                                        <th>Document Name</th>
                                        <th>Document Type</th>
                                        <th>Issue Date</th>
                                        <th>Expiry Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    data.credentials.forEach(doc => {
                        const issueDate = doc.issued_date ? 
                            new Date(doc.issued_date).toLocaleDateString() : 'N/A';
                        const expiryDate = doc.expiry_date ? 
                            new Date(doc.expiry_date).toLocaleDateString() : 'No expiration';
                        
                        html += `
                            <tr>
                                <td>${doc.name || 'N/A'}</td>
                                <td>${doc.type || 'N/A'}</td>
                                <td>${issueDate}</td>
                                <td>${expiryDate}</td>
                                <td>
                                    <div class="credential-actions">
                                        <button class="btn btn-sm btn-view" onclick="window.open('${doc.file_path}', '_blank')">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <a href="${doc.file_path}" download class="btn btn-sm btn-download">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                    
                    html += `
                                </tbody>
                            </table>
                        </div>
                    `;
                    document.getElementById('credentialsList').innerHTML = html;
                } else {
                    document.getElementById('credentialsList').innerHTML = `
                        <div class="no-credentials">No verified documents found for this faculty member</div>
                    `;
                }
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('credentialsList').innerHTML = `
                <div class="error-message">Error loading documents</div>
            `;
        });
    }

    // Modify the faculty table rows to be clickable
    document.addEventListener('DOMContentLoaded', function() {
        const facultyRows = document.querySelectorAll('#facultyTable tbody tr');
        facultyRows.forEach(row => {
            const facultyId = row.cells[0].textContent;
            const facultyName = row.cells[1].textContent;
            
            row.style.cursor = 'pointer';
            row.addEventListener('click', (e) => {
                // Don't trigger if clicking on action buttons
                if (!e.target.closest('.action-btn')) {
                    showFacultyCredentials(facultyId, facultyName);
                }
            });
        });
    });

    function closeAndRefreshAddModal() {
    document.getElementById('addModal').style.display = 'none';
    window.location.href = 'user.php'; // Refresh the page
}

  </script>
  <script src="../auth_2/help.js?v=<?php echo time(); ?>"></script>
</body>
</html>