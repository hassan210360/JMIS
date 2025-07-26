<?php include 'header.php';?>
<?php
require_once 'db.php';  // Includes your PDO $conn connection
session_start();
require_once 'auth_check.php'; // Checks if user is logged in

// Get user data
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JMIS - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --sidebar-width: 250px;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            background: linear-gradient(180deg, var(--primary-color), #0b5ed7);
            color: white;
            padding: 20px 0;
            transition: all 0.3s;
            z-index: 1000;
        }
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .sidebar-menu {
            padding: 20px 0;
        }
        .sidebar-menu a {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            display: block;
            text-decoration: none;
            transition: all 0.3s;
        }
        .sidebar-menu a:hover, .sidebar-menu a.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar-menu a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .navbar-custom {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px 20px;
            margin-left: var(--sidebar-width);
        }
        .user-dropdown img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .card-dashboard {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            margin-bottom: 20px;
        }
        .card-dashboard:hover {
            transform: translateY(-5px);
        }
        .card-icon {
            font-size: 2rem;
            color: var(--primary-color);
        }
        .welcome-banner {
            background: linear-gradient(135deg, var(--primary-color), #0b5ed7);
            color: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
        }
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }
            .sidebar.active {
                margin-left: 0;
            }
            .main-content, .navbar-custom {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h4>JMIS</h4>
          <p class="text-muted">JMIS**</p>
        </div>
        
        <div class="sidebar-menu">
            <a href="dashboard.php" class="active"><i class="bi bi-house-door"></i> Dashboard</a>
            
            <?php if ($user_type == 'jobseeker' || $user_type == 'employer' || $user_type == 'admin'): ?>
            <a href="profile.php"><i class="bi bi-person"></i> My Profile</a>
            <?php endif; ?>
            
            <?php if ($user_type == 'jobseeker'): ?>
            <a href="job_search.php"><i class="bi bi-search"></i> Job Search</a>
            <a href="my_applications.php"><i class="bi bi-file-earmark-text"></i> My Applications</a>
            <a href="career_guidance.php"><i class="bi bi-lightbulb"></i> Career Guidance</a>
            <a href="skills_assessment.php"><i class="bi bi-clipboard-check"></i> Skills Assessment</a>
            <?php endif; ?>
            
            <?php if ($user_type == 'employer'): ?>
            <a href="post_job.php"><i class="bi bi-plus-circle"></i> Post a Job</a>
            <a href="manage_jobs.php"><i class="bi bi-briefcase"></i> Manage Jobs</a>
            <a href="candidates.php"><i class="bi bi-people"></i> Candidates</a>
            <?php endif; ?>
            
            <?php if ($user_type == 'admin'): ?>
            <a href="occupation_standards.php"><i class="bi bi-card-checklist"></i> Occupation Standards</a>
            <a href="manage_users.php"><i class="bi bi-people"></i> User Management</a>
            <a href="lmi_reports.php"><i class="bi bi-graph-up"></i> LMIS Reports</a>
            <a href="system_settings.php"><i class="bi bi-gear"></i> System Settings</a>
            <?php endif; ?>
            
            <a href="resources.php"><i class="bi bi-book"></i> Resources</a>
            <a href="help.php"><i class="bi bi-question-circle"></i> Help Center</a>
            
            <div class="mt-4 pt-3 border-top">
                <a href="logout.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand navbar-custom">
            <div class="container-fluid">
                <button class="btn btn-sm d-md-none" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                
                <div class="ms-auto d-flex align-items-center">
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" 
                           id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?php echo getProfileImage($user_id); ?>" alt="User" class="rounded-circle me-2">
                            <span><?php echo getUserName($user_id); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="settings.php"><i class="bi bi-gear me-2"></i> Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-left me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="container-fluid">
            <!-- Welcome Banner -->
            <div class="welcome-banner">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2>Welcome back, <?php echo getUserFirstName($user_id); ?>!</h2>
                        <p class="mb-0">Here's what's happening with your account today.</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <?php if ($user_type == 'jobseeker'): ?>
                        <a href="job_search.php" class="btn btn-light">Find Jobs</a>
                        <?php elseif ($user_type == 'employer'): ?>
                        <a href="post_job.php" class="btn btn-light">Post a Job</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Dashboard Cards -->
            <div class="row">
                <?php if ($user_type == 'jobseeker'): ?>
                <div class="col-md-4">
                    <div class="card card-dashboard">
                        <div class="card-body text-center">
                            <i class="bi bi-briefcase card-icon"></i>
                            <h5 class="card-title mt-3">Job Applications</h5>
                            <h2 class="text-primary"><?php echo getApplicationCount($user_id); ?></h2>
                            <p class="text-muted">Total applications submitted</p>
                            <a href="my_applications.php" class="btn btn-sm btn-outline-primary">View Applications</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card card-dashboard">
                        <div class="card-body text-center">
                            <i class="bi bi-bookmark-check card-icon"></i>
                            <h5 class="card-title mt-3">Recommended Jobs</h5>
                            <h2 class="text-primary"><?php echo getRecommendedJobCount($user_id); ?></h2>
                            <p class="text-muted">Jobs matching your skills</p>
                            <a href="job_recommendations.php" class="btn btn-sm btn-outline-primary">View Jobs</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card card-dashboard">
                        <div class="card-body text-center">
                            <i class="bi bi-graph-up card-icon"></i>
                            <h5 class="card-title mt-3">Career Progress</h5>
                            <div class="progress mt-3" style="height: 10px;">
                                <div class="progress-bar bg-success" style="width: <?php echo getCareerProgress($user_id); ?>%"></div>
                            </div>
                            <p class="text-muted mt-2">Complete your profile for better matches</p>
                            <a href="profile.php" class="btn btn-sm btn-outline-primary">Complete Profile</a>
                        </div>
                    </div>
                </div>
                <?php elseif ($user_type == 'employer'): ?>
                <!-- Employer dashboard cards -->
                <?php elseif ($user_type == 'admin'): ?>
                <!-- Admin dashboard cards -->
                <?php endif; ?>
            </div>

            <!-- Recent Activity Section -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Recent Activity</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <?php foreach(getRecentActivity($user_id) as $activity): ?>
                                <li class="list-group-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="bi bi-<?php echo $activity['icon']; ?> text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <small class="text-muted float-end"><?php echo $activity['time']; ?></small>
                                            <p class="mb-1"><?php echo $activity['message']; ?></p>
                                        </div>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>