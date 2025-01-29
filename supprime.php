<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

// CORS headers
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// If the request is OPTIONS, just return an empty response with status 200
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "job_portal"; // Change to your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    // Return JSON error response instead of HTML
    echo json_encode([ "status" => "error", "message" => "Database connection failed: " . $conn->connect_error ]);
    http_response_code(500); // Internal Server Error
    exit;
}

// Function to delete accepted user from the acceptedusers table
function deleteAcceptedUser($conn, $userId, $offreName) {
    $sql = "DELETE FROM acceptedusers WHERE user_id = ? AND offre_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $userId, $offreName);

    if ($stmt->execute()) {
        return ["status" => "success", "message" => "User acceptance removed successfully"];
    } else {
        return ["status" => "error", "message" => "Failed to remove user acceptance: " . $stmt->error];
    }
}

// Handle POST request to delete user acceptance
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (isset($data->user_id) && isset($data->offre_name)) {
        $userId = $data->user_id;
        $offreName = $data->offre_name;

        // Call the function to delete the accepted user
        $result = deleteAcceptedUser($conn, $userId, $offreName);
        echo json_encode($result);
    } else {
        echo json_encode([ "status" => "error", "message" => "User ID and Offer Name are required" ]);
    }
}

$conn->close();
?>
