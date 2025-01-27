<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'F:\laragon\www\offre\errr.txt');  // Provide the full path to your error log file

// Assuming you have a connection to the database
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
    die(json_encode(['success' => false, 'message' => "Connection failed: " . $pdo->connect_error]));
}

// Receive the POST data
$data = json_decode(file_get_contents('php://input'), true);

// Log the received data for debugging
error_log(print_r($data, true)); // This will log the received data to your PHP error log

if (isset($data['user_id'], $data['offre_id'], $data['offre_name'])) {
    $user_id = $data['user_id'];
    $offre_id = $data['offre_id'];
    $offre_name = $data['offre_name'];

    // Check if user exists in the database
    $userQuery = "SELECT id FROM users WHERE id = ?";
    $stmt = $pdo->prepare($userQuery);
    $stmt->bind_param("s", $user_id);  // Binding user_id as string
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        error_log('User ID found: ' . $user_id);

        // Add the offer to the user's list
        $insertQuery = "INSERT INTO useroffers (user_id, offre_name) VALUES (?, ?)";
        $insertStmt = $pdo->prepare($insertQuery);
        $insertStmt->bind_param("ss", $user_id, $offre_name);  // Bind user_id and offre_name as strings


        if ($insertStmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Offer added successfully']);
        } else {
            error_log("MySQL Error: " . $insertStmt->error);
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $insertStmt->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
}

?>
