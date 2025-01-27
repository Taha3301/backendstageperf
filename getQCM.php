<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "job_portal";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

$conn->set_charset("utf8");

// Get offer_id from query parameter
$offer_id = isset($_GET['offer_id']) ? (int)$_GET['offer_id'] : null;

if ($offer_id === null) {
    http_response_code(400);
    echo json_encode(["message" => "Offer ID is required"]);
    exit();
}

// Fetch the QCM data for the offer
$sql = "SELECT qcm.id, qcm.question, qcm.reponseCorrecte, qo.id AS option_id, qo.option_text
        FROM qcm
        LEFT JOIN qcm_options qo ON qcm.id = qo.qcm_id
        WHERE qcm.offre_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $offer_id);
$stmt->execute();
$result = $stmt->get_result();

$qcmData = [];
while ($row = $result->fetch_assoc()) {
    $qcmData[$row['id']]['id'] = $row['id'];
    $qcmData[$row['id']]['question'] = $row['question'];
    $qcmData[$row['id']]['reponseCorrecte'] = $row['reponseCorrecte'];
    $qcmData[$row['id']]['options'][] = [
        'option_id' => $row['option_id'],
        'option_text' => $row['option_text']
    ];
}

$stmt->close();
$conn->close();

// Return the QCM data
echo json_encode(array_values($qcmData));
?>
