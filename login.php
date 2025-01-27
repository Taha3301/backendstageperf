<?php
// Allow cross-origin requests from your Vue app
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// Handle preflight request (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database connection settings
$host = 'localhost';
$dbname = 'job_portal';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $requestMethod = $_SERVER["REQUEST_METHOD"];

    if ($requestMethod === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);

        if (isset($input['email']) && isset($input['password'])) {
            $email = $input['email'];
            $password = $input['password'];

            $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && $user['password'] === $password) {
                echo json_encode(["status" => "success", "message" => "Login successful", "username" => $user['name']]);
            } else {
                echo json_encode(["status" => "error", "message" => "Identifiants incorrects !"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Champs requis manquants"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Méthode non autorisée"]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Erreur de connexion: " . $e->getMessage()]);
}
?>
