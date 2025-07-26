<?php
session_start();
require_once 'auth_check.php';
require_once 'header.php';

if ($_SESSION['user_type'] != 'employer') {
    header("Location: dashboard.php");
    exit();
}

$employer_id = $_SESSION['user_id'];
$employer_profile = getEmployerProfile($employer_id);
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-12">
                <h2 class="mb-0">Post a Job</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="manage_jobs.php">Jobs</a></li>
                        <li class="breadcrumb-item active">Post New Job</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Job Details</h5>
                    </div>
                    <div class="card-body">
                        <form action="process_job.php" method="post" id="jobPostForm">
                            <div class="mb-3">
                                <label for="job_title" class="form-label">Job Title *</label>
                                <input type="text" class="form-control" id="job_title" name="job_title" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="job_description" class="form-label">Job Description *</label>
                                <textarea class="form-control" id="job_description" name="job_description" rows="6" required></textarea>
                                <small class="text-muted">Describe the responsibilities, requirements, and benefits of the position.</small>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="occupation_id" class="form-label">Occupation *</label>
                                    <select class="form-select" id="occupation_id" name="occupation_id" required>
                                        <option value="">Select Occupation</option>
                                        <?php foreach(getOccupations() as $occupation): ?>
                                        <option value="<?php echo $occupation['occupation_id']; ?>">
                                            <?php echo $occupation['title_en']; ?> (ENOC: <?php echo $occupation['enoc_code']; ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="employment_type" class="form-label">Employment Type *</label>
                                    <select class="form-select" id="employment_type" name="employment_type" required>
                                        <option value="full-time">Full-time</option>
                                        <option value="part-time">Part-time</option>
                                        <option value="contract">Contract</option>
                                        <option value="temporary">Temporary</option>
                                        <option value="internship">Internship</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="experience_level" class="form-label">Experience Level *</label>
                                    <select class="form-select" id="experience_level" name="experience_level" required>
                                        <option value="entry">Entry Level</option>
                                        <option value="mid">Mid Level</option>
                                        <option value="senior">Senior Level</option>
                                        <option value="executive">Executive</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="education_required" class="form-label">Minimum Education *</label>
                                    <select class="form-select" id="education_required" name="education_required" required>
                                        <option value="primary">Primary</option>
                                        <option value="preparatory">Preparatory</option>
                                        <option value="secondary">Secondary</option>
                                        <option value="diploma">Diploma</option>
                                        <option value="bachelor" selected>Bachelor's Degree</option>
                                        <option value="master">Master's Degree</option>
                                        <option value="phd">PhD</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="governorate" class="form-label">Governorate *</label>
                                    <select class="form-select" id="governorate" name="governorate" required>
                                        <option value="">Select Governorate</option>
                                        <?php foreach(getGovernorates() as $gov): ?>
                                        <option value="<?php echo $gov; ?>"><?php echo $gov; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="city">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="salary_min" class="form-label">Minimum Salary (EGP)</label>
                                    <input type="number" class="form-control" id="salary_min" name="salary_min" min="0">
                                </div>
                                <div class="col-md-6">
                                    <label for="salary_max" class="form-label">Maximum Salary (EGP)</label>
                                    <input type="number" class="form-control" id="salary_max" name="salary_max" min="0">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Required Skills *</label>
                                <select class="form-select" id="job_skills" name="skills[]" multiple required>
                                    <?php foreach(getAllSkills() as $skill): ?>
                                    <option value="<?php echo $skill['skill_id']; ?>"><?php echo $skill['name_en']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Hold Ctrl/Cmd to select multiple skills</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="application_instructions" class="form-label">Application Instructions</label>
                                <textarea class="form-control" id="application_instructions" name="application_instructions" rows="3"></textarea>
                                <small class="text-muted">Provide specific instructions for applicants if needed</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="closing_date" class="form-label">Closing Date</label>
                                <input type="date" class="form-control" id="closing_date" name="closing_date">
                                <small class="text-muted">Leave blank if the position has no closing date</small>
                            </div>
                            
                            <input type="hidden" name="action" value="post_job">
                            <button type="submit" class="btn btn-primary">Post Job</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Company Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <img src="<?php echo $employer_profile['logo_url'] ?: 'images/default-company.png'; ?>" 
                                 alt="<?php echo $employer_profile['company_name']; ?>" 
                                 class="img-fluid rounded" style="max-height: 100px;">
                        </div>
                        <h5 class="text-center"><?php echo $employer_profile['company_name']; ?></h5>
                        <p class="text-center text-muted"><?php echo $employer_profile['industry']; ?></p>
                        <p class="small"><?php echo nl2br($employer_profile['company_description']); ?></p>
                        <a href="employer_profile.php" class="btn btn-sm btn-outline-primary w-100">Edit Company Profile</a>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Job Posting Tips</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <h6><i class="bi bi-check-circle text-success"></i> Be Specific</h6>
                                <p class="small">Clearly define the job title, responsibilities, and requirements.</p>
                            </li>
                            <li class="mb-3">
                                <h6><i class="bi bi-check-circle text-success"></i> Highlight Benefits</h6>
                                <p class="small">Mention any perks or benefits to attract top talent.</p>
                            </li>
                            <li class="mb-3">
                                <h6><i class="bi bi-check-circle text-success"></i> Use Keywords</h6>
                                <p class="small">Include relevant skills and terms that job seekers might search for.</p>
                            </li>
                            <li>
                                <h6><i class="bi bi-check-circle text-success"></i> Be Transparent</h6>
                                <p class="small">Provide accurate information about salary and location.</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize select2 for skills multiselect
    $('#job_skills').select2({
        placeholder: "Select required skills",
        allowClear: true
    });
    
    // Form validation
    $('#jobPostForm').validate({
        rules: {
            job_title: "required",
            job_description: "required",
            occupation_id: "required",
            employment_type: "required",
            experience_level: "required",
            education_required: "required",
            governorate: "required",
            'skills[]': {
                required: true,
                minlength: 1
            }
        },
        messages: {
            'skills[]': "Please select at least one required skill"
        },
        errorElement: 'div',
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            element.closest('.mb-3').append(error);
        },
        highlight: function(element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    });
    
    // Salary validation
    $('#salary_min, #salary_max').on('change', function() {
        const min = parseFloat($('#salary_min').val());
        const max = parseFloat($('#salary_max').val());
        
        if (min && max && min > max) {
            alert('Minimum salary cannot be greater than maximum salary');
            $(this).val('');
        }
    });
});
</script>

<?php require_once 'footer.php'; ?>