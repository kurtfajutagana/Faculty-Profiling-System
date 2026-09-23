<?php
require_once '../db_connection.php';

// Get college ID from URL parameter (default to 1)
$college_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Fetch all colleges for navigation
$all_colleges_stmt = $conn->query("SELECT college_id, college_name FROM colleges ORDER BY college_id ASC");
$all_colleges = [];
if ($all_colleges_stmt) {
    while ($row = $all_colleges_stmt->fetch_assoc()) {
        $all_colleges[] = $row;
    }
}

// Fetch specific college details
$stmt = $conn->prepare("SELECT college_id, college_name FROM colleges WHERE college_id = ?");
$stmt->bind_param("i", $college_id);
$stmt->execute();
$college_result = $stmt->get_result();
$college = $college_result->fetch_assoc();

if (!$college) {
    // If not found, fallback to first college
    $college = $all_colleges[0] ?? ['college_id' => 1, 'college_name' => 'College of Arts and Science'];
    $college_id = $college['college_id'];
}

// Fetch active faculty for this college
$faculty_stmt = $conn->prepare("SELECT faculty_id, full_name, email, employment_type, specialization FROM faculty WHERE college_id = ? AND status = 'Active' ORDER BY full_name ASC");
$faculty_stmt->bind_param("i", $college_id);
$faculty_stmt->execute();
$faculty_result = $faculty_stmt->get_result();

$faculty_list = [];
$full_time_count = 0;
$part_time_count = 0;

if ($faculty_result) {
    while ($f = $faculty_result->fetch_assoc()) {
        $faculty_list[] = $f;
        if ($f['employment_type'] === 'Full-Time') {
            $full_time_count++;
        } elseif ($f['employment_type'] === 'Part-Time') {
            $part_time_count++;
        }
    }
}
$total_faculty = count($faculty_list);

// Descriptions for colleges
$college_descriptions = [
    1 => "The College of Arts and Science is dedicated to providing students with foundational and specialized knowledge in liberal arts, natural sciences, and social sciences.",
    2 => "The College of Business and Accountancy develops ethical, innovative, and competent business leaders and accountants prepared for global competitive landscapes.",
    3 => "The College of Computer Studies delivers cutting-edge curriculum in information technology, computer science, and software engineering to drive technological innovation.",
    4 => "The College of Education prepares dedicated educators and academic leaders equipped with modern pedagogical methodologies and strong moral character.",
    5 => "The College of Engineering fosters technical proficiency, research innovation, and problem-solving skills in various engineering disciplines.",
    6 => "The College of Hospitality Management prepares students for dynamic careers in the global tourism, hotel, culinary, and hospitality management sectors.",
    7 => "The College of Nursing provides world-class healthcare education, clinical training, and compassionate patient care practices."
];

$college_desc = $college_descriptions[$college_id] ?? "Dedicated to academic excellence, research advancement, and community service.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($college['college_name']) ?> | PLP Faculty Profiling</title>
    <link rel="stylesheet" href="../css/styles.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .college-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .college-card {
            background: rgba(255, 255, 255, 0.96);
            border-radius: 12px;
            padding: 35px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            margin-bottom: 30px;
        }
        .college-header-title {
            color: #007c2c;
            font-size: 2rem;
            margin-top: 0;
            margin-bottom: 12px;
            border-bottom: 3px solid #007c2c;
            padding-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .college-desc {
            font-size: 1.05rem;
            line-height: 1.6;
            color: #444;
            margin-bottom: 25px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        .stat-item {
            background: #f4fbf5;
            border: 1px solid #c8e6c9;
            border-radius: 8px;
            padding: 15px 20px;
            text-align: center;
        }
        .stat-item h3 {
            font-size: 1.8rem;
            color: #007c2c;
            margin: 0 0 5px 0;
        }
        .stat-item p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
            font-weight: bold;
        }
        .faculty-table-container {
            overflow-x: auto;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .faculty-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            font-size: 0.95rem;
        }
        .faculty-table th {
            background-color: #007c2c;
            color: white;
            padding: 12px 16px;
            text-align: left;
            font-weight: 600;
        }
        .faculty-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #eee;
            color: #333;
        }
        .faculty-table tr:hover {
            background-color: #f9f9f9;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .badge-ft {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        .badge-pt {
            background-color: #fff3e0;
            color: #ef6c00;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #888;
            font-style: italic;
        }
        .back-nav {
            margin-bottom: 20px;
        }
        .back-nav a {
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: bold;
            background: rgba(0, 124, 44, 0.85);
            padding: 8px 16px;
            border-radius: 6px;
            transition: 0.2s;
        }
        .back-nav a:hover {
            background: #00551e;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-section">
            <img src="../images/logo.png" alt="logo of plp">
            <div class="title">PAMANTASAN NG LUNGSOD NG PASIG</div>
        </div>
        <div class="nav">
            <a href="index.php"><i class="fas fa-home"></i> Home</a>
            <div class="dropdown">
                <a href="javascript:void(0)" style="cursor: pointer;"><i class="fas fa-university"></i> Colleges ▾</a>
                <div class="dropdown-content">
                    <?php foreach ($all_colleges as $c): ?>
                        <a href="college.php?id=<?= $c['college_id'] ?>" style="<?= ($c['college_id'] == $college_id) ? 'background-color: #e8f5e9; font-weight: bold; color: #007c2c;' : '' ?>">
                            <?= htmlspecialchars($c['college_name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <a href="../login/index.php"><i class="fas fa-sign-in-alt"></i> Log In</a>
        </div>
    </div>

    <div class="college-container">
        <div class="back-nav">
            <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Main Overview</a>
        </div>

        <div class="college-card">
            <h1 class="college-header-title">
                <i class="fas fa-graduation-cap"></i>
                <?= htmlspecialchars($college['college_name']) ?>
            </h1>
            <p class="college-desc">
                <?= htmlspecialchars($college_desc) ?>
            </p>

            <div class="stats-grid">
                <div class="stat-item">
                    <h3><?= $total_faculty ?></h3>
                    <p>TOTAL FACULTY</p>
                </div>
                <div class="stat-item">
                    <h3><?= $full_time_count ?></h3>
                    <p>FULL-TIME FACULTY</p>
                </div>
                <div class="stat-item">
                    <h3><?= $part_time_count ?></h3>
                    <p>PART-TIME FACULTY</p>
                </div>
            </div>

            <h2 style="color: #222; margin-top: 30px; margin-bottom: 15px; font-size: 1.3rem;">
                <i class="fas fa-users" style="color: #007c2c;"></i> Active Faculty Members Roster
            </h2>

            <?php if (!empty($faculty_list)): ?>
                <div class="faculty-table-container">
                    <table class="faculty-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Faculty Name</th>
                                <th>Specialization</th>
                                <th>Employment Type</th>
                                <th>Contact / Institutional Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($faculty_list as $index => $f): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><strong><?= htmlspecialchars($f['full_name']) ?></strong></td>
                                    <td><?= htmlspecialchars($f['specialization'] ?: 'General') ?></td>
                                    <td>
                                        <span class="badge <?= ($f['employment_type'] === 'Full-Time') ? 'badge-ft' : 'badge-pt' ?>">
                                            <?= htmlspecialchars($f['employment_type'] ?: 'Faculty') ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($f['email']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-folder-open" style="font-size: 2.5rem; margin-bottom: 10px; color: #ccc;"></i>
                    <p>No active faculty records listed for this college yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

