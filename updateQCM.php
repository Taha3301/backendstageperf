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

// Get the JSON data from the request
$data = json_decode(file_get_contents("php://input"), true);

// Validate input
if (empty($data['offerId']) || empty($data['qcmData'])) {
    http_response_code(400);
    echo json_encode(["message" => "Invalid input data"]);
    exit();
}

$offerId = $data['offerId'];
$qcmData = $data['qcmData'];

// Start a transaction to update QCM data
$conn->begin_transaction();

try {
    // Update each question
    foreach ($qcmData as $qcm) {
        $stmt = $conn->prepare("UPDATE qcm SET question = ?, reponseCorrecte = ? WHERE id = ?");
        $stmt->bind_param("ssi", $qcm['question'], $qcm['reponseCorrecte'], $qcm['id']);
        $stmt->execute();
        $stmt->close();

        // Update options for each question
        foreach ($qcm['options'] as $option) {
            $stmt = $conn->prepare("UPDATE qcm_options SET option_text = ? WHERE id = ?");
            $stmt->bind_param("si", $option['option_text'], $option['option_id']);
            $stmt->execute();
            $stmt->close();
        }
    }

    $conn->commit();
    echo json_encode(["message" => "QCM updated successfully"]);
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(["message" => "Error updating QCM: " . $e->getMessage()]);
}

$conn->close();
?>
