<?php
// TEMP: Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db_connection.php'; // ✅ Ensure $pdo is set

/**
 * Check if a user has a specific permission based on role.
 *
 * @param PDO $pdo
 * @param int $user_id
 * @param string $permission_name
 * @return bool
 */
function hasPermission(PDO $pdo, int $user_id, string $permission_name): bool {
    // ✅ Fixed: Use correct column name (user_id)
    $stmt = $pdo->prepare("SELECT role_id FROM lmis3_users_table WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $role_id = $stmt->fetchColumn();

    if (!$role_id) {
        return false; // No role assigned
    }

    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM lmis3_role_permissions_table rp
        JOIN lmis3_permissions_table p ON rp.permission_id = p.permission_id
        WHERE rp.role_id = ? AND p.permission_name = ?
    ");
    $stmt->execute([$role_id, $permission_name]);

    return $stmt->fetchColumn() > 0;
}
