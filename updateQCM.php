<?php
// Allow pre-flight OPTIONS request (for CORS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Allow-Credentials: true');
    http_response_code(200);
    exit();
}

// Handle POST request after OPTIONS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

ini_set('display_errors', 1);  // Ensure errors are displayed in the browser
error_reporting(E_ALL);  // Report all errors

// Enable error logging to a file for further debugging
ini_set('log_errors', 1);
ini_set('error_log', 'F:\\laragon\\www\\offre\\errr.txt');

// Database connection
$conn = new mysqli("localhost", "root", "", "job_portal");

// Check connection
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    http_response_code(500);
    echo json_encode(["message" => "Database connection failed"]);
    exit();
}

// Check if input is empty
$rawInput = file_get_contents("php://input");

if (empty($rawInput)) {
    error_log("Empty JSON payload");
    http_response_code(400);
    echo json_encode(["message" => "Empty JSON payload"]);
    exit();
}

// Decode JSON
$data = json_decode($rawInput, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("Invalid JSON input: " . json_last_error_msg());
    http_response_code(400);
    echo json_encode(["message" => "Invalid JSON input"]);
    exit();
}

// Validate required fields
if (!isset($data['offerId'], $data['qcmData'])) {
    error_log("Missing required fields");
    http_response_code(400);
    echo json_encode(["message" => "Missing required fields"]);
    exit();
}

// Extract data
$offerId = $data['offerId'];
$qcmData = $data['qcmData'];

// Begin transaction
$conn->begin_transaction();

try {
    // Delete existing QCM for the offer
    // First, delete the related options from qcm_options
$stmt = $conn->prepare("DELETE FROM qcm_options WHERE qcm_id = ?");
$stmt->bind_param("i", $offerId);
if (!$stmt->execute()) {
    error_log("SQL Error (Delete QCM Options): " . $stmt->error);
    throw new Exception("Error deleting related QCM options");
}
$stmt->close();

// Now, delete the QCM from qcm table
$stmt = $conn->prepare("DELETE FROM qcm WHERE offre_id = ?");
$stmt->bind_param("i", $offerId);
if (!$stmt->execute()) {
    error_log("SQL Error (Delete QCM): " . $stmt->error);
    throw new Exception("Error deleting QCM");
}
$stmt->close();


    // Insert new QCM data
    foreach ($qcmData as $qcm) {
        if (!isset($qcm['question'], $qcm['reponseCorrecte'], $qcm['options'])) {
            error_log("Missing QCM data fields");
            http_response_code(400);
            echo json_encode(["message" => "Missing QCM data fields"]);
            exit();
        }

        // Insert the QCM question
        $stmt = $conn->prepare("INSERT INTO qcm (offre_id, question, reponseCorrecte) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $offerId, $qcm['question'], $qcm['reponseCorrecte']);
        if (!$stmt->execute()) {
            error_log("SQL Error (QCM Insert): " . $stmt->error);
            throw new Exception("Error inserting QCM question");
        }
        $qcm_id = $stmt->insert_id;  // Get the ID of the inserted question
        $stmt->close();

        // Ensure there are exactly 4 options for each question
        if (count($qcm['options']) != 4) {
            error_log("Invalid number of options for question ID: " . $qcm_id);
            http_response_code(400);
            echo json_encode(["message" => "Each question must have exactly 4 options"]);
            exit();
        }

        // Log the options data for debugging purposes
        error_log("QCM Options Data: " . json_encode($qcm['options'], JSON_PRETTY_PRINT));

        // Insert the options for this question (only qcm_id and option_text)
        foreach ($qcm['options'] as $option) {
            // Check if the option_text is present and not empty
            if (isset($option['option_text']) && !empty($option['option_text'])) {
                // Prepare SQL statement for inserting options
                $sql = "INSERT INTO qcm_options (qcm_id, option_text) VALUES (?, ?)";
                error_log("Executing SQL: " . $sql);  // Log the SQL query being executed

                // Prepare and execute the statement
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    // Bind parameters (qcm_id and option_text only)
                    $stmt->bind_param("is", $qcm_id, $option['option_text']);

                    // Execute the query
                    if (!$stmt->execute()) {
                        error_log("SQL Error (Options Insert): " . $stmt->error);  // Log error if insertion fails
                        throw new Exception("Error inserting QCM options");
                    }
                    $stmt->close();
                } else {
                    error_log("SQL Error (Prepare Statement): " . $conn->error);  // Log error if prepare fails
                }
            } else {
                error_log("Option text is empty or not set: " . json_encode($option));  // Debugging log
            }
        }

        // After adding options, ensure the correct answer is recorded in the `qcm` table
        if (!empty($qcm['reponseCorrecte'])) {
            $stmt = $conn->prepare("UPDATE qcm SET reponseCorrecte = ? WHERE id = ?");
            $stmt->bind_param("si", $qcm['reponseCorrecte'], $qcm_id);
            if (!$stmt->execute()) {
                error_log("SQL Error (Update Correct Answer): " . $stmt->error);
                throw new Exception("Error updating correct answer in QCM");
            }
            $stmt->close();
        }
    }

    // Commit transaction
    $conn->commit();
    echo json_encode(["message" => "QCM updated successfully"]);
} catch (Exception $e) {
    $conn->rollback();
    error_log("Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["message" => "Error updating QCM", "error" => $e->getMessage()]);
}

// Close connection
$conn->close();
?>
