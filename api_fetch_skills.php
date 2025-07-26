<?php
function fetch_esco_skills($esco_code) {
    $url = "https://ec.europa.eu/esco/api/resource/occupation/" . urlencode($esco_code);
    $opts = [
        "http" => [
            "method" => "GET",
            "header" => "Accept: application/json\\r\\n"
        ]
    ];
    $context = stream_context_create($opts);
    $response = @file_get_contents($url, false, $context);

    if ($response === FALSE) {
        return null;
    }

    $data = json_decode($response, true);
    $skills = [];

    if (!empty($data['_links']['hasEssentialSkill'])) {
        foreach ($data['_links']['hasEssentialSkill'] as $skill) {
            $skills[] = [
                'label' => $skill['title'] ?? 'N/A',
                'uri' => $skill['href']
            ];
        }
    }

    return $skills;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['esco_code'])) {
    $esco_code = $_GET['esco_code'];
    $skills = fetch_esco_skills($esco_code);
    header('Content-Type: application/json');
    echo json_encode(['skills' => $skills]);
    exit;
}
?>
