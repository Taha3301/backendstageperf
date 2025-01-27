<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Set the content type to JSON
header('Content-Type: application/json');

// Database credentials
$servername = "localhost";
$username = "root"; // Use your database username
$password = ""; // Use your database password
$dbname = "job_portal"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch job offers and related categories and domains
$sql = "
    SELECT 
        offres.id, 
        offres.titre, 
        offres.description, 
        offres.salaire, 
        domaines.id AS domaine_id, 
        domaines.nom AS domaine_name,
        categories.nom AS categorie_name
    FROM offres
    JOIN domaines ON offres.domaine_id = domaines.id
    JOIN categories ON domaines.categorie_id = categories.id
"; 

$result = $conn->query($sql);

// Check if any offers are found
if ($result->num_rows > 0) {
    $offers = [];

    // Fetch data for each offer
    while ($row = $result->fetch_assoc()) {
        $offers[] = [
            'id' => $row['id'],
            'titre' => $row['titre'],
            'description' => $row['description'],
            'salaire' => $row['salaire'],
            'domaine' => [
                'id' => $row['domaine_id'],
                'nom' => $row['domaine_name']
            ],
            'categorie' => [
                'nom' => $row['categorie_name']
            ]
        ];
    }

    // Organize offers by categories
    $categories = [];
    foreach ($offers as $offer) {
        $categorie_name = $offer['categorie']['nom'];
        if (!isset($categories[$categorie_name])) {
            $categories[$categorie_name] = [];
        }

        $categories[$categorie_name][] = $offer;
    }

    // Return categories with their respective offers as JSON
    echo json_encode($categories);
} else {
    echo json_encode(["message" => "No offers found"]);
}

// Close the database connection
$conn->close();
?>
