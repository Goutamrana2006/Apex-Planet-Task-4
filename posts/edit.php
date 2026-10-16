<?php

session_start();

require_once "../config/database.php";

// 1. Authentication check
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

// 2. Validate post ID
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id < 1) {
    header("Location: index.php");
    exit();
}

// 3. Generate CSRF token
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$message = "";
$title = "";
$content = "";

// 4. Fetch existing post
$stmt = $conn->prepare(
    "SELECT id, title, content FROM posts WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    header("Location: index.php");
    exit();
}

$post = $result->fetch_assoc();

$stmt->close();

// Display existing post values
$title = $post["title"];
$content = $post["content"];

// 5. Process update request
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

            // 6. Update post using prepared statement
            $stmt = $conn->prepare(
                "UPDATE posts
                 SET title = ?, content = ?
                 WHERE id = ?"
            );

            $stmt->bind_param(
                "ssi",
                $title,
                $content,
                $id
            );

            if ($stmt->execute()) {

                $stmt->close();
                $conn->close();

                // Redirect after successful update
                header("Location: index.php");
                exit();

            } else {

                // Log technical details privately
                error_log("Update post failed: " . $stmt->error);

                $message = "Unable to update the post. Please try again.";
            }

            $stmt->close();
        }
    }
}

// 7. Escape HTML output
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

    <title>Edit Post - ApexPlanet Task 4</title>

    <link rel="stylesheet" href="../style.css">

</head>

<body>

<div class="container">

    <h1>Edit Post</h1>

    <?php if ($message !== ""): ?>

        <p role="alert">
            <?php echo e($message); ?>
        </p>

    <?php endif; ?>

    <form method="POST" action="edit.php?id=<?php echo (int) $id; ?>">

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

        <button type="submit">
            Update Post
        </button>

    </form>

    <br>

    <a href="index.php">Back to Posts</a>

</div>

</body>

</html>

<?php
$conn->close();
?>
