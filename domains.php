<?php
header("Access-Control-Allow-Origin: *"); // Allow all origins, or specify 'http://localhost:5173'
  header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
  header("Access-Control-Allow-Headers: Content-Type, Authorization");
  
// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'job_portal';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if a category ID is provided
    $categoryId = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;

    if ($categoryId) {
        // Fetch domains for the given category
        $stmt = $pdo->prepare("SELECT id, nom FROM domaines WHERE categorie_id = :categoryId");
        $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        // Fetch all domains
        $stmt = $pdo->query("SELECT id, nom FROM domaines");
    }

    $domains = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return JSON response
    echo json_encode($domains);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
}
?>