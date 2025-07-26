<?php
session_start();
require_once 'auth_check.php';
require_once 'header.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-12">
                <h2 class="mb-0">Career Guidance</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                        <li class="breadcrumb-item active">Career Guidance</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <!-- Career Assessment -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Career Assessment</h5>
                    </div>
                    <div class="card-body">
                        <p>Take our career assessment to discover occupations that match your skills, interests, and personality.</p>
                        <div class="progress mb-3" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: <?php echo getAssessmentProgress($_SESSION['user_id']); ?>%"></div>
                        </div>
                        <a href="career_assessment.php" class="btn btn-primary">
                            <?php echo hasStartedAssessment($_SESSION['user_id']) ? 'Continue Assessment' : 'Start Assessment'; ?>
                        </a>
                    </div>
                </div>

                <!-- Recommended Occupations -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Recommended Occupations</h5>
                    </div>
                    <div class="card-body">
                        <?php if (hasCompletedAssessment($_SESSION['user_id'])): ?>
                            <div class="list-group">
                                <?php foreach(getRecommendedOccupations($_SESSION['user_id']) as $occupation): ?>
                                <a href="occupation_detail.php?id=<?php echo $occupation['id']; ?>" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?php echo $occupation['title']; ?></h6>
                                        <small class="text-muted">Match: <?php echo $occupation['match_score']; ?>%</small>
                                    </div>
                                    <small class="text-muted"><?php echo $occupation['sector']; ?></small>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                Complete the career assessment to get personalized occupation recommendations.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Career Resources -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Career Resources</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <img src="images/resume-guide.jpg" class="card-img-top" alt="Resume Guide">
                                    <div class="card-body">
                                        <h6 class="card-title">Resume Writing Guide</h6>
                                        <p class="card-text small">Learn how to create a professional resume that stands out.</p>
                                        <a href="resource_detail.php?id=1" class="btn btn-sm btn-outline-primary">View Guide</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <img src="images/interview-tips.jpg" class="card-img-top" alt="Interview Tips">
                                    <div class="card-body">
                                        <h6 class="card-title">Interview Preparation</h6>
                                        <p class="card-text small">Master the art of interviewing with our comprehensive guide.</p>
                                        <a href="resource_detail.php?id=2" class="btn btn-sm btn-outline-primary">View Tips</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <img src="images/skills-development.jpg" class="card-img-top" alt="Skills Development">
                                    <div class="card-body">
                                        <h6 class="card-title">Skills Development</h6>
                                        <p class="card-text small">Identify and develop skills needed for your dream career.</p>
                                        <a href="resource_detail.php?id=3" class="btn btn-sm btn-outline-primary">Learn More</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <img src="images/salary-guide.jpg" class="card-img-top" alt="Salary Guide">
                                    <div class="card-body">
                                        <h6 class="card-title">Salary Information</h6>
                                        <p class="card-text small">Understand salary ranges for different occupations in Egypt.</p>
                                        <a href="resource_detail.php?id=4" class="btn btn-sm btn-outline-primary">View Data</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Career Pathways -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Career Pathways</h5>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="careerPathways">
                            <?php foreach(getCareerPathways() as $index => $pathway): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                            data-bs-target="#collapse<?php echo $index; ?>" aria-expanded="false" 
                                            aria-controls="collapse<?php echo $index; ?>">
                                        <?php echo $pathway['title']; ?>
                                    </button>
                                </h2>
                                <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse" 
                                     aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#careerPathways">
                                    <div class="accordion-body">
                                        <p><?php echo $pathway['description']; ?></p>
                                        <h6>Potential Careers:</h6>
                                        <ul>
                                            <?php foreach($pathway['careers'] as $career): ?>
                                            <li><?php echo $career; ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                        <a href="pathway_detail.php?id=<?php echo $pathway['id']; ?>" class="btn btn-sm btn-primary">Explore Pathway</a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sector Information -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Key Sectors in Egypt</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach(getKeySectors() as $sector): ?>
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title"><?php echo $sector['name']; ?></h6>
                                        <p class="card-text small"><?php echo $sector['description']; ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted"><?php echo $sector['job_growth']; ?>% job growth</small>
                                            <a href="sector_detail.php?id=<?php echo $sector['id']; ?>" class="btn btn-sm btn-outline-primary">Explore</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>