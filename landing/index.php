<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Profiling System | Pamantasan ng Lungsod ng Pasig</title>
    <link rel="stylesheet" href="../css/styles.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .colleges-preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 30px;
            width: 100%;
        }
        .college-preview-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
            color: #333;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-top: 4px solid #007c2c;
        }
        .college-preview-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.25);
        }
        .college-preview-card h3 {
            margin: 0 0 10px 0;
            color: #007c2c;
            font-size: 1.15rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .college-preview-card p {
            font-size: 0.9rem;
            color: #666;
            line-height: 1.4;
            margin: 0 0 15px 0;
        }
        .view-roster-btn {
            font-size: 0.85rem;
            font-weight: bold;
            color: #007c2c;
            display: inline-flex;
            align-items: center;
            gap: 5px;
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
                    <a href="college.php?id=1">College of Arts and Science</a>
                    <a href="college.php?id=2">College of Business and Accountancy</a>
                    <a href="college.php?id=3">College of Computer Studies</a>
                    <a href="college.php?id=4">College of Education</a>
                    <a href="college.php?id=5">College of Engineering</a>
                    <a href="college.php?id=6">College of Hospitality Management</a>
                    <a href="college.php?id=7">College of Nursing</a>
                </div>
            </div>
            <a href="../login/index.php"><i class="fas fa-sign-in-alt"></i> Log In</a>
        </div>
    </div>

    <div class="main-content" style="flex-direction: column; max-width: 1200px; margin: 0 auto;">
        <div class="info-box" style="width: 100%; box-sizing: border-box;">
            <h1>Welcome to the PLP Faculty Profiling System</h1>
            <p>
                The Faculty Profiling System of Pamantasan ng Lungsod ng Pasig is designed to streamline 
                the management and evaluation of faculty data. It enables efficient record-keeping, 
                performance tracking, and departmental assignments.
            </p>
            <p>
                This system supports the university’s commitment to academic excellence by providing 
                administrators with accurate, up-to-date information about the teaching staff across all colleges.
            </p>

            <h2 style="color: #007c2c; margin-top: 35px; border-bottom: 2px solid #e0e0e0; padding-bottom: 10px;">
                <i class="fas fa-graduation-cap"></i> Explore Academic Colleges & Faculty Rosters
            </h2>

            <div class="colleges-preview-grid">
                <a href="college.php?id=1" class="college-preview-card">
                    <div>
                        <h3><i class="fas fa-flask"></i> College of Arts & Science</h3>
                        <p>Liberal arts, natural sciences, languages, mathematics, and social science programs.</p>
                    </div>
                    <span class="view-roster-btn">View Faculty Directory <i class="fas fa-arrow-right"></i></span>
                </a>

                <a href="college.php?id=2" class="college-preview-card">
                    <div>
                        <h3><i class="fas fa-chart-line"></i> College of Business & Accountancy</h3>
                        <p>Business administration, accountancy, finance, marketing, and entrepreneurship.</p>
                    </div>
                    <span class="view-roster-btn">View Faculty Directory <i class="fas fa-arrow-right"></i></span>
                </a>

                <a href="college.php?id=3" class="college-preview-card">
                    <div>
                        <h3><i class="fas fa-laptop-code"></i> College of Computer Studies</h3>
                        <p>Information technology, computer science, software development, and systems design.</p>
                    </div>
                    <span class="view-roster-btn">View Faculty Directory <i class="fas fa-arrow-right"></i></span>
                </a>

                <a href="college.php?id=4" class="college-preview-card">
                    <div>
                        <h3><i class="fas fa-chalkboard-teacher"></i> College of Education</h3>
                        <p>Teacher training, elementary and secondary pedagogy, and educational leadership.</p>
                    </div>
                    <span class="view-roster-btn">View Faculty Directory <i class="fas fa-arrow-right"></i></span>
                </a>

                <a href="college.php?id=5" class="college-preview-card">
                    <div>
                        <h3><i class="fas fa-cogs"></i> College of Engineering</h3>
                        <p>Civil, mechanical, electrical, and computer engineering disciplines.</p>
                    </div>
                    <span class="view-roster-btn">View Faculty Directory <i class="fas fa-arrow-right"></i></span>
                </a>

                <a href="college.php?id=6" class="college-preview-card">
                    <div>
                        <h3><i class="fas fa-hotel"></i> College of Hospitality Management</h3>
                        <p>Hotel and restaurant administration, tourism, culinary arts, and event management.</p>
                    </div>
                    <span class="view-roster-btn">View Faculty Directory <i class="fas fa-arrow-right"></i></span>
                </a>

                <a href="college.php?id=7" class="college-preview-card">
                    <div>
                        <h3><i class="fas fa-user-nurse"></i> College of Nursing</h3>
                        <p>Clinical nursing, community health, patient care, and healthcare sciences.</p>
                    </div>
                    <span class="view-roster-btn">View Faculty Directory <i class="fas fa-arrow-right"></i></span>
                </a>
            </div>
        </div>
    </div>
</body>
</html>