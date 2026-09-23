<?php
session_start();
if (!isset($_SESSION['faculty_id'])) {
    header('Location: ../landing/index.php');
    exit();
}

if (isset($_SESSION['upload_success'])) {
    $success = $_SESSION['upload_success'];
    unset($_SESSION['upload_success']);
}

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}   

require_once '../db_connection.php';

$faculty_id = $_SESSION['faculty_id'];
$credentials = [];
$error = null;
$success = null;

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['credential_file'])) {
    $credentialType = $_POST['credential_type'];
    $credentialName = $_POST['credential_name'];
    $issuedBy = $_POST['issued_by'];
    $issuedDate = $_POST['issued_date'];
    $expiryDate = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null;
    
    $file = $_FILES['credential_file'];
    $originalFileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];

    // Validate issued date is not in the future
    $currentDate = date('Y-m-d');
    if ($issuedDate > $currentDate) {
        $_SESSION['upload_alert'] = 'error';
        $_SESSION['error_message'] = "Issued date cannot be in the future";
        header('Location: credentials.php');
        exit();
    }

    // Validate expiry date is after issued date if provided
    if ($expiryDate && $expiryDate < $issuedDate) {
        $_SESSION['upload_alert'] = 'error';
        $_SESSION['error_message'] = "Expiry date must be after issued date";
        header('Location: credentials.php');
        exit();
    }
    
    // Validate file (PDF only, max 5MB)
    $fileExt = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
    $allowed = ['pdf'];
    
    if (in_array($fileExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize < 5000000) { // 5MB
                $newFileName = uniqid('', true) . '.' . $fileExt;
                $fileDestination = 'uploads/credentials/' . $newFileName;
                
                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    try {
                        $stmt = $conn->prepare("INSERT INTO credentials (faculty_id, credential_type, credential_name, issued_by, issued_date, expiry_date, file_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("sssssss", $faculty_id, $credentialType, $credentialName, $issuedBy, $issuedDate, $expiryDate, $fileDestination);
                        $stmt->execute();
                        $stmt->close();
                        
                        // Set success flag and redirect
                        $_SESSION['upload_alert'] = 'success';
                        header('Location: credentials.php');
                        exit();
                    } catch (Exception $e) {
                        // Set error flag and redirect
                        $_SESSION['upload_alert'] = 'error';
                        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
                        unlink($fileDestination);
                        header('Location: credentials.php');
                        exit();
                    }
                } else {
                    $_SESSION['upload_alert'] = 'error';
                    $_SESSION['error_message'] = "There was an error uploading your file.";
                    header('Location: credentials.php');
                    exit();
                }
            } else {
                $error = "File is too large. Maximum size is 5MB.";
            }
        } else {
            $error = "There was an error uploading your file.";
        }
    } else {
        $error = "You can only upload PDF files.";
    }
}

// Build the base query
$query = "SELECT * FROM credentials WHERE faculty_id = ?";
$params = [$faculty_id];
$types = "s";

// Add filters if they exist in GET parameters
if (isset($_GET['status']) && !empty($_GET['status'])) {
    $query .= " AND status = ?";
    $params[] = $_GET['status'];
    $types .= "s";
}

if (isset($_GET['credential_type']) && !empty($_GET['credential_type'])) {
    $query .= " AND credential_type = ?";
    $params[] = $_GET['credential_type'];
    $types .= "s";
}

// Add sorting
$query .= " ORDER BY issued_date DESC";

