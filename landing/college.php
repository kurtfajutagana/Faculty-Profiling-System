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

// College details mapping (Programs, Mission, and Focus Areas)
$college_details = [
    1 => [
        'icon' => 'fa-flask',
        'desc' => 'The College of Arts and Science is dedicated to providing students with foundational and specialized knowledge in liberal arts, natural sciences, languages, mathematics, and social sciences.',
        'programs' => [
            'Bachelor of Arts in Communication (BA Comm)',
            'Bachelor of Science in Psychology (BS Psych)',
            'Bachelor of Science in Mathematics (BS Math)',
            'Bachelor of Science in Political Science (BS PolSci)'
        ],
        'core_competencies' => [
            'Critical Thinking & Ethical Research',
            'Scientific Inquiry & Quantitative Analysis',
            'Advanced Multilingual & Intercultural Communication',
            'Community Advocacy & Social Science Research'
        ]
    ],
    2 => [
        'icon' => 'fa-chart-line',
        'desc' => 'The College of Business and Accountancy develops ethical, innovative, and competent business leaders, accountants, and entrepreneurs prepared for competitive global corporate environments.',
        'programs' => [
            'Bachelor of Science in Accountancy (BSA)',
            'Bachelor of Science in Business Administration - Financial Management (BSBA-FM)',
            'Bachelor of Science in Business Administration - Marketing Management (BSBA-MM)',
            'Bachelor of Science in Entrepreneurship (BS Entrep)'
        ],
        'core_competencies' => [
            'Financial Analysis & Auditing Standards',
            'Strategic Corporate Management',
            'Digital Marketing & Consumer Insights',
            'Corporate Governance & Ethical Decision Making'
        ]
    ],
    3 => [
        'icon' => 'fa-laptop-code',
        'desc' => 'The College of Computer Studies delivers industry-aligned curriculum in information technology, computer science, and software engineering to drive regional and national digital transformation.',
        'programs' => [
            'Bachelor of Science in Information Technology (BSIT)',
            'Bachelor of Science in Computer Science (BSCS)',
            'Associate in Computer Technology (ACT)'
        ],
        'core_competencies' => [
            'Full-Stack Software Development & Cloud Computing',
            'Cybersecurity, Network Administration & Infrastructure',
            'Database Architecture & Big Data Analytics',
            'Artificial Intelligence & Machine Learning Applications'
        ]
    ],
    4 => [
        'icon' => 'fa-chalkboard-teacher',
        'desc' => 'The College of Education prepares dedicated, pedagogical experts and academic leaders equipped with innovative teaching methodologies, values education, and research capability.',
        'programs' => [
            'Bachelor of Elementary Education (BEEd)',
            'Bachelor of Secondary Education - Major in English (BSEd-Eng)',
            'Bachelor of Secondary Education - Major in Mathematics (BSEd-Math)',
            'Bachelor of Secondary Education - Major in Science (BSEd-Sci)',
            'Bachelor of Secondary Education - Major in Social Studies (BSEd-SocSci)'
        ],
        'core_competencies' => [
            'Modern Instructional Design & Technology Integration',
            'Educational Assessment & Curriculum Development',
            'Child & Adolescent Learner Psychology',
            'Action Research & Community Learning Programs'
        ]
    ],
    5 => [
        'icon' => 'fa-cogs',
        'desc' => 'The College of Engineering fosters technical proficiency, engineering design, sustainable infrastructure research, and innovative problem-solving skills across various engineering disciplines.',
        'programs' => [
            'Bachelor of Science in Civil Engineering (BSCE)',
            'Bachelor of Science in Electrical Engineering (BSEE)',
            'Bachelor of Science in Industrial Engineering (BSIE)',
            'Bachelor of Science in Electronics Engineering (BSECE)',
            'Bachelor of Science in Computer Engineering (BSCpE)'
        ],
        'core_competencies' => [
            'Structural Design & Geotechnical Engineering',
            'Power Systems & Renewable Energy Technology',
            'Industrial Process Optimization & Quality Engineering',
            'Robotics, Embedded Systems & Automation'
        ]
    ],
    6 => [
        'icon' => 'fa-hotel',
        'desc' => 'The College of Hospitality Management prepares students for dynamic management careers in global tourism, hotel operations, culinary arts, airline services, and event management.',
        'programs' => [
            'Bachelor of Science in Hospitality Management (BSHM)',
            'Bachelor of Science in Tourism Management (BSTM)'
        ],
        'core_competencies' => [
            'International Hospitality & Hotel Operations',
            'Culinary Arts & Food Beverage Service Management',
            'Sustainable Ecotourism & Destination Marketing',
            'MICE (Meetings, Incentives, Conferences, Exhibitions) Planning'
        ]
    ],
    7 => [
        'icon' => 'fa-user-nurse',
        'desc' => 'The College of Nursing provides world-class clinical nursing education, evidence-based healthcare practice, patient safety standards, and community healthcare services.',
        'programs' => [
            'Bachelor of Science in Nursing (BSN)'
        ],
        'core_competencies' => [
            'Comprehensive Clinical Nursing & Patient Care',
            'Critical Care & Emergency Healthcare Management',
            'Community Health Promotion & Disease Prevention',
            'Evidence-Based Nursing Research & Clinical Ethics'
        ]
    ]
];

