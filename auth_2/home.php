<?php
session_start();
include '../db_connection.php';

// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Initialize variables
$facultyName = "USER";
$facultyEmployment = "";
$facultySpecialization = "";
$facultyCollege = "";
$facultyEmail = "";
$facultyContact = "";
$scheduleCount = 0;
$latestSchedule = null;
$credentialsCount = 0;
$latestCredential = null;

if (isset($_SESSION['faculty_id'])) {
    $facultyId = $_SESSION['faculty_id'];
    
    // Get faculty basic information
    $query = "SELECT f.full_name, f.email, f.contact_number, f.employment_type as employment, f.specialization as specialization,
                     c.college_name as college 
              FROM faculty f
              JOIN colleges c ON f.college_id = c.college_id
              WHERE f.faculty_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $facultyId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $facultyNameWelcome = strtoupper($row['full_name']);
        $facultyName = $row['full_name'] ?? "Not specified";
        $facultyEmployment = $row['employment'] ?? "Not specified";
        $facultySpecialization = $row['specialization'] ?? "Not specified";
        $facultyCollege = $row['college'] ?? "Not specified";
        $facultyEmail = $row['email'] ?? "Not specified";
        $facultyContact = $row['contact_number'] ?? "Not specified";
    }
    
    // Get count of uploaded schedules with status 'Verified'
    $countQuery = "SELECT COUNT(*) as count FROM teaching_load 
                  WHERE faculty_id = ?";
    $countStmt = $conn->prepare($countQuery);
    $countStmt->bind_param("s", $facultyId);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    
    if ($countRow = $countResult->fetch_assoc()) {
        $scheduleCount = $countRow['count'];
    }
    
    // Get latest uploaded schedule
    $latestQuery = "SELECT file_path, created_at FROM teaching_load
                   WHERE faculty_id = ? AND status = 'Verified'
                   ORDER BY created_at DESC LIMIT 1";
    $latestStmt = $conn->prepare($latestQuery);
    $latestStmt->bind_param("s", $facultyId);
    $latestStmt->execute();
    $latestResult = $latestStmt->get_result();
    
    if ($latestRow = $latestResult->fetch_assoc()) {
        $latestSchedule = [
            'file_path' => $latestRow['file_path'],
            'upload_date' => date('M d, Y', strtotime($latestRow['created_at']))
        ];
    }
    
    // Get count of uploaded credentials with status 'Verified'
    $credCountQuery = "SELECT COUNT(*) as count FROM credentials 
                      WHERE faculty_id = ?";
    $credCountStmt = $conn->prepare($credCountQuery);
    $credCountStmt->bind_param("s", $facultyId);
    $credCountStmt->execute();
    $credCountResult = $credCountStmt->get_result();
    
    if ($credCountRow = $credCountResult->fetch_assoc()) {
        $credentialsCount = $credCountRow['count'];
    }
    
    // Get latest uploaded credential
    $latestCredQuery = "SELECT credential_type, file_path, uploaded_at FROM credentials
                       WHERE faculty_id = ? AND status = 'Verified'
                       ORDER BY uploaded_at DESC LIMIT 1";
    $latestCredStmt = $conn->prepare($latestCredQuery);
    $latestCredStmt->bind_param("s", $facultyId);
    $latestCredStmt->execute();
    $latestCredResult = $latestCredStmt->get_result();
    
    if ($latestCredRow = $latestCredResult->fetch_assoc()) {
        $latestCredential = [
            'type' => $latestCredRow['credential_type'],
            'file_path' => $latestCredRow['file_path'],
            'upload_date' => date('M d, Y', strtotime($latestCredRow['uploaded_at']))
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | Faculty</title>
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="stylesheet" href="../css/faculty_style.css?v=<?php echo time(); ?>"/>
    <link rel="stylesheet" href="../css/homedashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/help.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/themes.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="theme.js?v=<?php echo time(); ?>"></script>
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
        <li><a href="home.php" class="active"><img src="../images/home.png" alt="Home Icon" class="menu-icon">HOME</a></li>
        <li><a href="profile.php"><img src="../images/profile.png" alt="Profile Icon" class="menu-icon">PROFILE</a></li>
        <li><a href="teachingload.php"><img src="../images/teachingload.png" alt="Teaching Icon" class="menu-icon">TEACHING LOAD</a></li>
        <li><a href="credentials.php"><img src="../images/credentials.png" alt="Credentials Icon" class="menu-icon">CREDENTIALS</a></li>
        <li><a href="setting.php"><img src="../images/setting.png" alt="Settings Icon" class="menu-icon">SETTINGS</a></li>
      </ul>
    </nav>

    <div class="logout-section">
        <a href="#" onclick="confirmLogout()"><img src="../images/logout.png" alt="Logout Icon" class="menu-icon">LOGOUT</a>
      </div>
  </div>



          <div class="dashboard-container">
    <div class="welcome-banner">
      <header>
  <h2>
        <i class="fas fa-home"></i>
        Faculty Home
      </h2>
        <div class="welcome-message">
            <p>Welcome back, <?php echo htmlspecialchars($facultyNameWelcome); ?>! Here's your dashboard overview.</p>
        </div>
      </header>
    </div>

        <div class="dashboard-grid">
            <!-- Profile Information Card -->
            <div class="dashboard-card profile-card">
                <div class="card-header">
                    <i class="fas fa-user-circle"></i>
                    <h3>Profile Information</h3>
                </div>
                <div class="card-body">
                    <div class="profile-info">
                        <div class="info-item">
                            <span class="info-label">Employment Type:</span>
                            <span class="info-value"><?php echo htmlspecialchars($facultyEmployment); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Full Name:</span>
                            <span class="info-value"><?php echo htmlspecialchars($facultyName); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Specialization:</span>
                            <span class="info-value"><?php echo htmlspecialchars($facultySpecialization); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">College:</span>
                            <span class="info-value"><?php echo htmlspecialchars($facultyCollege); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Email:</span>
                            <span class="info-value"><?php echo htmlspecialchars($facultyEmail); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Contact:</span>
                            <span class="info-value"><?php echo htmlspecialchars($facultyContact); ?></span>
                        </div>
                    </div>
                    <a href="profile.php" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Profile
                    </a>
                </div>
            </div>

            <!-- Teaching Schedule Card -->
            <div class="dashboard-card schedule-card">
                <div class="card-header">
                    <i class="fas fa-calendar-alt"></i>
                    <h3>Teaching Schedules</h3>
                </div>
                <div class="card-body">
                    <div class="schedule-summary">
                        <div class="schedule-count">
                            <i class="fas fa-file-upload"></i>
                            <span class="count-number"><?php echo $scheduleCount; ?></span>
                            <span class="count-label">Uploaded Schedule<?php echo $scheduleCount !== 1 ? 's' : ''; ?></span>
                        </div>
                        <div class="schedule-info">
                            <?php if ($scheduleCount > 0): ?>
                                <p>You have successfully uploaded <?php echo $scheduleCount; ?> teaching schedule<?php echo $scheduleCount !== 1 ? 's' : ''; ?>.</p>
                                <?php if ($latestSchedule): ?>
                                    <div class="latest-upload">
                                        <p>Latest upload: <?php echo $latestSchedule['upload_date']; ?></p>
                                        <a href="<?php echo htmlspecialchars($latestSchedule['file_path']); ?>" target="_blank">
                                            <i class="fas fa-eye"></i> View Latest
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <p>No teaching schedules uploaded yet.</p>
                                <p>Upload your teaching schedule to get started.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <a href="teachingload.php" class="btn btn-primary">
                        <i class="fas <?php echo $scheduleCount > 0 ? 'fa-calendar-check' : 'fa-calendar-plus'; ?>"></i> 
                        <?php echo $scheduleCount > 0 ? 'Manage Schedules' : 'Upload Schedule'; ?>
                    </a>
                </div>
            </div>

            <!-- Uploaded Credentials Card -->
            <div class="dashboard-card credentials-card">
                <div class="card-header">
                    <i class="fas fa-file-certificate"></i>
                    <h3>Credentials</h3>
                </div>
                <div class="card-body">
                    <div class="credentials-summary">
                        <div class="credentials-count">
                            <i class="fas fa-file-upload"></i>
                            <span class="count-number"><?php echo $credentialsCount; ?></span>
                            <span class="count-label">Uploaded Credential<?php echo $credentialsCount !== 1 ? 's' : ''; ?></span>
                        </div>
                        <div class="credentials-info">
                            <?php if ($credentialsCount > 0): ?>
                                <p>You have successfully uploaded <?php echo $credentialsCount; ?> credential<?php echo $credentialsCount !== 1 ? 's' : ''; ?>.</p>
                                <?php if ($latestCredential): ?>
                                    <div class="latest-upload">
                                        <p>Latest upload: <?php echo $latestCredential['upload_date']; ?></p>
                                        <a href="<?php echo htmlspecialchars($latestCredential['file_path']); ?>" target="_blank">
                                            <i class="fas fa-eye"></i> View Latest
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <p>No credentials uploaded yet.</p>
                                <p>Upload your credentials to get started.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <a href="credentials.php" class="btn btn-primary">
                        <i class="fas <?php echo $credentialsCount > 0 ? 'fa-file-alt' : 'fa-upload'; ?>"></i> 
                        <?php echo $credentialsCount > 0 ? 'Manage Credentials' : 'Upload Credentials'; ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'help.php'; ?>
                                
    <script>
        function confirmLogout() {
            if (confirm('Are you sure you want to logout?')) {
                // Change this to point to your process_logout.php
                window.location.href = '../login/process_logout.php';
            }
        }
    </script>
    <script src="help.js"></script>
    <script src="../scripts.js"></script>
</body>
</html>