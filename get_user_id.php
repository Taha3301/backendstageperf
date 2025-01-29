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

// Receive the POST data
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['username'])) {
    $username = $data['username'];

    // Fetch user ID based on the username
    $userQuery = "SELECT id FROM users WHERE name = ?";
    $stmt = $pdo->prepare($userQuery);
    $stmt->bind_param("s", $username);  // Bind the username parameter
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        echo json_encode(['success' => true, 'user_id' => $user['id']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
}
?>