$details = $college_details[$college_id] ?? $college_details[1];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($college['college_name']) ?> | PLP Faculty Profiling</title>
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="stylesheet" href="../css/styles.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .college-container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .college-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            margin-bottom: 30px;
        }
        .college-header-title {
            color: #007c2c;
            font-size: 2.1rem;
            margin-top: 0;
            margin-bottom: 15px;
            border-bottom: 3px solid #007c2c;
            padding-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .college-desc {
            font-size: 1.1rem;
            line-height: 1.7;
            color: #444;
            margin-bottom: 30px;
        }
        .section-title {
            color: #007c2c;
            font-size: 1.35rem;
            margin-top: 30px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .programs-list {
            list-style: none;
            padding: 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 12px;
            margin-bottom: 30px;
        }
        .programs-list li {
            background: #f4fbf5;
            border-left: 4px solid #007c2c;
            padding: 12px 18px;
            border-radius: 0 6px 6px 0;
            font-weight: 600;
            color: #2e7d32;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .competencies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 15px;
            margin-bottom: 35px;
        }
        .competency-item {
            background: #fdfdfd;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #555;
            font-size: 0.95rem;
        }
        .competency-item i {
            color: #007c2c;
        }
        .portal-access-box {
            background: linear-gradient(135deg, #007c2c 0%, #00501c 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 30px;
        }
        .portal-access-box h3 {
            margin: 0 0 8px 0;
            font-size: 1.3rem;
        }
        .portal-access-box p {
            margin: 0;
            font-size: 0.95rem;
            opacity: 0.9;
            max-width: 600px;
            line-height: 1.5;
        }
        .portal-login-btn {
            background: #ffffff;
            color: #007c2c;
            font-weight: bold;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            transition: transform 0.2s, background 0.2s;
        }
        .portal-login-btn:hover {
            background: #f0f0f0;
            transform: translateY(-2px);
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

        @media (max-width: 768px) {
            .college-container {
                margin: 20px auto;
                padding: 0 12px;
            }
            .college-card {
                padding: 20px 15px;
            }
            .college-header-title {
                font-size: 1.4rem;
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            .programs-list {
                grid-template-columns: 1fr;
            }
            .portal-access-box {
                padding: 20px 15px;
                flex-direction: column;
                align-items: flex-start;
            }
            .portal-login-btn {
                width: 100%;
                justify-content: center;
                box-sizing: border-box;
            }
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
                <i class="fas <?= htmlspecialchars($details['icon']) ?>"></i>
                <?= htmlspecialchars($college['college_name']) ?>
            </h1>
            <p class="college-desc">
                <?= htmlspecialchars($details['desc']) ?>
            </p>

            <h2 class="section-title">
                <i class="fas fa-graduation-cap"></i> Academic Degree Programs
            </h2>
            <ul class="programs-list">
                <?php foreach ($details['programs'] as $prog): ?>
                    <li><i class="fas fa-check-circle"></i> <?= htmlspecialchars($prog) ?></li>
                <?php endforeach; ?>
            </ul>

            <h2 class="section-title">
                <i class="fas fa-award"></i> Core Focus & Key Competencies
            </h2>
            <div class="competencies-grid">
                <?php foreach ($details['core_competencies'] as $comp): ?>
                    <div class="competency-item">
                        <i class="fas fa-star"></i>
                        <span><?= htmlspecialchars($comp) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Secure Portal Access -->
            <div class="portal-access-box">
                <div>
                    <h3><i class="fas fa-shield-alt"></i> Faculty & Staff Information</h3>
                    <p>In accordance with institutional data privacy policies, detailed faculty profiles, credentials, and teaching load records are accessible only to authorized personnel.</p>
                </div>
                <a href="../login/index.php" class="portal-login-btn">
                    <i class="fas fa-lock"></i> Portal Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>
