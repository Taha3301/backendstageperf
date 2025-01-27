<?php 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight (OPTIONS) requests
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

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    $errorMessage = "Database connection failed: " . $conn->connect_error;
    echo json_encode(["message" => $errorMessage]);
    error_log($errorMessage, 3, "F:/laragon/www/offre/errr.txt");
    exit();
}

// Set character set for proper handling of special characters
$conn->set_charset("utf8");

// Get HTTP method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Fetch all offers
        $sql = "SELECT offres.id, offres.titre, offres.description, offres.salaire, domaines.nom AS domaine 
                FROM offres 
                JOIN domaines ON offres.domaine_id = domaines.id";
        $result = $conn->query($sql);

        if ($result) {
            $offres = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($offres);
        } else {
            http_response_code(500);
            $errorMessage = "Failed to fetch offers: " . $conn->error;
            echo json_encode(["message" => $errorMessage]);
            error_log($errorMessage, 3, "F:/laragon/www/offre/errr.txt");
        }
        break;

    case 'POST':
        // Add a new offer
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['titre']) || empty($data['description']) || !isset($data['salaire']) || !isset($data['domaine_id'])) {
            $errorMessage = "Missing required fields: ";
            $missingFields = [];
            if (empty($data['titre'])) $missingFields[] = "titre";
            if (empty($data['description'])) $missingFields[] = "description";
            if (!isset($data['salaire'])) $missingFields[] = "salaire";
            if (!isset($data['domaine_id'])) $missingFields[] = "domaine_id";
            $errorMessage .= implode(", ", $missingFields);
            http_response_code(400);
            echo json_encode(["message" => $errorMessage]);
            error_log($errorMessage, 3, "F:/laragon/www/offre/errr.txt");
            exit();
        }
        

        $titre = htmlspecialchars(strip_tags($data['titre']));
        $description = htmlspecialchars(strip_tags($data['description']));
        $salaire = filter_var($data['salaire'], FILTER_VALIDATE_INT);
        $domaine_id = filter_var($data['domaine_id'], FILTER_VALIDATE_INT);

        if ($salaire === false || $domaine_id === false) {
            http_response_code(400);
            $errorMessage = "Invalid input values";
            echo json_encode(["message" => $errorMessage]);
            error_log($errorMessage, 3, "F:/laragon/www/offre/errr.txt");
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO offres (titre, description, salaire, domaine_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $titre, $description, $salaire, $domaine_id);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["message" => "Offer added successfully"]);
        } else {
            http_response_code(500);
            $errorMessage = "Error adding offer: " . $stmt->error;
            echo json_encode(["message" => $errorMessage]);
            error_log($errorMessage, 3, "F:/laragon/www/offre/errr.txt");
        }
        $stmt->close();
        break;

    case 'PUT':
        // Update an offer
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['id']) || empty($data['titre']) || empty($data['description']) || !isset($data['salaire']) || !isset($data['domaine_id'])) {
            http_response_code(400);
            $errorMessage = "Missing required fields";
            echo json_encode(["message" => $errorMessage]);
            error_log($errorMessage, 3, "F:/laragon/www/offre/errr.txt");
            exit();
        }

        $id = filter_var($data['id'], FILTER_VALIDATE_INT);
        $titre = htmlspecialchars(strip_tags($data['titre']));
        $description = htmlspecialchars(strip_tags($data['description']));
        $salaire = filter_var($data['salaire'], FILTER_VALIDATE_INT);
        $domaine_id = filter_var($data['domaine_id'], FILTER_VALIDATE_INT);

        if ($id === false || $salaire === false || $domaine_id === false) {
            http_response_code(400);
            $errorMessage = "Invalid input values";
            echo json_encode(["message" => $errorMessage]);
            error_log($errorMessage, 3, "F:/laragon/www/offre/errr.txt");
            exit();
        }

        $stmt = $conn->prepare("UPDATE offres SET titre = ?, description = ?, salaire = ?, domaine_id = ? WHERE id = ?");
        $stmt->bind_param("ssiii", $titre, $description, $salaire, $domaine_id, $id);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Offer updated successfully"]);
        } else {
            http_response_code(500);
            $errorMessage = "Error updating offer: " . $stmt->error;
            echo json_encode(["message" => $errorMessage]);
            error_log($errorMessage, 3, "F:/laragon/www/offre/errr.txt");
        }
        $stmt->close();
        break;

    case 'DELETE':
        // Delete an offer
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['id'])) {
            http_response_code(400);
            $errorMessage = "Missing offer ID";
            echo json_encode(["message" => $errorMessage]);
            error_log($errorMessage, 3, "F:/laragon/www/offre/errr.txt");
            exit();
        }

        $id = filter_var($data['id'], FILTER_VALIDATE_INT);

        if ($id === false) {
            http_response_code(400);
            $errorMessage = "Invalid offer ID";
            echo json_encode(["message" => $errorMessage]);
            error_log($errorMessage, 3, "F:/laragon/www/offre/errr.txt");
            exit();
        }

        $stmt = $conn->prepare("DELETE FROM offres WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Offer deleted successfully"]);
        } else {
            http_response_code(500);
            $errorMessage = "Error deleting offer: " . $stmt->error;
            echo json_encode(["message" => $errorMessage]);
            error_log($errorMessage, 3, "F:/laragon/www/offre/errr.txt");
        }
        $stmt->close();
        break;

    default:
        // Invalid request method
        http_response_code(405);
        $errorMessage = "Method not allowed";
        echo json_encode(["message" => $errorMessage]);
        error_log($errorMessage, 3, "F:/laragon/www/offre/errr.txt");
        break;
}

$conn->close();
?>
