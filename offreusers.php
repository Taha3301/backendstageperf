<?php
header("Access-Control-Allow-Origin: *"); // or specify http://localhost:5173 for security
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    // Log errors to a file
// Enable error reporting
error_reporting(E_ALL); // Report all errors
ini_set('display_errors', 1); // Display errors on the browser (for development)

// Set error logging to the custom file
ini_set('log_errors', 1); // Enable logging
ini_set('error_log', 'F:/laragon/www/offre/errr.txt'); // Path to your error log file

    // Handle preflight OPTIONS request
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        exit;
    }
$host = "localhost";
$user = "root";  
$pass = "";
$db = "job_portal";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Connection failed: " . $conn->connect_error]));
}

// Handle preflight requests for CORS
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(204);
    exit;
}

// Fetch User Offers (GET)
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["action"]) && $_GET["action"] == "get_user_offers") {
    $username = $_GET['username'];

    // Get user_id from username
    $stmt = $conn->prepare("SELECT id FROM users WHERE name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $userResult = $stmt->get_result();
    
    if ($userRow = $userResult->fetch_assoc()) {
        $user_id = $userRow["id"];

        // Fetch offers for this user
        $stmt = $conn->prepare("SELECT id, offre_name FROM useroffers WHERE user_id = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        echo json_encode($result->fetch_all(MYSQLI_ASSOC));
    } else {
        echo json_encode(["success" => false, "error" => "User not found"]);
    }
    exit;
}

// Delete Offer (POST with JSON)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);

    if (isset($input["action"]) && $input["action"] == "delete_offer" && isset($input["offer_name"]) && isset($input["username"])) {
        $offer_name = $input["offer_name"];
        $username = $input["username"];  // The username from the front-end

        // Get user_id from username
        $stmt = $conn->prepare("SELECT id FROM users WHERE name = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $userResult = $stmt->get_result();

        if ($userRow = $userResult->fetch_assoc()) {
            $user_id = $userRow["id"];

            // Delete the offer by offer_name and user_id
            $stmt = $conn->prepare("DELETE FROM useroffers WHERE offre_name = ? AND user_id = ?");
            $stmt->bind_param("si", $offer_name, $user_id);

            if ($stmt->execute()) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["success" => false, "error" => $stmt->error]);
            }
        } else {
            echo json_encode(["success" => false, "error" => "User not found"]);
        }
        exit;
    }
}


$conn->close();
?>
