SELECT sa.*, u.first_name, u.last_name
FROM lmis3_skills_assessments sa
JOIN lmis3_users_table u ON sa.user_id = u.user_id
ORDER BY sa.created_at DESC;
