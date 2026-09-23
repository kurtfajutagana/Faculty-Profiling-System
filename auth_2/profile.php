<?php
session_start();
if (!isset($_SESSION['faculty_id'])) {
    header('Location: ../landing/index.php');
    exit();
}

if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
    unset($_SESSION['error_message']);
}

if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
    unset($_SESSION['success_message']);
}

// Database connection
require_once('../db_connection.php');
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Faculty</title>
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="stylesheet" href="../css/faculty_style.css?v=<?php echo time(); ?>"/>
    <link rel="stylesheet" href="../css/profile.css?v=<?php echo time(); ?>"/>
    <link rel="stylesheet" href="../css/help.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/themes.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
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
        <li><a href="home.php"><img src="../images/home.png" alt="Home Icon" class="menu-icon">HOME</a></li>
        <li><a href="profile.php" class="active"><img src="../images/profile.png" alt="Profile Icon" class="menu-icon">PROFILE</a></li>
        <li><a href="teachingload.php"><img src="../images/teachingload.png" alt="Teaching Icon" class="menu-icon">TEACHING LOAD</a></li>
        <li><a href="credentials.php"><img src="../images/credentials.png" alt="Credentials Icon" class="menu-icon">CREDENTIALS</a></li>
        <li><a href="setting.php"><img src="../images/setting.png" alt="Settings Icon" class="menu-icon">SETTINGS</a></li>
      </ul>
    </nav>

    <div class="logout-section">
            <a href="#" onclick="confirmLogout()"><img src="../images/logout.png" alt="Logout Icon" class="menu-icon">LOGOUT</a>
        </div>
    </div>

    <div class="pds-container">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success-message">
                <?php echo $_SESSION['success_message']; ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        
        <div class="pds-header">
            <img src="../images/logo.png" alt="Logo" />
            <div class="header-text">
                <h1>PERSONAL DATA SHEET</h1>
                <p>PAMANTASAN NG LUNGSOD NG PASIG</p>
            </div>
        </div>

        <?php include 'profile_sections/personal_info.php'; ?>
        <?php include 'profile_sections/educational_background.php'; ?>
        <?php include 'profile_sections/civil_service.php'; ?>
        <?php include 'profile_sections/work_experience.php'; ?>
        <?php include 'profile_sections/training_programs.php'; ?>
        
        <!-- Download Button -->
        <div style="text-align: center;">
            <a href="download_pds.php" class="download-button">
                <i class="fas fa-download"></i> Download PDS
            </a>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="modal">
        <div class="modal-content" style="width: 400px; text-align: center;">
            <h3>Confirm Deletion</h3>
            <p>Are you sure you want to delete this record?</p>
            <div class="form-actions">
                <button type="button" class="save-btn" id="confirmDeleteBtn">Yes, Delete</button>
                <button type="button" class="cancel-btn" onclick="closeModal('confirmModal')">Cancel</button>
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

        // Modal functions
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        // Open modals for adding new records
        function openEducationModal() {
            document.getElementById('educationForm').reset();
            document.getElementById('education_id').value = '';
            openModal('educationModal');
        }
        
        function openCivilServiceModal() {
            document.getElementById('civilServiceForm').reset();
            document.getElementById('civil_id').value = '';
            openModal('civilServiceModal');
        }
        
        function openWorkExperienceModal() {
            document.getElementById('workExperienceForm').reset();
            document.getElementById('work_id').value = '';
            openModal('workExperienceModal');
        }
        
        function openTrainingModal() {
            document.getElementById('trainingForm').reset();
            document.getElementById('training_id').value = '';
            openModal('trainingModal');
        }
        
        // Function to open edit modal with existing data
        function editEducation(id) {
            fetch(`profile_api/get_education.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    // Populate form fields
                    document.getElementById('education_id').value = data.id;
                    document.getElementById('level').value = data.level;
                    document.getElementById('institution_name').value = data.institution_name;
                    document.getElementById('degree_course').value = data.degree_course === 'N/A' ? '' : data.degree_course;
                    document.getElementById('start_year').value = data.start_year;
                    document.getElementById('end_year').value = data.end_year;
                    document.getElementById('honors').value = data.honors === 'N/A' ? '' : data.honors;
                    
                    // Open the modal
                    openModal('educationModal');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load education data');
                });
        }

        // Handle form submission with AJAX
        document.getElementById('educationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const formData = new FormData(form);
            const isEdit = !!formData.get('id');
            
            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeModal('educationModal');
                    loadEducationalBackground();
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Request failed');
            });
        });

        // Function to reload educational background section
        function loadEducationalBackground() {
            fetch('profile_api/get_education.php')
                .then(response => response.text())
                .then(html => {
                    // Replace the entire section
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newSection = doc.querySelector('.pds-section');
                    
                    location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    location.reload(); // Fallback if AJAX fails
                });
        }
        
        // Function to open edit modal with existing data
        function editCivilService(id) {
            fetch(`profile_api/get_service.php?id=${id}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network error');
                    return response.json();
                })
                .then(data => {
                    if (data.error) throw new Error(data.error);
                    
                    document.getElementById('civil_id').value = data.id;
                    document.getElementById('eligibility_type').value = data.eligibility_type;
                    document.getElementById('rating').value = data.rating;
                    document.getElementById('date_of_examination').value = data.date_of_examination;
                    document.getElementById('place_of_examination').value = data.place_of_examination;
                    document.getElementById('license_number').value = data.license_number;
                    document.getElementById('license_validity').value = data.license_validity;
                    
                    openModal('civilServiceModal');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(error.message);
                });
        }
        
        // Handle Civil Service form submission with AJAX
        document.getElementById('civilServiceForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const formData = new FormData(form);
            const isEdit = !!formData.get('id');
            
            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeModal('civilServiceModal');
                    loadCivilServiceSection(); // Reload the section
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Request failed');
            });
        });

        // Function to reload civil service section
        function loadCivilServiceSection() {
            fetch('profile_api/get_all_civil_service.php')
                .then(response => {
                    if (!response.ok) throw new Error('Network error');
                    return response.text();
                })
                .then(html => {
                    // Replace the entire section
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    const newSection = tempDiv.querySelector('.pds-section');
                    
                    location.reload(); // Fallback if AJAX fails
                })
                .catch(error => {
                    console.error('Error:', error);
                    location.reload(); // Fallback if AJAX fails
                });
        }
        
        function editWorkExperience(id) {
            fetch(`profile_api/get_experience.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('work_id').value = data.id;
                    document.getElementById('position_title').value = data.position_title;
                    document.getElementById('department_or_agency').value = data.department_or_agency;
                    document.getElementById('salary_grade_step').value = data.salary_grade_step;
                    document.getElementById('monthly_salary').value = data.monthly_salary;
                    document.getElementById('appointment_status').value = data.appointment_status;
                    document.getElementById('is_government_service').value = data.is_government_service;
                    document.getElementById('date_from').value = data.date_from;
                    document.getElementById('date_to').value = data.date_to;
                    openModal('workExperienceModal');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load work experience data');
                });
        }

        // Handle Work Experience form submission
        document.getElementById('workExperienceForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeModal('workExperienceModal');
                    loadWorkExperienceSection();
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Request failed');
            });
        });

        // Function to reload work experience section
        function loadWorkExperienceSection() {
            fetch('profile_api/get_experience.php')
                .then(response => response.text())
                .then(html => {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    const newSection = tempDiv.querySelector('.pds-section');
                    
                    location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    location.reload();
                });
        }

        // Handle Training form submission
        document.getElementById('trainingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const hoursInput = document.getElementById('number_of_hours');
            const hoursValue = parseInt(hoursInput.value);
            
            // Final validation check
            if (isNaN(hoursValue)) {
                alert('Please enter a valid number of hours');
                hoursInput.focus();
                return;
            }
            
            if (hoursValue < 1) {
                alert('Number of hours must be at least 1');
                hoursInput.focus();
                return;
            }
            
            // Rest of your form submission code
            const form = this;
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeModal('trainingModal');
                    loadTrainingSection();
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Request failed');
            });
        });

        // Function to reload training section
        function loadTrainingSection() {
            fetch('profile_api/get_trainings.php')
                .then(response => response.text())
                .then(html => {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    const newSection = tempDiv.querySelector('.pds-section');
                    
                    location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    location.reload();
                });
        }
        
        function editTraining(id) {
            fetch(`profile_api/get_training.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('training_id').value = data.id;
                    document.getElementById('training_title').value = data.training_title;
                    document.getElementById('conducted_by').value = data.conducted_by;
                    document.getElementById('learning_type').value = data.learning_type;
                    document.getElementById('number_of_hours').value = data.number_of_hours;
                    document.getElementById('training_date_from').value = data.date_from;
                    document.getElementById('training_date_to').value = data.date_to;
                    openModal('trainingModal');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load training data');
                });
        }

        // Prevent negative numbers in the hours field
        document.getElementById('number_of_hours').addEventListener('input', function(e) {
            // Remove any negative signs
            this.value = this.value.replace(/^-/, '');
            
            // Ensure minimum value is 1
            if (this.value < 1) this.value = 1;
        });

        // Prevent 'e' (scientific notation) and '-' (negative) key presses
        document.getElementById('number_of_hours').addEventListener('keydown', function(e) {
            // Block 'e' key (scientific notation) and '-' key (negative)
            if (e.key === 'e' || e.key === '-' || e.key === 'E') {
                e.preventDefault();
            }
        });
        
        // Reusable confirmation function
        function confirmDelete(type, id, customMessage) {
            // Set custom message if provided
            const modal = document.getElementById('confirmModal');
            const messageElement = modal.querySelector('p');
            messageElement.textContent = customMessage || 'Are you sure you want to delete this record?';
            
            // Clear previous click handler
            const confirmBtn = document.getElementById('confirmDeleteBtn');
            confirmBtn.onclick = null;
            
            // Set new handler
            confirmBtn.onclick = function() {
                fetch(`profile_api/delete_${type}.php?id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            closeModal('confirmModal');
                            // Reload the specific section or page
                            if (typeof reloadSection === 'function') {
                                reloadSection();
                            } else {
                                location.reload();
                            }
                        } else {
                            alert('Error: ' + data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Delete failed');
                    });
            };
            
            openModal('confirmModal');
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
        
        
        // Toggle edit mode for a field
        function toggleField(element) {
            const parent = element.closest('.editable');
            const displayValue = parent.querySelector('.display-value');
            const editValue = parent.querySelector('.edit-value');
            const editToggle = parent.querySelector('.edit-toggle');
            const formActions = parent.closest('.pds-section').querySelector('.form-actions');

            // Start edit
            displayValue.style.display = 'none';
            editValue.style.display = '';
            if (editToggle) editToggle.style.display = 'none';
            if (formActions) formActions.style.display = 'block';
        }

        function toggleEdit(sectionId) {
            const section = document.getElementById(sectionId);
            const editFields = section.querySelectorAll('.editable');
            const formActions = section.querySelector('.form-actions');

            // Check if already in edit mode
            const alreadyEditing = [...editFields].some(field => {
                const editValue = field.querySelector('.edit-value');
                return editValue.style.display === '';
            });

            if (alreadyEditing) return;

            // Enable edit mode for all fields
            editFields.forEach(field => {
                const displayValue = field.querySelector('.display-value');
                const editValue = field.querySelector('.edit-value');
                const editToggle = field.querySelector('.edit-toggle');

                displayValue.style.display = 'none';
                editValue.style.display = '';
                if (editToggle) editToggle.style.display = 'none';
            });

            if (formActions) formActions.style.display = 'block';
        }

        function submitPersonalInfo(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            // Show loading indicator
            const saveBtn = form.querySelector('.save-btn');
            const originalBtnText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            saveBtn.disabled = true;

            fetch('profile_api/save_pds.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // First check if response is OK (status 200-299)
                if (!response.ok) {
                    // If not OK, try to parse the error response
                    return response.json().then(errData => {
                        // Create a new error with the server's message
                        const error = new Error(errData.message || 'Server error');
                        error.response = response;
                        throw error;
                    }).catch(() => {
                        // If we can't parse the JSON, throw generic error
                        throw new Error(`HTTP error! status: ${response.status}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert(data.message || "Personal information updated successfully!");
                    cancelEdit('personal-info');
                    location.reload();
                } else {
                    // Show the error message from server
                    const errorMsg = data.message || data.error || "Error updating information";
                    alert(errorMsg);
                    
                    // Keep form in edit mode if there's an error
                    const editFields = document.querySelectorAll('#personal-info .editable');
                    editFields.forEach(field => {
                        const editValue = field.querySelector('.edit-value');
                        if (editValue) editValue.style.display = '';
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Show the actual error message
                alert(error.message || "An error occurred while saving. Please try again.");
            })
            .finally(() => {
                saveBtn.innerHTML = originalBtnText;
                saveBtn.disabled = false;
            });
        }

        function cancelEdit(sectionId) {
            const section = document.getElementById(sectionId);
            const editFields = section.querySelectorAll('.editable');
            const formActions = section.querySelector('.form-actions');

            editFields.forEach(field => {
                const displayValue = field.querySelector('.display-value');
                const editValue = field.querySelector('.edit-value');
                const editToggle = field.querySelector('.edit-toggle');

                displayValue.style.display = '';
                editValue.style.display = 'none';
                if (editToggle) editToggle.style.display = '';
            });

            if (formActions) formActions.style.display = 'none';
        }
    </script>
    <script src="help.js?v=<?php echo time(); ?>"></script>
    <script src="../scripts.js?v=<?php echo time(); ?>"></script>
</body>
</html>