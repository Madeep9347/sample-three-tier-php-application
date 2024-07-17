<?php
// Enable CORS
header('Access-Control-Allow-Origin: http://<frontend-publicip>');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Database connection details
$host = "<database-ip>";
$username = "<username>";
$password = "<mysql password>";
$database = "<database name>";

// Create MySQLi object
$mysqli = new mysqli($host, $username, $password, $database);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Query to get students
$sql = "SELECT name, email FROM students";
$result = $mysqli->query($sql);

$students = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

// Output students as JSON
echo json_encode($students);

// Close database connection
$mysqli->close();
?>
