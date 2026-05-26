<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

session_start();

// Hardcoded credentials (in production, use a database)
$valid_users = [
    'admin' => password_hash('admin123', PASSWORD_DEFAULT),
    'user' => password_hash('user123', PASSWORD_DEFAULT)
];

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo json_encode(["error" => "Username and password are required."]);
        exit;
    }
    
    // Check if user exists and password matches
    if (isset($valid_users[$username]) && password_verify($password, $valid_users[$username])) {
        $_SESSION['user_id'] = $username;
        $_SESSION['username'] = $username;
        $_SESSION['login_time'] = time();
        
        echo json_encode(["success" => true, "message" => "Login successful"]);
    } else {
        http_response_code(401);
        echo json_encode(["error" => "Invalid username or password."]);
    }
} elseif ($method === 'GET') {
    // Check if user is logged in
    if (isset($_SESSION['user_id'])) {
        echo json_encode(["logged_in" => true, "username" => $_SESSION['username']]);
    } else {
        echo json_encode(["logged_in" => false]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
}
?>
