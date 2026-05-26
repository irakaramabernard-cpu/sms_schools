
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized. Please login first."]);
    exit;
}

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type");
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
function response_with_code($message, $code) {
    http_response_code($code);
    echo json_encode($message);
    exit;
}
if ($method === 'GET') {
    $sql = "SELECT * FROM students ORDER BY id DESC";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $students = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $students[] = $row;
        }
        echo json_encode($students);
    } else {
        response_with_code(["error" => "Failed to fetch student data."], 500);
    }
}
if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!empty($data['name']) && !empty($data['email']) && !empty($data['phone']) && !empty($data['course'])) {
        $name   = mysqli_real_escape_string($conn, $data['name']);
        $email  = mysqli_real_escape_string($conn, $data['email']);
        $phone  = mysqli_real_escape_string($conn, $data['phone']);
        $course = mysqli_real_escape_string($conn, $data['course']);

        $sql = "INSERT INTO students (name, email, phone, course) VALUES ('$name', '$email', '$phone', '$course')";
        
        if (mysqli_query($conn, $sql)) {
            echo json_encode(["success" => "Student added successfully!"]);
        } else {
            response_with_code(["error" => "Email might already exist or system query failed."], 400);
        }
    } else {
        response_with_code(["error" => "All fields are required."], 400);
    }
}
if ($method === 'DELETE') {
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];

        $sql = "DELETE FROM students WHERE id = $id";
        
        if (mysqli_query($conn, $sql)) {
            echo json_encode(["success" => "Student deleted successfully!"]);
        } else {
            response_with_code(["error" => "Could not complete delete operation."], 500);
        }
    } else {
        response_with_code(["error" => "Missing student ID."], 400);
    }
}
if ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!empty($data['id']) && !empty($data['name']) && !empty($data['email']) && !empty($data['phone']) && !empty($data['course'])) {
        $id     = (int)$data['id'];
        $name   = mysqli_real_escape_string($conn, $data['name']);
        $email  = mysqli_real_escape_string($conn, $data['email']);
        $phone  = mysqli_real_escape_string($conn, $data['phone']);
        $course = mysqli_real_escape_string($conn, $data['course']);

        $sql = "UPDATE students SET name='$name', email='$email', phone='$phone', course='$course' WHERE id=$id";
        
        if (mysqli_query($conn, $sql)) {
            echo json_encode(["success" => "Student updated successfully!"]);
        } else {
            response_with_code(["error" => "Failed to update student. Email might already exist."], 400);
        }
    } else {
        response_with_code(["error" => "All fields are required."], 400);
    }
}?>