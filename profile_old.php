<?php
session_start();
require_once 'auth_check.php';
require_once 'header.php';

$user_id = $_SESSION['user_id'];
$profile = getUserProfile($user_id);
$skills = getUserSkills($user_id);
$education = getUserEducation($user_id);
$experience = getUserExperience($user_id);
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-12">
                <h2 class="mb-0">My Profile</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                        <li class="breadcrumb-item active">Profile</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <!-- Profile Card -->
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <img src="<?php echo getProfileImage($user_id); ?>" alt="Profile" class="rounded-circle mb-3" width="150" height="150">
                        <h4><?php echo $profile['first_name'] . ' ' . $profile['last_name']; ?></h4>
                        <p class="text-muted"><?php echo $profile['headline'] ?: 'Jobseeker'; ?></p>
                        
                        <div class="d-flex justify-content-center mb-3">
                            <div class="me-3">
                                <h6 class="mb-0"><?php echo count($skills); ?></h6>
                                <small class="text-muted">Skills</small>
                            </div>
                            <div class="me-3">
                                <h6 class="mb-0"><?php echo getApplicationCount($user_id); ?></h6>
                                <small class="text-muted">Applications</small>
                            </div>
                            <div>
                                <h6 class="mb-0"><?php echo count($experience); ?></h6>
                                <small class="text-muted">Experiences</small>
                            </div>
                        </div>
                        
                        <div class="progress mb-3" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: <?php echo getProfileCompletion($user_id); ?>%"></div>
                        </div>
                        <small class="text-muted">Profile Completion: <?php echo getProfileCompletion($user_id); ?>%</small>
                    </div>
                </div>
                
                <!-- Contact Information -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Contact Information</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="bi bi-envelope me-2"></i> <?php echo $profile['email']; ?></li>
                            <li class="mb-2"><i class="bi bi-telephone me-2"></i> <?php echo $profile['phone'] ?: 'Not provided'; ?></li>
                            <li class="mb-2"><i class="bi bi-geo-alt me-2"></i> <?php echo $profile['governorate'] ?: 'Not provided'; ?></li>
                            <li><i class="bi bi-calendar me-2"></i> Member since <?php echo date('M Y', strtotime($profile['registration_date'])); ?></li>
                        </ul>
                        <button class="btn btn-sm btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#editContactModal">
                            Edit Contact Info
                        </button>
                    </div>
                </div>
                
                <!-- Skills -->
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Skills</h5>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addSkillModal">
                            <i class="bi bi-plus"></i> Add
                        </button>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($skills)): ?>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach($skills as $skill): ?>
                                <span class="badge bg-primary">
                                    <?php echo $skill['name']; ?>
                                    <button class="btn-close btn-close-white btn-sm ms-1" onclick="removeSkill(<?php echo $skill['id']; ?>)"></button>
                                </span>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                No skills added yet. Add skills to improve your job matches.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <!-- About Section -->
                <div class="card mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">About</h5>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editAboutModal">
                            Edit
                        </button>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($profile['summary'])): ?>
                            <p><?php echo nl2br($profile['summary']); ?></p>
                        <?php else: ?>
                            <div class="alert alert-info">
                                No summary provided. Add a summary to tell employers about yourself.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Experience -->
                <div class="card mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Work Experience</h5>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addExperienceModal">
                            <i class="bi bi-plus"></i> Add
                        </button>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($experience)): ?>
                            <div class="list-group">
                                <?php foreach($experience as $exp): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?php echo $exp['job_title']; ?></h6>
                                        <div>
                                            <button class="btn btn-sm btn-outline-secondary" 
                                                    onclick="editExperience(<?php echo $exp['id']; ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteExperience(<?php echo $exp['id']; ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <p class="mb-1"><?php echo $exp['company']; ?></p>
                                    <small class="text-muted">
                                        <?php echo date('M Y', strtotime($exp['start_date'])); ?> - 
                                        <?php echo $exp['current'] ? 'Present' : date('M Y', strtotime($exp['end_date'])); ?>
                                    </small>
                                    <?php if (!empty($exp['description'])): ?>
                                    <p class="mt-2 mb-0"><?php echo nl2br($exp['description']); ?></p>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                No work experience added yet. Add your work history to strengthen your profile.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Education -->
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Education</h5>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addEducationModal">
                            <i class="bi bi-plus"></i> Add
                        </button>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($education)): ?>
                            <div class="list-group">
                                <?php foreach($education as $edu): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?php echo $edu['degree']; ?></h6>
                                        <div>
                                            <button class="btn btn-sm btn-outline-secondary" 
                                                    onclick="editEducation(<?php echo $edu['id']; ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteEducation(<?php echo $edu['id']; ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <p class="mb-1"><?php echo $edu['institution']; ?></p>
                                    <small class="text-muted">
                                        <?php echo date('M Y', strtotime($edu['start_date'])); ?> - 
                                        <?php echo $edu['current'] ? 'Present' : date('M Y', strtotime($edu['end_date'])); ?>
                                    </small>
                                    <?php if (!empty($edu['description'])): ?>
                                    <p class="mt-2 mb-0"><?php echo nl2br($edu['description']); ?></p>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                No education history added yet. Add your education to strengthen your profile.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Contact Modal -->
