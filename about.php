<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>About  JMIS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<?php include 'header.php'; ?>

<div class="container my-5">
    <h1 class="mb-4">Job Management Information Systm JMIS</h1>

    <p>
        Job Management Information Systm  JMIS is an essential tools used by all, researchers, governments, policymakers, 
        businesses, and jobseekers to understand the dynamics of the job market. LMIS collects, analyzes, 
        and disseminates labor market data, including employment trends, skills demands, wages, and 
        unemployment rates.
    </p>

    <p>
        A well-functioning JMIS helps in improving job matching, guiding education and training programs, 
        and supporting economic development strategies. It bridges the information gap between employers, 
        jobseekers, and training institutions.
    </p>

    <h2 class="mt-5">Occupational Classification Systems: ISCO & ESCO</h2>

    <p>
        International Standard Classification of Occupations (ISCO) and European Skills, Competences, 
        Qualifications and Occupations (ESCO) are frameworks used to classify jobs and skills globally.
    </p>

    <ul>
        <li><strong>ISCO:</strong> Developed by the International Labour Organization (ILO), ISCO groups jobs into hierarchical codes representing occupations worldwide.</li>
        <li><strong>ESCO:</strong> Developed by the European Commission, ESCO links occupations with the skills and qualifications required, helping to improve job matching and skills forecasting.</li>
    </ul>

    <h3 class="mt-4">Example: Using ISCO and ESCO codes in LMIS</h3>
    <pre><code class="language-php">
// Example array for an occupation with ISCO and ESCO codes
$occupation = [
    'name' => 'Software Developer',
    'isco_code' => '2512',  // ISCO-08 code for Software Developers
    'esco_code' => 'b2651f57-5d70-4d18-8d51-cf415c4121db', // ESCO occupation URI (UUID)
    'skills' => [
        'programming' => ['php', 'javascript', 'python'],
        'soft_skills' => ['problem solving', 'teamwork', 'communication']
    ]
];

// Display occupation info
echo "Occupation: " . $occupation['name'] . "\n";
echo "ISCO Code: " . $occupation['isco_code'] . "\n";
echo "ESCO Code: " . $occupation['esco_code'] . "\n";
echo "Skills: " . implode(", ", $occupation['skills']['programming']) . "\n";
    </code></pre>

    <p>
        Integrating ISCO and ESCO coding in LMIS platforms enables standardization across data sources and supports international labor market comparisons.
    </p>

    <p>
        This makes it easier for jobseekers to find relevant opportunities based on their skills and for employers to identify candidates with the right qualifications.
    </p>

</div>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
