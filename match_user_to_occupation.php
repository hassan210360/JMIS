<?php
require_once 'db_connection.php';

$user_id = $_GET['user_id'] ?? 1;
$occupation_id = $_GET['occupation_id'] ?? 1;

$match_percent = 0;
$missing_skills = [];

// Required Skills
$stmt = $conn->prepare("SELECT s.skill_id, s.skill_name FROM lmis3_skills_table s
    JOIN lmis3_occupation_skills_table os ON s.skill_id = os.skill_id
    WHERE os.occupation_id = ?");
$stmt->bind_param("i", $occupation_id);
$stmt->execute();
$required_skills = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// User Skills
$stmt = $conn->prepare("SELECT s.skill_id, s.skill_name FROM lmis3_skills_table s
    JOIN lmis3_user_skills_table us ON s.skill_id = us.skill_id
    WHERE us.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_skills = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$required_ids = array_column($required_skills, 'skill_id');
$user_ids = array_column($user_skills, 'skill_id');

$match_count = count(array_intersect($required_ids, $user_ids));
$total_required = count($required_ids);
$match_percent = $total_required > 0 ? round(($match_count / $total_required) * 100) : 0;

foreach ($required_skills as $skill) {
    if (!in_array($skill['skill_id'], $user_ids)) {
        $missing_skills[] = $skill['skill_name'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Skill Match Result</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container py-5">
  <h3>Skill Match Result</h3>
  <p><strong>Match Percentage:</strong> <?= $match_percent ?>%</p>
  <?php if (!empty($missing_skills)): ?>
  <h5>Missing Skills</h5>
  <ul class="list-group">
    <?php foreach ($missing_skills as $ms): ?>
      <li class="list-group-item"><?= htmlspecialchars($ms) ?></li>
    <?php endforeach; ?>
  </ul>
  <?php else: ?>
    <div class="alert alert-success">No skill gaps found!</div>
  <?php endif; ?>
</body>
</html>
