<?php
function calculateProfileStrength($user_id) {
    global $conn;
    
    $total_fields = 10; // Total fields we're checking
    $completed_fields = 0;
    
    // Get user profile
    $stmt = $conn->prepare("SELECT * FROM lmis3_user_profiles_table WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $profile = $stmt->get_result()->fetch_assoc();
    
    // Check each important field
    if (!empty($profile['first_name'])) $completed_fields++;
    if (!empty($profile['last_name'])) $completed_fields++;
    if (!empty($profile['phone'])) $completed_fields++;
    if (!empty($profile['governorate'])) $completed_fields++;
    if (!empty($profile['address'])) $completed_fields++;
    
    // Check skills
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM lmis3_jobseeker_skills_table WHERE jobseeker_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $skills = $stmt->get_result()->fetch_assoc();
    if ($skills['count'] > 0) $completed_fields++;
    
    // Check education
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM lmis3_education_table WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $education = $stmt->get_result()->fetch_assoc();
    if ($education['count'] > 0) $completed_fields++;
    
    // Check experience
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM lmis3_experience_table WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $experience = $stmt->get_result()->fetch_assoc();
    if ($experience['count'] > 0) $completed_fields++;
    
    // Check summary
    $stmt = $conn->prepare("SELECT summary FROM lmis3_user_profiles_table WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $summary = $stmt->get_result()->fetch_assoc();
    if (!empty($summary['summary'])) $completed_fields++;
    
    return round(($completed_fields / $total_fields) * 100);
}

function getRecentActivity($user_id) {
    global $conn;
    
    $activities = [];
    
    // Get recent applications
    $stmt = $conn->prepare("
        SELECT a.application_date as time, j.title, 
               CONCAT('Applied for ', j.title) as message,
               'file-earmark-text' as icon
        FROM lmis3_applications_table a
        JOIN lmis3_jobs_table j ON a.job_id = j.job_id
        WHERE a.jobseeker_id = ?
        ORDER BY a.application_date DESC
        LIMIT 5
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }
    
    // Get profile updates (you would need to log these separately)
    // This is just a placeholder - implement proper activity logging
    
    return $activities;
}