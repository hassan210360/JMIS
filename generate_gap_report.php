<?php
require_once 'db_connection.php';
require('fpdf/fpdf.php'); // Make sure FPDF is installed

$user_id = $_GET['user_id'] ?? 1;
$occupation_id = $_GET['occupation_id'] ?? 1;

$occupation = $conn->query("SELECT * FROM lmis3_occupations_table WHERE occupation_id = $occupation_id")->fetch_assoc();
$user = $conn->query("SELECT * FROM lmis3_users_table WHERE user_id = $user_id")->fetch_assoc();

$rs = $conn->query("SELECT s.skill_name FROM lmis3_skills_table s 
    JOIN lmis3_occupation_skills_table os ON s.skill_id = os.skill_id 
    WHERE os.occupation_id = $occupation_id");
$required_skills = array_column($rs->fetch_all(MYSQLI_ASSOC), 'skill_name');

$us = $conn->query("SELECT s.skill_name FROM lmis3_skills_table s 
    JOIN lmis3_user_skills_table us ON s.skill_id = us.skill_id 
    WHERE us.user_id = $user_id");
$user_skills = array_column($us->fetch_all(MYSQLI_ASSOC), 'skill_name');

$missing = array_diff($required_skills, $user_skills);
$match_count = count(array_intersect($required_skills, $user_skills));
$match_percent = count($required_skills) > 0 ? round(($match_count / count($required_skills)) * 100) : 0;

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Skill Gap Report',0,1,'C');
$pdf->Ln(10);
$pdf->SetFont('Arial','',12);
$pdf->MultiCell(0,8,"User: {$user['first_name']} {$user['last_name']}\nOccupation: {$occupation['occupation_name']}\nMatch: {$match_percent}%\n\nMissing Skills:");
foreach ($missing as $skill) {
    $pdf->Cell(0,8,"- $skill",0,1);
}
$pdf->Output("I", "gap_report_user{$user_id}.pdf");
?>
