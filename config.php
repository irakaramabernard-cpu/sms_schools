<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "student_student";
$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed: " . mysqli_connect_error()]);
    exit;
}
mysqli_set_charset($conn, "utf8");