<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$servername = "localhost";  // Default for Laragon
$username = "root";         // Default for Laragon
$password = "";             // Default (empty password)
$database = "job_portal"; // Replace with your database name

// Connect to MySQL database
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed"]));
}

// Get JSON data from the request body
$data = json_decode(file_get_contents("php://input"));

if (isset($data->name) && isset($data->email) && isset($data->password)) {
    $name = $conn->real_escape_string($data->name);
    $email = $conn->real_escape_string($data->email);
    $password = $data->password; // Store the password directly without hashing

    // Check if the email already exists
    $check_query = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($check_query);

    if ($result->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email déjà utilisé"]);
    } else {
        // Insert new user
        $id = uniqid();
        $sql = "INSERT INTO users (id, name, email, password) VALUES ('$id', '$name', '$email', '$password')";
        
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["status" => "success", "message" => "Inscription réussie"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Erreur lors de l'inscription"]);
        }
    }
} else {
    echo json_encode(["status" => "error", "message" => "Données invalides"]);
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn->close();

?>
