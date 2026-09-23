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

// Database connection
require_once('../db_connection.php');

// Get all teaching loads for the faculty
$teachingLoads = [];
$error = null;
$success = null;

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['teaching_file'])) {
    $displayName = $_POST['display_name'];
    $semester = $_POST['semester'];
    $startYear = $_POST['start_year'];
    $endYear = $_POST['end_year'];
    $regularLoads = $_POST['regular_loads'];
    $overloadUnits = $_POST['overload_units'];
    $totalLoads = $regularLoads + $overloadUnits;
    
    // Validate loads
    if ($regularLoads < 0 || $overloadUnits < 0) {
        $_SESSION['upload_alert'] = 'error';
        $_SESSION['error_message'] = "Load values cannot be negative";
        header('Location: teachingload.php');
        exit();
    }
    
    if ($totalLoads > 50) {
        $_SESSION['upload_alert'] = 'error';
        $_SESSION['error_message'] = "Total loads cannot exceed 50 units";
        header('Location: teachingload.php');
        exit();
    }

    $file = $_FILES['teaching_file'];
    $originalFileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];

    // Validate academic year range
    if ($endYear < $startYear) {
         $_SESSION['upload_alert'] = 'error';
        $_SESSION['error_message'] = "End year cannot be less than start year";
        header('Location: teachingload.php');
        exit();
    } elseif (($endYear - $startYear) > 1) {
        $_SESSION['upload_alert'] = 'error';
        $_SESSION['error_message'] = "End year must not be ahead of start year by more than 1 year";
        header('Location: teachingload.php');
        exit();;
    }
    
    // Validate file (PDF only, max 5MB)
    $fileExt = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
    $allowed = ['pdf'];
    
    if (in_array($fileExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize < 5000000) { // 5MB
                $newFileName = uniqid('', true) . '.' . $fileExt;
                $fileDestination = 'uploads/teaching_loads/' . $newFileName;
                
                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    try {
                        $stmt = $conn->prepare("INSERT INTO teaching_load (faculty_id, file_name, semester, start_year, end_year, regular_loads, overload_units, total_loads, file_path) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("sssssiiss", $_SESSION['faculty_id'], $displayName, $semester, $startYear, $endYear, $regularLoads, $overloadUnits, $totalLoads, $fileDestination);
                        $stmt->execute();
                        $stmt->close();
                        
                        // Set success flag and redirect
                        $_SESSION['upload_alert'] = 'success';
                        header('Location: teachingload.php');
                        exit();
                    } catch (Exception $e) {
                        // Set error flag and redirect
                        $_SESSION['upload_alert'] = 'error';
                        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
                        unlink($fileDestination);
                        header('Location: teachingload.php');
                        exit();
                    }
                } else {
                    $_SESSION['upload_alert'] = 'error';
                    $_SESSION['error_message'] = "There was an error uploading your file.";
                    header('Location: teachingload.php');
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
$query = "SELECT * FROM teaching_load WHERE faculty_id = ?";
$params = [$_SESSION['faculty_id']];
$types = "s";

// Add filters if they exist in GET parameters
if (isset($_GET['status']) && !empty($_GET['status'])) {
    $query .= " AND status = ?";
    $params[] = $_GET['status'];
    $types .= "s";
}

if (isset($_GET['semester']) && !empty($_GET['semester'])) {
    $query .= " AND semester = ?";
    $params[] = $_GET['semester'];
    $types .= "s";
}

if (isset($_GET['year']) && !empty($_GET['year'])) {
    $query .= " AND start_year = ?";
    $params[] = $_GET['year'];
    $types .= "s";
}

// Add sorting
$query .= " ORDER BY created_at DESC";

try {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $teachingLoads = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teaching Schedule | Faculty</title>
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="stylesheet" href="../css/faculty_style.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="../css/teachingload.css?v=<?php echo time(); ?>">
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
                <li><a href="teachingload.php" class="active"><img src="../images/teachingload.png" alt="Teaching Icon" class="menu-icon">TEACHING LOAD</a></li>
                <li><a href="credentials.php"><img src="../images/credentials.png" alt="Credentials Icon" class="menu-icon">CREDENTIALS</a></li>
                <li><a href="setting.php"><img src="../images/setting.png" alt="Settings Icon" class="menu-icon">SETTINGS</a></li>
            </ul>
        </nav>

        <div class="logout-section">
            <a href="#" onclick="confirmLogout()"><img src="../images/logout.png" alt="Logout Icon" class="menu-icon">LOGOUT</a>
        </div>
    </div>   

    <div class="main-content">
        <div class="header-content">
            <h2><i class="fas fa-calendar-alt"></i> My Teaching Load</h2>
        </div>

        <!-- Upload Card -->
        <div class="card upload-card">
            <h2><i class="fas fa-upload"></i> Upload Teaching Load</h2>
            <form action="teachingload.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="display_name">Document Name:</label>
                    <input type="text" id="display_name" name="display_name" placeholder="e.g. Teaching Load AY 2023-2024" required>
                </div>
                
                <div class="form-group">
                    <label for="semester">Semester:</label>
                    <select id="semester" name="semester" required>
                        <option value="">Select Semester</option>
                        <option value="First Semester">First Semester</option>
                        <option value="Second Semester">Second Semester</option>
                        <option value="Summer">Summer</option>
                    </select>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="start_year">Start Year:</label>
                        <select id="start_year" name="start_year" required>
                            <option value="">Select Year</option>
                            <?php 
                            $currentYear = date('Y');
                            for ($year = $currentYear; $year >= 2016; $year--) {
                                echo "<option value='$year'>$year</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="end_year">End Year:</label>
                        <select id="end_year" name="end_year" required>
                            <option value="">Select Year</option>
                            <?php 
                            for ($year = $currentYear + 1; $year >= 2017; $year--) {
                                echo "<option value='$year'>$year</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="regular_loads">Regular Loads (units):</label>
                        <input type="number" id="regular_loads" name="regular_loads" placeholder="e.g. 18" min="0" max="50" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="overload_units">Overload Units:</label>
                        <input type="number" id="overload_units" name="overload_units" placeholder="e.g. 6" min="0" max="50" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Total Loads: <span id="total-loads-display">0</span> units</label>
                </div>
                
                <div class="form-group file-upload">
                    <label for="teaching_file">Upload PDF File (max 5MB):</label>
                    <div class="file-upload-wrapper">
                        <input type="file" id="teaching_file" name="teaching_file" accept=".pdf" required>
                        <label for="teaching_file" class="file-upload-label">
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
            <h2><i class="fas fa-list"></i> Uploaded Teaching Loads</h2>
            
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
                            <label for="semester_filter">Semester:</label>
                            <select id="semester_filter" name="semester" onchange="this.form.submit()">
                                <option value="">All Semesters</option>
                                <option value="First Semester" <?php echo (isset($_GET['semester']) && $_GET['semester'] == 'First Semester') ? 'selected' : ''; ?>>First Semester</option>
                                <option value="Second Semester" <?php echo (isset($_GET['semester']) && $_GET['semester'] == 'Second Semester') ? 'selected' : ''; ?>>Second Semester</option>
                                <option value="Summer" <?php echo (isset($_GET['semester']) && $_GET['semester'] == 'Summer') ? 'selected' : ''; ?>>Summer</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="year_filter">School Year:</label>
                            <select id="year_filter" name="year" onchange="this.form.submit()">
                                <option value="">All Years</option>
                                <?php
                                $currentYear = date('Y');
                                for ($year = $currentYear; $year >= 2017; $year--) {
                                    $yearRange = $year . '-' . ($year + 1);
                                    $selected = (isset($_GET['year']) && $_GET['year'] == $year) ? 'selected' : '';
                                    echo "<option value='$year' $selected>$yearRange</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            
            <?php if (empty($teachingLoads)): ?>
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <p>No teaching loads found matching your criteria.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>File Name</th>
                                <th>Semester</th>
                                <th>Academic Year</th>
                                <th>Regular Loads</th>
                                <th>Overload Units</th>
                                <th>Total Loads</th>
                                <th>Status</th>
                                <th>Date Uploaded</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($teachingLoads as $load): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($load['file_name']); ?></td>
                                    <td><?php echo htmlspecialchars($load['semester']); ?></td>
                                    <td><?php echo htmlspecialchars($load['start_year']) . ' - ' . htmlspecialchars($load['end_year']); ?></td>
                                    <td><?php echo htmlspecialchars($load['regular_loads']); ?></td>
                                    <td><?php echo htmlspecialchars($load['overload_units']); ?></td>
                                    <td><?php echo htmlspecialchars($load['total_loads']); ?></td>
                                    <td>
                                        <div class="status-cell">
                                            <span class="status-badge <?php echo strtolower($load['status']); ?>">
                                                <?php echo htmlspecialchars($load['status']); ?>
                                            </span>
                                            <?php if ($load['status'] === 'Rejected' && !empty($load['reason'])): ?>
                                                <span class="reason-tooltip" title="<?php echo htmlspecialchars($load['reason']); ?>">
                                                    <i class="fas fa-info-circle"></i>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td><?php echo date('M d, Y h:i A', strtotime($load['created_at'])); ?></td>
                                    <td class="actions">
                                        <a href="<?php echo $load['file_path']; ?>" target="_blank" class="btn-view" title="View"><i class="fas fa-eye"></i></a>
                                        <a href="<?php echo $load['file_path']; ?>" download class="btn-download" title="Download"><i class="fas fa-download"></i></a>
                                        <?php if ($load['status'] === 'Pending' || $load['status'] === 'Rejected'): ?>
                                        <a href="#" onclick="openEditModal('<?php echo $load['load_id']; ?>'); return false;" class="btn-action btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
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

    <!-- Edit Modal -->
    <div id="editTeachingLoadModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2><i class="fas fa-edit"></i> Edit Teaching Load</h2>
            <form id="editTeachingLoadForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="edit_load_id" name="load_id">
                
                <div class="form-group">
                    <label for="edit_display_name">Document Name:</label>
                    <input type="text" id="edit_display_name" name="display_name" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_semester">Semester:</label>
                    <select id="edit_semester" name="semester" required>
                        <option value="First Semester">First Semester</option>
                        <option value="Second Semester">Second Semester</option>
                        <option value="Summer">Summer</option>
                    </select>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_start_year">Start Year:</label>
                        <select id="edit_start_year" name="start_year" required>
                            <?php 
                            $currentYear = date('Y');
                            for ($year = $currentYear; $year >= 2016; $year--) {
                                echo "<option value='$year'>$year</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_end_year">End Year:</label>
                        <select id="edit_end_year" name="end_year" required>
                            <?php 
                            for ($year = $currentYear + 1; $year >= 2017; $year--) {
                                echo "<option value='$year'>$year</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_regular_loads">Regular Loads (units):</label>
                        <input type="number" id="edit_regular_loads" name="regular_loads" min="0" max="50" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_overload_units">Overload Units:</label>
                        <input type="number" id="edit_overload_units" name="overload_units" min="0" max="50" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Total Loads: <span id="edit-total-loads-display">0</span> units</label>
                </div>
                
                <div class="form-group file-upload">
                    <label for="edit_teaching_file">Change PDF File (optional):</label>
                    <div class="file-upload-wrapper">
                        <input type="file" id="edit_teaching_file" name="teaching_file" accept=".pdf">
                        <label for="edit_teaching_file" class="file-upload-label">
                            <i class="fas fa-cloud-upload"></i>
                            <span class="file-upload-text">Choose a file</span>
                            <span class="file-upload-filename" id="edit-file-name">No file chosen</span>
                        </label>
                    </div>
                    <p class="file-info">Current file: <span id="current-file-name"></span></p>
                </div>
                
                <div class="form-buttons">
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn-save">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    
    <?php include 'help.php'; ?>

    <script>
        window.onload = function() {
            <?php if (isset($_SESSION['upload_alert'])): ?>
                <?php if ($_SESSION['upload_alert'] === 'success'): ?>
                    alert('Teaching load uploaded successfully!');
                <?php elseif ($_SESSION['upload_alert'] === 'error'): ?>
                    alert('<?php echo isset($_SESSION['error_message']) ? addslashes($_SESSION['error_message']) : "Error uploading file"; ?>');
                <?php endif; ?>
                <?php 
                unset($_SESSION['upload_alert']);
                unset($_SESSION['error_message']);
                ?>
            <?php endif; ?>
            
            // Update total loads display when page loads
            updateTotalLoads();
        };

        // Calculate and display total loads
        function updateTotalLoads() {
            const regularLoads = parseInt(document.getElementById('regular_loads').value) || 0;
            const overloadUnits = parseInt(document.getElementById('overload_units').value) || 0;
            const totalLoads = regularLoads + overloadUnits;
            document.getElementById('total-loads-display').textContent = totalLoads;
        }

        // Add event listeners to update total loads when values change
        document.getElementById('regular_loads').addEventListener('input', updateTotalLoads);
        document.getElementById('overload_units').addEventListener('input', updateTotalLoads);

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

        // File name display
        document.getElementById('teaching_file').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'No file chosen';
            document.getElementById('file-name').textContent = fileName;
        });

        // Edit Modal Functions
        function openEditModal(loadId) {
            // Fetch the teaching load data via AJAX
            fetch(`get_teaching_load.php?id=${loadId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const load = data.load;
                        
                        // Populate the form fields
                        document.getElementById('edit_load_id').value = load.load_id;
                        document.getElementById('edit_display_name').value = load.file_name;
                        document.getElementById('edit_semester').value = load.semester;
                        document.getElementById('edit_start_year').value = load.start_year;
                        document.getElementById('edit_end_year').value = load.end_year;
                        document.getElementById('edit_regular_loads').value = load.regular_loads;
                        document.getElementById('edit_overload_units').value = load.overload_units;
                        document.getElementById('edit-total-loads-display').textContent = load.total_loads;
                        document.getElementById('current-file-name').textContent = load.file_name;
                        
                        // Show the modal
                        document.getElementById('editTeachingLoadModal').style.display = 'block';
                    } else {
                        alert('Error loading teaching load data: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while loading the teaching load data.');
                });
        }

        function closeEditModal() {
            document.getElementById('editTeachingLoadModal').style.display = 'none';
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('editTeachingLoadModal');
            if (event.target == modal) {
                closeEditModal();
            }
        }

        // Close modal with X button
        document.querySelector('.close-modal').addEventListener('click', closeEditModal);

        // Update total loads for edit form
        function updateEditTotalLoads() {
            const regularLoads = parseInt(document.getElementById('edit_regular_loads').value) || 0;
            const overloadUnits = parseInt(document.getElementById('edit_overload_units').value) || 0;
            const totalLoads = regularLoads + overloadUnits;
            document.getElementById('edit-total-loads-display').textContent = totalLoads;
        }

        // Add event listeners for edit form
        document.getElementById('edit_regular_loads').addEventListener('input', updateEditTotalLoads);
        document.getElementById('edit_overload_units').addEventListener('input', updateEditTotalLoads);

        // Handle file name display for edit form
        document.getElementById('edit_teaching_file').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'No file chosen';
            document.getElementById('edit-file-name').textContent = fileName;
        });

        // Handle form submission
        document.getElementById('editTeachingLoadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('update_teaching_load.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Teaching load updated successfully!');
                    closeEditModal();
                    window.location.reload();
                } else {
                    alert('Error updating teaching load: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the teaching load.');
            });
        });
    </script>
    <script src="help.js?v=<?php echo time(); ?>"></script>
    <script src="../scripts.js?v=<?php echo time(); ?>"></script>
</body>
</html>