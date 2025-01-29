<?php
// Allow cross-origin requests from your Vue app
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

$host = 'localhost';
$dbname = 'job_portal';
$username = 'root';
$password = '';
$pdo = new mysqli($host, $username, $password, $dbname);

// Check for connection error
if ($pdo->connect_error) {
    die("Connection failed: " . $pdo->connect_error);
}

// Fetch the QCM questions for the selected offer
if (isset($_GET['id'])) {
    $offre_id = $_GET['id'];

    // Prepare the query
    $query = "SELECT qcm.id, qcm.question, qcm.reponseCorrecte, qcm_options.option_text
              FROM qcm
              JOIN qcm_options ON qcm.id = qcm_options.qcm_id
              WHERE qcm.offre_id = ?";
    
    $stmt = $pdo->prepare($query);
    
    // Bind the parameter
    $stmt->bind_param("i", $offre_id);
    
    // Execute the query
    $stmt->execute();
    
    // Get the result
    $result = $stmt->get_result();

    $qcm = [];
    while ($row = $result->fetch_assoc()) {
        // Group options for the current question
        $qcm[$row['id']]['question'] = $row['question'];
        $qcm[$row['id']]['reponseCorrecte'] = $row['reponseCorrecte'];
        $qcm[$row['id']]['options'][] = $row['option_text'];
    }

    // Return the structured QCM data as a JSON response
    echo json_encode(['qcm' => array_values($qcm)]);
} else {
    echo json_encode(['error' => 'No offer ID provided']);
}
?>
