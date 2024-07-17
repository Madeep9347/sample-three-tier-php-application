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

// Check if JSON data is received and parse it
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Check if JSON decoding was successful
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Invalid JSON data"]);
    exit;
}

// Check if 'name' and 'email' keys exist in the received data
if (!isset($data['name'], $data['email'])) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Name and email must be provided"]);
    exit;
}

// Sanitize inputs to prevent SQL injection
$name = htmlspecialchars($data['name']);
$email = htmlspecialchars($data['email']);

// Validate inputs (optional step based on your application's requirements)

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

// Prepare SQL statement with parameterized query to prevent SQL injection
$sql = "INSERT INTO students (name, email) VALUES (?, ?)";
$stmt = $mysqli->prepare($sql);

// Bind parameters
$stmt->bind_param("ss", $name, $email);

// Execute statement
if ($stmt->execute()) {
    echo json_encode(["message" => "Student added successfully"]);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => "Error: " . $mysqli->error]);
}

// Close statement and database connection
$stmt->close();
$mysqli->close();
?>
