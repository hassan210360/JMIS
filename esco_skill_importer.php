<?php
require_once 'db_connection.php';
require_once 'api_fetch_skills.php'; // must include the fetch_esco_skills() function

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['esco_code'], $_POST['occupation_id'])) {
    $esco_code = $_POST['esco_code'];
    $occupation_id = intval($_POST['occupation_id']);
    $skills = fetch_esco_skills($esco_code);

    if ($skills) {
        foreach ($skills as $s) {
            $name = $conn->real_escape_string($s['label']);
            $code = $conn->real_escape_string($s['uri']);
            $conn->query("INSERT IGNORE INTO lmis3_skills_table (skill_name, esco_code) VALUES ('$name', '$code')");
            $skill_id = $conn->insert_id ?: $conn->query("SELECT skill_id FROM lmis3_skills_table WHERE esco_code = '$code'")->fetch_assoc()['skill_id'];
            $conn->query("INSERT IGNORE INTO lmis3_occupation_skills_table (occupation_id, skill_id) VALUES ($occupation_id, $skill_id)");
        }
        echo "<div class='alert alert-success'>Imported " . count($skills) . " skills for ESCO: $esco_code</div>";
    } else {
        echo "<div class='alert alert-danger'>Failed to fetch skills.</div>";
    }
}
?>
<form method='POST' class='container mt-5'>
  <h4>Import ESCO Skills</h4>
  <input class='form-control' name='esco_code' placeholder='ESCO Code' required>
  <input class='form-control mt-2' name='occupation_id' type='number' placeholder='Occupation ID' required>
  <button class='btn btn-primary mt-2'>Import</button>
</form>
