<?php
session_start();
require_once 'auth_check.php';
require_once 'header.php';

if ($_SESSION['user_type'] != 'admin') {
    header("Location: dashboard.php");
    exit();
}
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-12">
                <h2 class="mb-0">Occupation Standards Management</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                        <li class="breadcrumb-item active">Occupation Standards</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Egyptian National Occupational Classification (ENOC)</h5>
                        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addOccupationModal">
                            <i class="bi bi-plus"></i> Add Occupation
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="occupationSearch" placeholder="Search occupations...">
                                    <button class="btn btn-outline-secondary" type="button" id="searchButton">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="sectorFilter">
                                    <option value="">All Sectors</option>
                                    <option value="Agriculture">Agriculture</option>
                                    <option value="Construction">Construction</option>
                                    <option value="Manufacturing">Manufacturing</option>
                                    <option value="Tourism">Tourism</option>
                                    <option value="IT">Information Technology</option>
                                    <!-- More sectors -->
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="iscoFilter">
                                    <option value="">All ISCO Groups</option>
                                    <option value="1">Managers</option>
                                    <option value="2">Professionals</option>
                                    <option value="3">Technicians</option>
                                    <option value="4">Clerical Support</option>
                                    <option value="5">Service/Sales</option>
                                    <!-- More ISCO groups -->
                                </select>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="occupationsTable">
                                <thead>
                                    <tr>
                                        <th>ENOC Code</th>
                                        <th>Occupation Title</th>
                                        <th>ISCO Code</th>
                                        <th>ESCO Code</th>
                                        <th>Sector</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach(getOccupationsPaginated() as $occupation): ?>
                                    <tr>
                                        <td><?php echo $occupation['enoc_code']; ?></td>
                                        <td>
                                            <strong><?php echo $occupation['title_en']; ?></strong><br>
                                            <small class="text-muted"><?php echo $occupation['title_ar']; ?></small>
                                        </td>
                                        <td><?php echo $occupation['isco_code']; ?></td>
                                        <td><?php echo $occupation['esco_code']; ?></td>
                                        <td><?php echo $occupation['sector']; ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    onclick="viewOccupation(<?php echo $occupation['occupation_id']; ?>)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary" 
                                                    onclick="editOccupation(<?php echo $occupation['occupation_id']; ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteOccupation(<?php echo $occupation['occupation_id']; ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Import Occupations</h5>
                    </div>
                    <div class="card-body">
                        <form id="importForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="importFile" class="form-label">Select File</label>
                                <input class="form-control" type="file" id="importFile" name="importFile" accept=".csv, .xlsx">
                                <small class="text-muted">Supported formats: CSV, Excel</small>
                            </div>
                            <div class="mb-3">
                                <label for="importType" class="form-label">Import Type</label>
                                <select class="form-select" id="importType" name="importType">
                                    <option value="update">Update Existing Records</option>
                                    <option value="replace">Replace All Records</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Import</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">ESCO Integration</h5>
                    </div>
                    <div class="card-body">
                        <p>Connect with the European Skills, Competences, Qualifications and Occupations (ESCO) database to align ENOC with international standards.</p>
                        
                        <div class="mb-3">
                            <label for="escoSearch" class="form-label">Search ESCO Occupations</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="escoSearch" placeholder="Search ESCO...">
                                <button class="btn btn-outline-secondary" type="button" id="escoSearchBtn">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                        
                        <button class="btn btn-primary mb-3" id="fetchEscoBtn">
                            <i class="bi bi-cloud-download"></i> Fetch Latest ESCO Data
                        </button>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Last synchronized: <?php echo getLastEscoSync(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Occupation Modal -->
<div class="modal fade" id="addOccupationModal" tabindex="-1" aria-labelledby="addOccupationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="addOccupationForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="addOccupationModalLabel">Add New Occupation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="enocCode" class="form-label">ENOC Code *</label>
                            <input type="text" class="form-control" id="enocCode" name="enocCode" required>
                        </div>
                        <div class="col-md-6">
                            <label for="iscoCode" class="form-label">ISCO Code</label>
                            <input type="text" class="form-control" id="iscoCode" name="iscoCode">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="titleEn" class="form-label">Title (English) *</label>
                            <input type="text" class="form-control" id="titleEn" name="titleEn" required>
                        </div>
                        <div class="col-md-6">
                            <label for="titleAr" class="form-label">Title (Arabic) *</label>
                            <input type="text" class="form-control" id="titleAr" name="titleAr" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descriptionEn" class="form-label">Description (English)</label>
                        <textarea class="form-control" id="descriptionEn" name="descriptionEn" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descriptionAr" class="form-label">Description (Arabic)</label>
                        <textarea class="form-control" id="descriptionAr" name="descriptionAr" rows="3"></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="sector" class="form-label">Sector *</label>
                            <select class="form-select" id="sector" name="sector" required>
                                <option value="">Select Sector</option>
                                <option value="Agriculture">Agriculture</option>
                                <option value="Construction">Construction</option>
                                <option value="Manufacturing">Manufacturing</option>
                                <option value="Tourism">Tourism</option>
                                <option value="IT">Information Technology</option>
                                <!-- More sectors -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="educationLevel" class="form-label">Education Level</label>
                            <select class="form-select" id="educationLevel" name="educationLevel">
                                <option value="">Select Level</option>
                                <option value="primary">Primary</option>
                                <option value="preparatory">Preparatory</option>
                                <option value="secondary">Secondary</option>
                                <option value="diploma">Diploma</option>
                                <option value="bachelor">Bachelor's Degree</option>
                                <option value="master">Master's Degree</option>
                                <option value="phd">PhD</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="escoCode" class="form-label">ESCO URI</label>
                        <input type="text" class="form-control" id="escoCode" name="escoCode" placeholder="http://data.europa.eu/esco/occupation/...">
                        <small class="text-muted">Paste the full ESCO URI for this occupation</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Associated Skills</label>
                        <select class="form-select" id="occupationSkills" name="skills[]" multiple>
                            <?php foreach(getAllSkills() as $skill): ?>
                            <option value="<?php echo $skill['skill_id']; ?>"><?php echo $skill['name_en']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple skills</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Occupation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Occupation Modal -->
<div class="modal fade" id="viewOccupationModal" tabindex="-1" aria-labelledby="viewOccupationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewOccupationModalLabel">Occupation Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="occupationDetails">
                <!-- Content loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize select2 for skills multiselect
    $('#occupationSkills').select2({
        placeholder: "Select associated skills",
        allowClear: true
    });
    
    // Form validation
    $('#addOccupationForm').validate({
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
    
    // Search functionality
    $('#searchButton').click(function() {
        searchOccupations();
    });
    
    $('#occupationSearch').keypress(function(e) {
        if (e.which == 13) {
            searchOccupations();
        }
    });
    
    $('#sectorFilter, #iscoFilter').change(function() {
        searchOccupations();
    });
});

function searchOccupations() {
    const searchTerm = $('#occupationSearch').val();
    const sector = $('#sectorFilter').val();
    const isco = $('#iscoFilter').val();
    
    $.ajax({
        url: 'api/search_occupations.php',
        method: 'GET',
        data: {
            search: searchTerm,
            sector: sector,
            isco: isco
        },
        success: function(data) {
            $('#occupationsTable tbody').html(data);
        }
    });
}

function viewOccupation(occupationId) {
    $.ajax({
        url: 'api/get_occupation.php',
        method: 'GET',
        data: { id: occupationId },
        success: function(data) {
            $('#occupationDetails').html(data);
            $('#viewOccupationModal').modal('show');
        }
    });
}

function editOccupation(occupationId) {
    // Similar to view but with editable fields and save button
    alert('Edit occupation with ID: ' + occupationId);
}

function deleteOccupation(occupationId) {
    if (confirm('Are you sure you want to delete this occupation? This action cannot be undone.')) {
        $.ajax({
            url: 'api/delete_occupation.php',
            method: 'POST',
            data: { id: occupationId },
            success: function(response) {
                if (response.success) {
                    alert('Occupation deleted successfully');
                    searchOccupations(); // Refresh the table
                } else {
                    alert('Error deleting occupation: ' + response.message);
                }
            }
        });
    }
}
</script>

<?php require_once 'footer.php'; ?>