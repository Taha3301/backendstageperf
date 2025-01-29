<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json"); // Set JSON response type

$host = "localhost";
$user = "root";  // Change if you have a password
$pass = "";
$db = "job_portal";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Database connection failed"]);
    exit;
}

// Handle POST requests
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"])) {
    $action = $_POST["action"];

    // Add Category
    if ($action === "add_category") {
        $nom = trim($_POST["nom"] ?? "");
        if ($nom === "") {
            echo json_encode(["success" => false, "error" => "Category name cannot be empty"]);
            exit;
        }
        $id = uniqid(); // Generate a unique ID
        $sql = "INSERT INTO categories (id, nom) VALUES ('$id', '$nom')";
        echo json_encode(["success" => $conn->query($sql)]);
        exit;
    }

    // Add Domain
    if ($action === "add_domain") {
        $nom = trim($_POST["nom"] ?? "");
        $categorie_id = $_POST["categorie_id"] ?? "";

        if ($nom === "" || $categorie_id === "") {
            echo json_encode(["success" => false, "error" => "All fields are required"]);
            exit;
        }

        $sql = "INSERT INTO domaines (nom, categorie_id) VALUES ('$nom', '$categorie_id')";
        echo json_encode(["success" => $conn->query($sql)]);
        exit;
    }

    // Delete Category
    if ($action === "delete_category") {
        $id = $_POST["id"] ?? "";
        if ($id === "") {
            echo json_encode(["success" => false, "error" => "Invalid category ID"]);
            exit;
        }

        // Ensure categories are deleted before their linked domains
        $conn->query("DELETE FROM domaines WHERE categorie_id='$id'");
        $sql = "DELETE FROM categories WHERE id='$id'";
        echo json_encode(["success" => $conn->query($sql)]);
        exit;
    }

    // Delete Domain
    if ($action === "delete_domain") {
        $id = $_POST["id"] ?? "";
        if ($id === "") {
            echo json_encode(["success" => false, "error" => "Invalid domain ID"]);
            exit;
        }

        $sql = "DELETE FROM domaines WHERE id='$id'";
        echo json_encode(["success" => $conn->query($sql)]);
        exit;
    }
}

// Handle GET requests
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["action"])) {
    $action = $_GET["action"];

    // Fetch Categories
    if ($action === "get_categories") {
        $result = $conn->query("SELECT * FROM categories");
        $categories = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($categories);
        exit;
    }

    // Fetch Domains with Category Names
    if ($action === "get_domains") {
        $result = $conn->query("SELECT d.id, d.nom, c.nom AS category_name FROM domaines d 
                                JOIN categories c ON d.categorie_id = c.id");
        $domains = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($domains);
        exit;
    }
}

// If no valid action is found, return an error
echo json_encode(["success" => false, "error" => "Invalid request"]);
?>
