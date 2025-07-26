// Function to fetch ESCO occupation data
function fetchEscoOccupation($escoCode) {
    $url = "https://ec.europa.eu/esco/api/resource/occupation?uri=http://data.europa.eu/esco/occupation/" . $escoCode;
    $response = file_get_contents($url);
    return json_decode($response, true);
}

// Function to map ENOC to ESCO/ISCO
function mapToInternationalStandards($enocCode) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM lmis3_occupations_table WHERE enoc_code = ?");
    $stmt->bind_param("s", $enocCode);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $occupation = $result->fetch_assoc();
        $escoData = [];
        
        if (!empty($occupation['esco_code'])) {
            $escoData = fetchEscoOccupation($occupation['esco_code']);
        }
        
        return [
            'enoc' => $occupation,
            'esco' => $escoData,
            'isco' => $occupation['isco_code'] // Would need similar fetch function for ISCO
        ];
    }
    
    return null;
}