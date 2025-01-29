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
$dbname = "job_portal"; // Ensure this matches your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    // Return JSON error response instead of HTML
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed: " . $conn->connect_error
    ]);
    http_response_code(500); // Internal Server Error
    exit;
}

// Function to fetch users and their job offers
function fetchUsersAndOffers($conn) {
    $sql = "
    SELECT users.id, users.name, users.email, useroffers.offre_name 
    FROM users
    LEFT JOIN useroffers ON users.id = useroffers.user_id
    LEFT JOIN acceptedusers ON users.id = acceptedusers.user_id AND useroffers.offre_name = acceptedusers.offre_name
    WHERE acceptedusers.accepted = 'yes';
";



    $result = $conn->query($sql);
    $users = [];

    if ($result === false) {
        // Return JSON error response if the query fails
        echo json_encode([
            "status" => "error",
            "message" => "SQL query failed: " . $conn->error
        ]);
        http_response_code(500); // Internal Server Error
        exit;
    }

    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    return $users;
}

// Handle GET request to fetch users and their offers
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $users = fetchUsersAndOffers($conn);
    echo json_encode($users);
}

// Handle POST request to accept a user (this could be for adding a record to another table or marking a user as accepted)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (isset($data->user_id) && isset($data->offre_name)) {
        $userId = $data->user_id;
        $offreName = $data->offre_name;
        $accepted = isset($data->accepted) ? $data->accepted : 0;

        // Check if the user has already been accepted for this offer
        $checkSql = "SELECT * FROM acceptedusers WHERE user_id = ? AND offre_name = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("ss", $userId, $offreName);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            // If the user is already accepted for this offer
            echo json_encode(["status" => "error", "message" => "User has already been accepted for this offer"]);
        } else {
            // If not already accepted, insert the user into the acceptedusers table
            $sql = "INSERT INTO acceptedusers (user_id, offre_name, accepted) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $userId, $offreName, $accepted);

            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "User accepted successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to accept user: " . $stmt->error]);
            }
        }
    } else {
        echo json_encode(["status" => "error", "message" => "User ID and Offer Name are required"]);
    }
}


$conn->close();
?>