try {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $credentials = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credentials | Faculty</title>
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="stylesheet" href="../css/faculty_style.css?v=<?php echo time(); ?>"/>
    <link rel="stylesheet" href="../css/credentials.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/help.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/themes.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="theme.js?v=<?php echo time(); ?>"></script>
</head>
<body>
    <div class="header">
        <div class="logo-section">
            <img src="../images/logo.png" alt="Logo">
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
        <li><a href="credentials.php" class="active"><img src="../images/credentials.png" alt="Credentials Icon" class="menu-icon">CREDENTIALS</a></li>
        <li><a href="setting.php"><img src="../images/setting.png" alt="Settings Icon" class="menu-icon">SETTINGS</a></li>
      </ul>
    </nav>

    <div class="logout-section">
            <a href="#" onclick="confirmLogout()"><img src="../images/logout.png" alt="Logout Icon" class="menu-icon">LOGOUT</a>
        </div>
    </div>        

    <div class="main-content">
        <div class="header-content">
            <h2><i class="fas fa-folder-open"></i> My Credentials</h2>
        </div>

        <!-- Upload Card -->
        <div class="card upload-card">
            <h2><i class="fas fa-upload"></i> Upload Credential</h2>
            <form action="credentials.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="credential_type">Credential Type:</label>
                    <select id="credential_type" name="credential_type" required>
                        <option value="">Select Type</option>
                        <option value="PDS">PDS</option>
                        <option value="SALN">SALN</option>
                        <option value="TOR">TOR</option>
                        <option value="Diploma">Diploma</option>
                        <option value="Certificates">Certificates</option>
                        <option value="Evaluation">Evaluation</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="credential_name">Credential Name:</label>
                    <input type="text" id="credential_name" name="credential_name" placeholder="e.g. Bachelor of Science in Computer Science" required>
                </div>
                
                <div class="form-group">
                    <label for="issued_by">Issued By:</label>
                    <input type="text" id="issued_by" name="issued_by" placeholder="e.g. Pamantasan ng Lungsod ng Pasig" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="issued_date">Issued Date:</label>
                        <input type="date" id="issued_date" name="issued_date" max="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="expiry_date">Expiry Date (if applicable):</label>
                        <input type="date" id="expiry_date" name="expiry_date">
                    </div>
                </div>
                
                <div class="form-group file-upload">
                    <label for="credential_file">Upload PDF File (max 5MB):</label>
                    <div class="file-upload-wrapper">
                        <input type="file" id="credential_file" name="credential_file" accept=".pdf" required>
                        <label for="credential_file" class="file-upload-label">
                            <i class="fas fa-cloud-upload"></i>
                            <span class="file-upload-text">Choose a file</span>
                            <span class="file-upload-filename" id="file-name">No file chosen</span>
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn-upload"><i class="fas fa-cloud-upload"></i> Upload</button>
            </form>
        </div>
        
        <!-- List of Uploaded Files Card -->
        <div class="card list-card">
            <h2><i class="fas fa-list"></i> Uploaded Credentials</h2>
            
            <!-- Filter Section -->
            <div class="filters">
                <form method="GET" class="filter-form">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="status_filter">Status:</label>
                            <select id="status_filter" name="status" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="Pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="Verified" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Verified') ? 'selected' : ''; ?>>Verified</option>
                                <option value="Rejected" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="credential_type_filter">Credential Type:</label>
                            <select id="credential_type_filter" name="credential_type" onchange="this.form.submit()">
                                <option value="">All Types</option>
                                <option value="PDS" <?php echo (isset($_GET['credential_type']) && $_GET['credential_type'] == 'PDS') ? 'selected' : ''; ?>>PDS</option>
                                <option value="SALN" <?php echo (isset($_GET['credential_type']) && $_GET['credential_type'] == 'SALN') ? 'selected' : ''; ?>>SALN</option>
                                <option value="TOR" <?php echo (isset($_GET['credential_type']) && $_GET['credential_type'] == 'TOR') ? 'selected' : ''; ?>>TOR</option>
                                <option value="Diploma" <?php echo (isset($_GET['credential_type']) && $_GET['credential_type'] == 'Diploma') ? 'selected' : ''; ?>>Diploma</option>
                                <option value="Certificates" <?php echo (isset($_GET['credential_type']) && $_GET['credential_type'] == 'Certificates') ? 'selected' : ''; ?>>Certificates</option>
                                <option value="Evaluation" <?php echo (isset($_GET['credential_type']) && $_GET['credential_type'] == 'Evaluation') ? 'selected' : ''; ?>>Evaluation</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            
            <?php if (empty($credentials)): ?>
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <p>No credentials found matching your criteria.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Credential Name</th>
                                <th>Type</th>
                                <th>Issued By</th>
                                <th>Issued Date</th>
                                <th>Expiry Date</th>
                                <th>Status</th>
                                <th>Date Uploaded</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($credentials as $credential): ?>
                                <tr data-id="<?php echo $credential['credential_id']; ?>">
                                    <td><?php echo htmlspecialchars($credential['credential_name']); ?></td>
                                    <td><?php echo htmlspecialchars($credential['credential_type']); ?></td>
                                    <td><?php echo htmlspecialchars($credential['issued_by']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($credential['issued_date'])); ?></td>
                                    <td><?php echo $credential['expiry_date'] ? date('M d, Y', strtotime($credential['expiry_date'])) : 'N/A'; ?></td>
                                    <td>
                                        <span class="status-badge <?php echo strtolower($credential['status']); ?>">
                                            <?php echo htmlspecialchars($credential['status']); ?>
                                        </span>
                                        <?php if ($credential['status'] === 'Rejected' && !empty($credential['reason'])): ?>
                                            <span class="reason-tooltip" title="<?php echo htmlspecialchars($credential['reason']); ?>">
                                                <i class="fas fa-info-circle"></i>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M d, Y h:i A', strtotime($credential['uploaded_at'])); ?></td>
                                    <td class="actions">
                                        <a href="<?php echo $credential['file_path']; ?>" target="_blank" class="btn-view" title="View"><i class="fas fa-eye"></i></a>
                                        <a href="<?php echo $credential['file_path']; ?>" download class="btn-download" title="Download"><i class="fas fa-download"></i></a>
                                        <?php if ($credential['status'] === 'Pending' || $credential['status'] === 'Rejected'): ?>
                                            <a href="#" class="btn-edit" title="Edit" onclick="openEditModal(<?php echo $credential['credential_id']; ?>)"><i class="fas fa-edit"></i></a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Edit Credential Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2><i class="fas fa-edit"></i> Edit Credential</h2>
            <form id="editCredentialForm" enctype="multipart/form-data">
                <input type="hidden" id="edit_credential_id" name="credential_id">
                
                <div class="form-group">
                    <label for="edit_credential_type">Credential Type:</label>
                    <select id="edit_credential_type" name="credential_type" required>
                        <option value="">Select Type</option>
                        <option value="PDS">PDS</option>
                        <option value="SALN">SALN</option>
                        <option value="TOR">TOR</option>
                        <option value="Diploma">Diploma</option>
                        <option value="Certificates">Certificates</option>
                        <option value="Evaluation">Evaluation</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_credential_name">Credential Name:</label>
                    <input type="text" id="edit_credential_name" name="credential_name" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_issued_by">Issued By:</label>
                    <input type="text" id="edit_issued_by" name="issued_by" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_issued_date">Issued Date:</label>
                        <input type="date" id="edit_issued_date" name="issued_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_expiry_date">Expiry Date (if applicable):</label>
                        <input type="date" id="edit_expiry_date" name="expiry_date">
                    </div>
                </div>
                
                <div class="form-group file-upload">
                    <label for="edit_credential_file">Update PDF File (optional):</label>
                    <div class="file-upload-wrapper">
                        <input type="file" id="edit_credential_file" name="credential_file" accept=".pdf">
                        <label for="edit_credential_file" class="file-upload-label">
                            <i class="fas fa-cloud-upload"></i>
                            <span class="file-upload-text">Choose a file</span>
                            <span class="file-upload-filename" id="edit-file-name">No file chosen</span>
                        </label>
                    </div>
                    <p class="current-file">Current file: <span id="current-file-name"></span></p>
                </div>
                
                <div class="form-buttons">
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn-save"><i class="fas fa-save"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <?php include 'help.php'; ?>

    <script>
        window.onload = function() {
            <?php if (isset($_SESSION['upload_alert'])): ?>
                <?php if ($_SESSION['upload_alert'] === 'success'): ?>
                    alert('Credential uploaded successfully!');
                <?php elseif ($_SESSION['upload_alert'] === 'error'): ?>
                    alert('<?php echo isset($_SESSION['error_message']) ? addslashes($_SESSION['error_message']) : "Error uploading file"; ?>');
                <?php endif; ?>
                <?php 
                unset($_SESSION['upload_alert']);
                unset($_SESSION['error_message']);
                ?>
            <?php endif; ?>
            
            // Update file name display when a file is selected
            document.getElementById('credential_file').addEventListener('change', function(e) {
                const fileName = e.target.files[0] ? e.target.files[0].name : 'No file chosen';
                document.getElementById('file-name').textContent = fileName;
            });
        };

        // Prevent form resubmission
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }


        document.querySelector('.main-content').addEventListener('click', function() {
            if (document.querySelector('.navigation.active')) {
                document.querySelector('.hamburger').click();
            }
        });

        function confirmLogout() {
            if (confirm('Are you sure you want to logout?')) {
                // Change this to point to your process_logout.php
                window.location.href = '../login/process_logout.php';
            }
        }

        // Edit Modal Functions
        function openEditModal(credentialId) {
            // Fetch credential data
            fetch(`get_credential.php?id=${credentialId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const credential = data.credential;
                        
                        // Populate form fields
                        document.getElementById('edit_credential_id').value = credential.credential_id;
                        document.getElementById('edit_credential_type').value = credential.credential_type;
                        document.getElementById('edit_credential_name').value = credential.credential_name;
                        document.getElementById('edit_issued_by').value = credential.issued_by;
                        document.getElementById('edit_issued_date').value = credential.issued_date;
                        document.getElementById('edit_expiry_date').value = credential.expiry_date || '';
                        
                        // Show current file name
                        const fileName = credential.credential_name;
                        document.getElementById('current-file-name').textContent = fileName;
                        
                        // Show modal
                        document.getElementById('editModal').style.display = 'block';
                    } else {
                        alert('Error loading credential data: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while loading credential data');
                });
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
            document.getElementById('editCredentialForm').reset();
            document.getElementById('current-file-name').textContent = '';
            document.getElementById('edit-file-name').textContent = 'No file chosen';
        }

        // Handle edit form submission
        document.getElementById('editCredentialForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const credentialId = document.getElementById('edit_credential_id').value;
            
            fetch('update_credential.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Credential updated successfully!');
                    closeEditModal();
                    window.location.reload(); // Refresh to show changes
                } else {
                    alert('Error updating credential: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the credential');
            });
        });

        // Update file name display for edit modal
        document.getElementById('edit_credential_file').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'No file chosen';
            document.getElementById('edit-file-name').textContent = fileName;
        });
    </script>
    <script src="help.js?v=<?php echo time(); ?>"></script>
    <script src="../scripts.js?v=<?php echo time(); ?>"></script>
</body>
</html>