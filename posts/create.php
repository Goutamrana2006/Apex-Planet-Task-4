<?php

session_start();

require_once "../config/database.php";

// 1. Check authentication
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

// 2. Generate CSRF token
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$message = "";
$title = "";
$content = "";

// 3. Process form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Validate CSRF token
    if (
        !isset($_POST["csrf_token"], $_SESSION["csrf_token"]) ||
        !is_string($_POST["csrf_token"]) ||
        !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
    ) {
        http_response_code(403);
        exit("Invalid security token. Please refresh the page and try again.");
    }

    // Validate input types
    if (
        !isset($_POST["title"], $_POST["content"]) ||
        !is_string($_POST["title"]) ||
        !is_string($_POST["content"])
    ) {
        $message = "Invalid form input.";
    } else {

        $title = trim($_POST["title"]);
        $content = trim($_POST["content"]);

        // Validate required fields
        if ($title === "" || $content === "") {

            $message = "Please fill in all fields.";

        } elseif (strlen($title) > 255) {

            $message = "Title must not exceed 255 bytes.";

        } elseif (strlen($content) > 20000) {

            $message = "Content is too long. Please keep it under 20,000 bytes.";

        } else {

            // 4. Insert using a prepared statement
            $stmt = $conn->prepare(
                "INSERT INTO posts (title, content) VALUES (?, ?)"
            );

            $stmt->bind_param("ss", $title, $content);

            if ($stmt->execute()) {

                $stmt->close();
                $conn->close();

                // Redirect after successful creation
                header("Location: index.php");
                exit();

            } else {

                // Log technical details privately
                error_log("Create post failed: " . $stmt->error);
                $message = "Unable to create the post. Please try again.";
            }

            $stmt->close();
        }
    }
}

// Escape output before displaying it
function e($value) {
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        "UTF-8"
    );
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Post - ApexPlanet Task 4</title>

    <link rel="stylesheet" href="../style.css">
</head>

<body>

<div class="container">

    <h1>Create New Post</h1>

    <?php if ($message !== ""): ?>
        <p role="alert"><?php echo e($message); ?></p>
    <?php endif; ?>

    <form method="POST" action="create.php">

        <!-- CSRF protection -->
        <input
            type="hidden"
            name="csrf_token"
            value="<?php echo e($_SESSION["csrf_token"]); ?>"
        >

        <label for="title">Title</label>

        <input
            type="text"
            id="title"
            name="title"
            maxlength="255"
            value="<?php echo e($title); ?>"
            required
        >

        <label for="content">Content</label>

        <textarea
            id="content"
            name="content"
            rows="8"
            maxlength="20000"
            required
        ><?php echo e($content); ?></textarea>

        <button type="submit">Create Post</button>

    </form>

    <br>

    <a href="index.php">Back to Posts</a>

</div>

</body>
</html>

<?php
$conn->close();
?>