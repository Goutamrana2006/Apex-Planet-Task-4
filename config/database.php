<?php

mysqli_report(MYSQLI_REPORT_OFF);

$host = "localhost";
$username = "root";
$password = "";
$database = "blog";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    http_response_code(500);
    exit("A server error occurred. Please try again later.");
}

if (!$conn->set_charset("utf8mb4")) {
    error_log("Failed to set database character encoding.");
    http_response_code(500);
    exit("A server error occurred. Please try again later.");
}

?>