<div class="modal fade" id="editContactModal" tabindex="-1" aria-labelledby="editContactModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="update_profile.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="editContactModalLabel">Edit Contact Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo $profile['phone']; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="governorate" class="form-label">Governorate</label>
                        <select class="form-select" id="governorate" name="governorate">
                            <option value="">Select Governorate</option>
                            <?php foreach(getGovernorates() as $gov): ?>
                            <option value="<?php echo $gov; ?>" <?php echo $profile['governorate'] == $gov ? 'selected' : ''; ?>>
                                <?php echo $gov; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"><?php echo $profile['address']; ?></textarea>
                    </div>
                    <input type="hidden" name="action" value="update_contact">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Skill Modal -->
<div class="modal fade" id="addSkillModal" tabindex="-1" aria-labelledby="addSkillModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="update_profile.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSkillModalLabel">Add Skill</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="skill" class="form-label">Select Skill</label>
                        <select class="form-select" id="skill" name="skill_id" required>
                            <option value="">Select a skill</option>
                            <?php foreach(getAllSkills() as $skill): ?>
                            <option value="<?php echo $skill['id']; ?>"><?php echo $skill['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="skill_level" class="form-label">Skill Level</label>
                        <select class="form-select" id="skill_level" name="skill_level" required>
                            <option value="basic">Basic</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                            <option value="expert">Expert</option>
                        </select>
                    </div>
                    <input type="hidden" name="action" value="add_skill">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Skill</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Experience Modal -->
<div class="modal fade" id="addExperienceModal" tabindex="-1" aria-labelledby="addExperienceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="update_profile.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="addExperienceModalLabel">Add Work Experience</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="job_title" class="form-label">Job Title</label>
                        <input type="text" class="form-control" id="job_title" name="job_title" required>
                    </div>
                    <div class="mb-3">
                        <label for="company" class="form-label">Company</label>
                        <input type="text" class="form-control" id="company" name="company" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date">
                        </div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="current" name="current">
                        <label class="form-check-label" for="current">I currently work here</label>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <input type="hidden" name="action" value="add_experience">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Experience</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Education Modal -->
<div class="modal fade" id="addEducationModal" tabindex="-1" aria-labelledby="addEducationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="update_profile.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEducationModalLabel">Add Education</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="degree" class="form-label">Degree</label>
                        <input type="text" class="form-control" id="degree" name="degree" required>
                    </div>
                    <div class="mb-3">
                        <label for="institution" class="form-label">Institution</label>
                        <input type="text" class="form-control" id="institution" name="institution" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edu_start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="edu_start_date" name="start_date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edu_end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="edu_end_date" name="end_date">
                        </div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="edu_current" name="current">
                        <label class="form-check-label" for="edu_current">I currently study here</label>
                    </div>
                    <div class="mb-3">
                        <label for="edu_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edu_description" name="description" rows="3"></textarea>
                    </div>
                    <input type="hidden" name="action" value="add_education">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Education</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit About Modal -->
<div class="modal fade" id="editAboutModal" tabindex="-1" aria-labelledby="editAboutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="update_profile.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAboutModalLabel">Edit About Section</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="headline" class="form-label">Professional Headline</label>
                        <input type="text" class="form-control" id="headline" name="headline" 
                               value="<?php echo $profile['headline']; ?>" placeholder="e.g. Web Developer">
                    </div>
                    <div class="mb-3">
                        <label for="summary" class="form-label">Professional Summary</label>
                        <textarea class="form-control" id="summary" name="summary" rows="6"><?php echo $profile['summary']; ?></textarea>
                        <small class="text-muted">Describe your professional background, skills, and career objectives.</small>
                    </div>
                    <input type="hidden" name="action" value="update_about">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function removeSkill(skillId) {
    if (confirm('Are you sure you want to remove this skill?')) {
        fetch('update_profile.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=remove_skill&skill_id=${skillId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error removing skill');
            }
        });
    }
}

function editExperience(expId) {
    // Fetch experience data and populate a modal (similar to add but with existing data)
    // Then show the modal
    alert('Edit experience with ID: ' + expId);
}

function deleteExperience(expId) {
    if (confirm('Are you sure you want to delete this experience?')) {
        fetch('update_profile.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete_experience&exp_id=${expId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting experience');
            }
        });
    }
}

function editEducation(eduId) {
    // Fetch education data and populate a modal
    alert('Edit education with ID: ' + eduId);
}

function deleteEducation(eduId) {
    if (confirm('Are you sure you want to delete this education entry?')) {
        fetch('update_profile.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete_education&edu_id=${eduId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting education');
            }
        });
    }
}
</script>

<?php require_once 'footer.php'; ?>