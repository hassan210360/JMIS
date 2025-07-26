<?php
$host = 'localhost';
$db = 'moodledatabase';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// Connect to MySQL
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$logFile = fopen("import_log.txt", "w");

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    if (($handle = fopen("fake_jobs_data.csv", "r")) !== FALSE) {
        $header = fgetcsv($handle); // Skip header row
        $imported = 0;
        $skipped = 0;

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $title = $data[0];
            $posted_by_user_id = $data[9];
            $employer_id = $data[10];

            // Check if user and employer exist
            $checkUser = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id = ?");
            $checkUser->execute([$posted_by_user_id]);
            $userExists = $checkUser->fetchColumn();

            $checkEmployer = $pdo->prepare("SELECT COUNT(*) FROM employers WHERE employer_id = ?");
            $checkEmployer->execute([$employer_id]);
            $employerExists = $checkEmployer->fetchColumn();

            if (!$userExists || !$employerExists) {
                fwrite($logFile, "Skipped: Missing user/employer for job '{$title}'\n");
                $skipped++;
                continue;
            }

            // Prevent duplicates (based on title + posted_by_user_id)
            $checkDuplicate = $pdo->prepare("SELECT COUNT(*) FROM lmis3_jobs WHERE title = ? AND posted_by_user_id = ?");
            $checkDuplicate->execute([$title, $posted_by_user_id]);
            $isDuplicate = $checkDuplicate->fetchColumn();

            if ($isDuplicate) {
                fwrite($logFile, "Skipped: Duplicate job '{$title}'\n");
                $skipped++;
                continue;
            }

            // Insert new job
            $stmt = $pdo->prepare("INSERT INTO lmis3_jobs (
                title, description, requirements, salary, currency, location_country,
                location_city, job_type, tor_file, posted_by_user_id, employer_id,
                expiry_date, status, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([
                $data[0], $data[1], $data[2], $data[3], $data[4],
                $data[5], $data[6], $data[7], $data[8], $data[9],
                $data[10], $data[11], $data[12], $data[13], $data[14]
            ]);

            $imported++;
        }

        fclose($handle);
        fwrite($logFile, "Import complete. Imported: {$imported}, Skipped: {$skipped}\n");
        echo "Import complete. Imported: {$imported}, Skipped: {$skipped}. Check 'import_log.txt' for details.";
    } else {
        echo "Failed to open the CSV file.";
    }

} catch (PDOException $e) {
    fwrite($logFile, "Database error: " . $e->getMessage() . "\n");
    echo "Database error: " . $e->getMessage();
} finally {
    fclose($logFile);
}
?>
