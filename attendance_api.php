<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized. Please login first."]);
    exit;
}

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

function response_with_code($message, $code) {
    http_response_code($code);
    echo json_encode($message);
    exit;
}

if ($method === 'GET') {
    if ($action === 'get_attendance') {
        $date = $_GET['date'] ?? date('Y-m-d');
        
        // Get all students
        $sql = "SELECT id, name, email, course FROM students ORDER BY id DESC";
        $result = mysqli_query($conn, $sql);
        
        $students = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $students[] = $row;
        }
        
        // Get attendance for this date
        $sql = "SELECT student_id, status FROM attendance WHERE attendance_date = '$date'";
        $result = mysqli_query($conn, $sql);
        
        $attendance = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $attendance[] = $row;
        }
        
        echo json_encode(["students" => $students, "attendance" => $attendance]);
    } elseif ($action === 'get_records') {
        $sql = "SELECT a.id, a.attendance_date, a.status, a.created_at, s.name as student_name FROM attendance a 
                JOIN students s ON a.student_id = s.id 
                ORDER BY a.created_at DESC, s.name ASC 
                LIMIT 100";
        $result = mysqli_query($conn, $sql);
        
        $records = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $records[] = $row;
        }
        
        echo json_encode($records);
    }
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (empty($data['date']) || empty($data['attendance'])) {
        response_with_code(["error" => "Date and attendance data are required."], 400);
    }
    
    $date = mysqli_real_escape_string($conn, $data['date']);
    
    // Delete existing records for this date
    $sql = "DELETE FROM attendance WHERE attendance_date = '$date'";
    mysqli_query($conn, $sql);
    
    // Insert new records
    $success = true;
    foreach ($data['attendance'] as $record) {
        $student_id = (int)$record['student_id'];
        $status = mysqli_real_escape_string($conn, $record['status']);
        
        $sql = "INSERT INTO attendance (student_id, attendance_date, status) VALUES ($student_id, '$date', '$status')";
        if (!mysqli_query($conn, $sql)) {
            $success = false;
            break;
        }
    }
    
    if ($success) {
        echo json_encode(["success" => true, "message" => "Attendance saved successfully!"]);
    } else {
        response_with_code(["error" => "Error saving attendance records."], 500);
    }
} else {
    response_with_code(["error" => "Method not allowed"], 405);
}
?>
