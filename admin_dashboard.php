<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "job_portal";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$response = [];

// Get total counts
$response['total_users'] = $conn->query("SELECT COUNT(*) AS count FROM users")->fetch_assoc()['count'];
$response['total_categories'] = $conn->query("SELECT COUNT(*) AS count FROM categories")->fetch_assoc()['count'];
$response['total_offers'] = $conn->query("SELECT COUNT(*) AS count FROM offres")->fetch_assoc()['count'];
$response['total_accepted_users'] = $conn->query("SELECT COUNT(*) AS count FROM acceptedusers WHERE accepted = 'yes'")->fetch_assoc()['count'];

// Fetch full data for frontend lists
$response['categories'] = $conn->query("SELECT * FROM categories")->fetch_all(MYSQLI_ASSOC);
$response['domaines'] = $conn->query("SELECT * FROM domaines")->fetch_all(MYSQLI_ASSOC);
$response['offres'] = $conn->query("SELECT * FROM offres")->fetch_all(MYSQLI_ASSOC);
$response['qcm'] = $conn->query("SELECT * FROM qcm")->fetch_all(MYSQLI_ASSOC);
$response['users'] = $conn->query("SELECT * FROM users")->fetch_all(MYSQLI_ASSOC);
$response['useroffers'] = $conn->query("SELECT * FROM useroffers")->fetch_all(MYSQLI_ASSOC);

echo json_encode($response);
?>