<?php

ini_set("session.use_strict_mode", "1");
session_start();

require_once "../config/database.php";

// 1. Check authentication
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

// 2. Accept only POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

// 3. Validate CSRF token
if (
    !isset($_POST["csrf_token"], $_SESSION["csrf_token"]) ||
    !is_string($_POST["csrf_token"]) ||
    !is_string($_SESSION["csrf_token"]) ||
    !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
) {
    http_response_code(403);
    exit("Invalid security token. Please refresh and try again.");
}

// 4. Validate the logged-in user's ID
$user_id = filter_var(
    $_SESSION["user_id"],
    FILTER_VALIDATE_INT,
    ["options" => ["min_range" => 1]]
);

if ($user_id === false || $user_id === null) {
    http_response_code(401);
    exit("Invalid login session. Please log in again.");
}

// 5. Validate the article ID
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id < 1) {
    header("Location: index.php");
    exit();
}

// 6. Delete only an article owned by the logged-in user
$stmt = $conn->prepare(
    "DELETE FROM posts WHERE id = ? AND user_id = ?"
);

if (!$stmt) {
    error_log("Delete post prepare failed: " . $conn->error);
    http_response_code(500);
    exit("Unable to process the request.");
}

$stmt->bind_param("ii", $id, $user_id);

if (!$stmt->execute()) {
    error_log("Delete post execution failed: " . $stmt->error);
    $stmt->close();
    http_response_code(500);
    exit("Unable to delete the article.");
}

// 7. Check whether an article was deleted
$deleted = $stmt->affected_rows;

$stmt->close();
$conn->close();

// 8. Redirect with the appropriate status
if ($deleted > 0) {
    header("Location: index.php?deleted=1");
} else {
    // Article not found or not owned by the logged-in user
    header("Location: index.php?deleted=0");
}

exit();

?>